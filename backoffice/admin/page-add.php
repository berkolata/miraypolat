<?php
ini_set('log_errors', 1);
//ini_set('error_log', dirname(__DIR__) . '/error.log');
error_log("Page Add başlatıldı");

require_once '../includes/auth.php';
checkAuth();

$error = '';
$success = '';

// Sayfa Ekleme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_page'])) {
    error_log("POST işlemi başladı");
    error_log("POST verisi: " . print_r($_POST, true));

    if (checkCSRFToken($_POST['csrf_token'])) {
        error_log("CSRF token doğrulandı");
        
        // Verileri logla
        $title = clean($_POST['title']);
        $content = $_POST['content'] ?? '';
        error_log("Başlık: " . $title);
        error_log("İçerik uzunluğu: " . strlen($content));
        
        // Veritabanı işlemi öncesi
        error_log("Veritabanı işlemi başlıyor");
        
        $meta_title = clean($_POST['meta_title'] ?? '');
        $meta_description = clean($_POST['meta_description'] ?? '');
        $focus_keyword = clean($_POST['focus_keyword'] ?? '');
        $status = clean($_POST['status'] ?? 'draft');
        
        // Boş içerik kontrolü
        if (empty($title)) {
            $error = "Başlık alanı zorunludur!";
        } else {
            // Slug kontrolü
            $slug = !empty($_POST['slug']) ? createSlug($_POST['slug']) : createSlug($title);
            
            // Slug benzersizlik kontrolü
            $stmt = $db->prepare('SELECT COUNT(*) as count FROM pages WHERE slug = :slug');
            $stmt->bindValue(':slug', $slug, SQLITE3_TEXT);
            $result = $stmt->execute();
            $row = $result->fetchArray(SQLITE3_ASSOC);
            
            if ($row['count'] > 0) {
                $error = "Bu SEO URL'si zaten kullanımda!";
            } else {
                try {
                    // Transaction başlat
                    $db->exec('BEGIN EXCLUSIVE TRANSACTION');
                    
                    $stmt = $db->prepare('INSERT INTO pages (
                        title, 
                        meta_title,
                        slug, 
                        content,
                        meta_description,
                        focus_keyword,
                        status,
                        author_id
                    ) VALUES (
                        :title,
                        :meta_title,
                        :slug,
                        :content,
                        :meta_description,
                        :focus_keyword,
                        :status,
                        :author_id
                    )');
                    
                    $stmt->bindValue(':title', $title, SQLITE3_TEXT);
                    $stmt->bindValue(':meta_title', $meta_title, SQLITE3_TEXT);
                    $stmt->bindValue(':slug', $slug, SQLITE3_TEXT);
                    $stmt->bindValue(':content', $content, SQLITE3_TEXT);
                    $stmt->bindValue(':meta_description', $meta_description, SQLITE3_TEXT);
                    $stmt->bindValue(':focus_keyword', $focus_keyword, SQLITE3_TEXT);
                    $stmt->bindValue(':status', $status, SQLITE3_TEXT);
                    $stmt->bindValue(':author_id', $_SESSION['user_id'], SQLITE3_INTEGER);
                    
                    if ($stmt->execute()) {
                        $db->exec('COMMIT');
                        error_log("Sayfa başarıyla eklendi");
                        header('Location: pages');
                        exit;
                    } else {
                        $db->exec('ROLLBACK');
                        error_log("HATA: İşlem geri alındı");
                        $error = "Sayfa eklenirken bir hata oluştu: " . $db->lastErrorMsg();
                    }
                } catch (Exception $e) {
                    $db->exec('ROLLBACK');
                    error_log("HATA: İşlem geri alındı - " . $e->getMessage());
                    $error = "Veritabanı hatası: " . $e->getMessage();
                }
            }
        }
    } else {
        error_log("CSRF token doğrulaması başarısız");
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Yeni Sayfa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <?php include 'includes/sidebar.php'; ?>

        <div class="flex-1">
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4">
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-gray-900">Yeni Sayfa Ekle</h1>
                        <div class="flex space-x-3">
                            <a href="pages" class="px-4 py-2 border rounded-md text-gray-700 bg-white hover:bg-gray-50">İptal</a>
                            <button type="submit" form="pageForm" name="add_page" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Kaydet
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

                <form id="pageForm" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="bg-white shadow rounded-lg p-6 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Başlık</label>
                            <input type="text" name="title" required class="mt-1 block w-full rounded-md p-2 border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">SEO URL</label>
                            <input type="text" name="slug" class="mt-1 block w-full rounded-md p-2 border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-sm text-gray-500">Boş bırakırsanız başlıktan otomatik oluşturulur</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">İçerik</label>
                            <div id="editor" class="mt-1" style="height: 400px;"></div>
                            <input type="hidden" name="content" id="content">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Odak Anahtar Kelime</label>
                            <input type="text" name="focus_keyword" class="mt-1 block w-full rounded-md p-2 border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Meta Başlık</label>
                            <input type="text" name="meta_title" class="mt-1 block w-full rounded-md p-2 border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Meta Açıklama</label>
                            <textarea name="meta_description" rows="3" class="mt-1 block w-full rounded-md p-2 border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Durum</label>
                            <select name="status" required class="mt-1 block w-full rounded-md p-2 border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="draft">Taslak</option>
                                <option value="pending">İnceleniyor</option>
                                <option value="published">Yayında</option>
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
    let slugManuallyChanged = false;

    slugInput.addEventListener('input', function() {
        slugManuallyChanged = true;
    });

    titleInput.addEventListener('input', function() {
        if (!slugManuallyChanged) {
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