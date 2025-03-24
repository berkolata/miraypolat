<?php
require_once '../includes/auth.php';
checkAuth();

$error = '';
$success = '';

// ID kontrolü
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: categories');
    exit;
}

$category_id = (int)$_GET['id'];

// Kategoriyi getir
$stmt = $db->prepare('SELECT * FROM categories WHERE id = :id');
$stmt->bindValue(':id', $category_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$category = $result->fetchArray(SQLITE3_ASSOC);

if (!$category) {
    header('Location: categories');
    exit;
}

// Tüm kategorileri getir (parent seçimi için)
$categories = [];
$query = 'SELECT * FROM categories WHERE id != :id ORDER BY name ASC';
$stmt = $db->prepare($query);
$stmt->bindValue(':id', $category_id, SQLITE3_INTEGER);
$result = $stmt->execute();
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $categories[] = $row;
}

// Kategori güncelleme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_category'])) {
    if (checkCSRFToken($_POST['csrf_token'])) {
        $name = clean($_POST['name']);
        $description = $_POST['description']; // HTML içerik
        $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        $slug = createSlug($name);
        
        // Döngüsel parent kontrolü
        $isCircular = false;
        if ($parent_id) {
            $checkId = $parent_id;
            while ($checkId) {
                if ($checkId == $category_id) {
                    $isCircular = true;
                    break;
                }
                $stmt = $db->prepare('SELECT parent_id FROM categories WHERE id = :id');
                $stmt->bindValue(':id', $checkId, SQLITE3_INTEGER);
                $result = $stmt->execute();
                $row = $result->fetchArray(SQLITE3_ASSOC);
                $checkId = $row ? $row['parent_id'] : null;
            }
        }
        
        if ($isCircular) {
            $error = "Döngüsel kategori ilişkisi oluşturulamaz!";
        } else {
            $stmt = $db->prepare('UPDATE categories SET name = :name, slug = :slug, description = :description, parent_id = :parent_id WHERE id = :id');
            $stmt->bindValue(':name', $name, SQLITE3_TEXT);
            $stmt->bindValue(':slug', $slug, SQLITE3_TEXT);
            $stmt->bindValue(':description', $description, SQLITE3_TEXT);
            $stmt->bindValue(':parent_id', $parent_id, $parent_id ? SQLITE3_INTEGER : SQLITE3_NULL);
            $stmt->bindValue(':id', $category_id, SQLITE3_INTEGER);
            
            try {
                if ($stmt->execute()) {
                    $success = "Kategori başarıyla güncellendi!";
                    // Güncel veriyi yeniden yükle
                    $stmt = $db->prepare('SELECT * FROM categories WHERE id = :id');
                    $stmt->bindValue(':id', $category_id, SQLITE3_INTEGER);
                    $result = $stmt->execute();
                    $category = $result->fetchArray(SQLITE3_ASSOC);
                } else {
                    $error = "Güncelleme sırasında bir hata oluştu!";
                }
            } catch (Exception $e) {
                $error = "Bu kategori adı zaten kullanılıyor!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Kategori Düzenle</title>
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
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-gray-900">Kategori Düzenle</h1>
                    <a href="categories" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Geri Dön
                    </a>
                </div>
            </header>

            <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <!-- Bildirimler -->
                <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?php echo $error; ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?php echo $success; ?></span>
                    </div>
                <?php endif; ?>

                <!-- Kategori Düzenleme Formu -->
                <div class="bg-white shadow rounded-lg">
                    <div class="p-4 sm:p-6">
                        <form method="POST" class="space-y-6">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    Kategori Adı
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="name" id="name" required
                                        value="<?php echo clean($category['name']); ?>"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>

                            <div>
                                <label for="parent_id" class="block text-sm font-medium text-gray-700">
                                    Üst Kategori
                                </label>
                                <div class="mt-1">
                                    <select name="parent_id" id="parent_id"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Ana Kategori</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>" 
                                                <?php echo ($cat['id'] == $category['parent_id']) ? 'selected' : ''; ?>>
                                                <?php echo clean($cat['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="editor" class="block text-sm font-medium text-gray-700">
                                    Açıklama
                                </label>
                                <div class="mt-1">
                                    <div id="editor" style="height: 200px;"><?php echo $category['description']; ?></div>
                                    <input type="hidden" name="description" id="description">
                                </div>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <a href="categories" 
                                   class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    İptal
                                </a>
                                <button type="submit" name="update_category" 
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Değişiklikleri Kaydet
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
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
        document.querySelector('form').addEventListener('submit', function() {
            document.getElementById('description').value = quill.root.innerHTML;
        });
    </script>
</body>
</html> 