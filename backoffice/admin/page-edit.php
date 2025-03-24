<?php
ini_set('log_errors', 1);
//ini_set('error_log', dirname(__DIR__) . '/error.log');
error_log("Page Edit başlatıldı - ID: " . ($_GET['id'] ?? 'Boş'));

require_once '../includes/auth.php';
checkAuth();

$error = '';
$success = '';

// ID kontrolü
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: pages');
    exit;
}

$page_id = (int)$_GET['id'];

// Sayfayı getir
$stmt = $db->prepare('SELECT * FROM pages WHERE id = :id');
error_log("Sayfa yükleme sorgusu hazırlandı");
$stmt->bindValue(':id', $page_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$page = $result->fetchArray(SQLITE3_ASSOC);

if (!$page) {
    error_log("Sayfa bulunamadı - ID: " . $page_id);
    header('Location: pages');
    exit;
} else {
    error_log("Sayfa yüklendi - Başlık: " . ($page['title'] ?? 'Boş'));
}

// Sayfa Güncelleme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_page'])) {
    error_log("POST işlemi başladı");
    error_log("POST verisi: " . print_r($_POST, true));

    if (checkCSRFToken($_POST['csrf_token'])) {
        error_log("CSRF token doğrulandı");
        
        // Verileri logla
        $title = clean($_POST['title']);
        $content = $_POST['content'] ?? '';
        error_log("Başlık: " . $title);
        error_log("İçerik uzunluğu: " . strlen($content));
        
        $meta_title = clean($_POST['meta_title'] ?? '');
        $meta_description = clean($_POST['meta_description'] ?? '');
        $focus_keyword = clean($_POST['focus_keyword'] ?? '');
        
        // Boş içerik kontrolü
        if (empty($title)) {
            $error = "Başlık alanı zorunludur!";
        } else {
            // Slug kontrolü
            $slug = !empty($_POST['slug']) ? createSlug($_POST['slug']) : createSlug($title);
            
            try {
                // Slug benzersizlik kontrolü (kendi ID'si hariç)
                $stmt = $db->prepare('SELECT COUNT(*) as count FROM pages WHERE slug = :slug AND id != :id');
                $stmt->bindValue(':slug', $slug, SQLITE3_TEXT);
                $stmt->bindValue(':id', $page_id, SQLITE3_INTEGER);
                $result = $stmt->execute();
                $row = $result->fetchArray(SQLITE3_ASSOC);
                
                if ($row['count'] > 0) {
                    $error = "Bu SEO URL'si zaten kullanımda!";
                } else {
                    $stmt = $db->prepare('UPDATE pages SET 
                        title = :title,
                        slug = :slug,
                        content = :content,
                        meta_title = :meta_title,
                        meta_description = :meta_description,
                        focus_keyword = :focus_keyword,
                        status = :status
                        WHERE id = :id');
                    
                    $stmt->bindValue(':title', $title, SQLITE3_TEXT);
                    $stmt->bindValue(':slug', $slug, SQLITE3_TEXT);
                    $stmt->bindValue(':content', $content, SQLITE3_TEXT);
                    $stmt->bindValue(':meta_title', $meta_title, SQLITE3_TEXT);
                    $stmt->bindValue(':meta_description', $meta_description, SQLITE3_TEXT);
                    $stmt->bindValue(':focus_keyword', $focus_keyword, SQLITE3_TEXT);
                    $stmt->bindValue(':status', clean($_POST['status'] ?? 'draft'), SQLITE3_TEXT);
                    $stmt->bindValue(':id', $page_id, SQLITE3_INTEGER);
                    
                    // Sayfa güncelleme işlemi
                    try {
                        $db->exec('BEGIN EXCLUSIVE TRANSACTION');
                        
                        if ($stmt->execute()) {
                            $db->exec('COMMIT');
                            
                            // Güncel verileri al
                            $stmt = $db->prepare('SELECT * FROM pages WHERE id = :id');
                            $stmt->bindValue(':id', $page_id, SQLITE3_INTEGER);
                            $result = $stmt->execute();
                            $page = $result->fetchArray(SQLITE3_ASSOC);
                            
                            if ($page) {
                                $success = "Sayfa başarıyla güncellendi!";
                            } else {
                                $error = "Güncel veriler alınamadı";
                            }
                        } else {
                            $db->exec('ROLLBACK');
                            $error = "Sayfa güncellenirken bir hata oluştu: " . $db->lastErrorMsg();
                        }
                    } catch (Exception $e) {
                        $db->exec('ROLLBACK');
                        $error = "Veritabanı hatası: " . $e->getMessage();
                    }
                }
            } catch (Exception $e) {
                $error = "Veritabanı hatası: " . $e->getMessage();
                error_log("HATA (Exception): " . $error);
                error_log("SQL Error: " . $db->lastErrorMsg());
            }
        }
    } else {
        error_log("CSRF token doğrulaması başarısız");
    }
}

// Hata ayıklama
if ($error) {
    error_log("Page Edit Error: " . $error);
    error_log("SQL Error: " . $db->lastErrorMsg());
}

// SEO Analiz Modülü
$item = $page; // veya $post

// Veritabanı bağlantı kontrolü
if (!$db) {
    error_log("Veritabanı bağlantısı başarısız: " . SQLite3::lastErrorMsg());
} else {
    error_log("Veritabanı bağlantısı başarılı");
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Sayfa Düzenle</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Ana İçerik -->
        <div class="flex-1">
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4">
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-gray-900">Sayfa Düzenle</h1>
                        <div class="flex space-x-3">
                            <a href="pages" class="px-4 py-2 border rounded-md text-gray-700 bg-white hover:bg-gray-50">İptal</a>
                            <button type="submit" form="pageForm" name="update_page" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Değişiklikleri Kaydet
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <main class="max-w-7xl mx-auto py-6 px-4">
                <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <form id="pageForm" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="bg-white shadow rounded-lg p-6 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Başlık</label>
                            <input type="text" name="title" required value="<?php echo clean($page['title']); ?>" 
                                   class="mt-1 block w-full rounded-md p-2 border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">SEO URL</label>
                            <input type="text" name="slug" value="<?php echo clean($page['slug']); ?>"
                                   class="mt-1 block w-full rounded-md p-2 border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-sm text-gray-500">Boş bırakırsanız başlıktan otomatik oluşturulur</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">İçerik</label>
                            <div id="editor" class="mt-1" style="height: 400px;"><?php echo $page['content']; ?></div>
                            <input type="hidden" name="content" id="content">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Odak Anahtar Kelime</label>
                            <input type="text" name="focus_keyword" value="<?php echo clean($page['focus_keyword']); ?>"
                                   class="mt-1 block w-full rounded-md p-2 border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Meta Başlık</label>
                            <input type="text" name="meta_title" value="<?php echo clean($page['meta_title']); ?>"
                                   class="mt-1 block w-full rounded-md p-2 border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Meta Açıklama</label>
                            <textarea name="meta_description" rows="3" 
                                      class="mt-1 block w-full rounded-md p-2 border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"><?php echo clean($page['meta_description']); ?></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Durum</label>
                            <select name="status" required class="mt-1 block w-full rounded-md p-2 border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="draft" <?php echo $page['status'] == 'draft' ? 'selected' : ''; ?>>Taslak</option>
                                <option value="pending" <?php echo $page['status'] == 'pending' ? 'selected' : ''; ?>>İnceleniyor</option>
                                <option value="published" <?php echo $page['status'] == 'published' ? 'selected' : ''; ?>>Yayında</option>
                            </select>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
    var quill = new Quill('#editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'align': [] }],
                ['link'],
                ['clean']
            ]
        }
    });

    // Form gönderilmeden önce editör içeriğini gizli alana aktar
    document.getElementById('pageForm').addEventListener('submit', function() {
        document.getElementById('content').value = quill.root.innerHTML;
    });

    // Başlık-Slug otomatik oluşturma
    const titleInput = document.querySelector('input[name="title"]');
    const slugInput = document.querySelector('input[name="slug"]');
    const originalSlug = slugInput.value;
    let slugManuallyChanged = false;

    slugInput.addEventListener('input', function() {
        slugManuallyChanged = true;
    });

    titleInput.addEventListener('input', function() {
        if (!slugManuallyChanged && slugInput.value === originalSlug) {
            slugInput.value = createSlug(this.value);
        }
    });

    function createSlug(str) {
        str = str.toLowerCase();
        str = str.replace(/[ıİğĞüÜşŞöÖçÇ]/g, function(letter) {
            return letter
                .replace(/[ıİ]/g, 'i')
                .replace(/[ğĞ]/g, 'g')
                .replace(/[üÜ]/g, 'u')
                .replace(/[şŞ]/g, 's')
                .replace(/[öÖ]/g, 'o')
                .replace(/[çÇ]/g, 'c');
        });
        str = str.replace(/[^a-z0-9]/g, '-');
        str = str.replace(/-+/g, '-');
        str = str.replace(/^-|-$/g, '');
        return str;
    }
    </script>
</body>
</html> 