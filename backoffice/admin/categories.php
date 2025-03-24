<?php
require_once '../includes/auth.php';
checkAuth();

$error = '';
$success = '';

// Kategori Ekleme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    if (checkCSRFToken($_POST['csrf_token'])) {
        $name = clean($_POST['name']);
        $slug = createSlug($name);
        
        $stmt = $db->prepare('INSERT INTO categories (name, slug) VALUES (:name, :slug)');
        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
        $stmt->bindValue(':slug', $slug, SQLITE3_TEXT);
        
        try {
            $stmt->execute();
            $success = "Kategori başarıyla eklendi!";
        } catch (Exception $e) {
            $error = "Bu kategori adı zaten kullanılıyor!";
        }
    }
}

// Kategori Silme
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    $stmt = $db->prepare('DELETE FROM categories WHERE id = :id');
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $stmt->execute();
    
    header('Location: categories');
    exit;
}

// Kategorileri Listele
$categories = [];
$query = 'SELECT c.*, p.name as parent_name 
          FROM categories c 
          LEFT JOIN categories p ON c.parent_id = p.id 
          ORDER BY c.name ASC';
$result = $db->query($query);
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $categories[] = $row;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Kategoriler</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Ana İçerik -->
        <div class="flex-1">
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h1 class="text-2xl font-bold text-gray-900">Kategoriler</h1>
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

                <!-- Kategori Ekleme Formu -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="p-4 sm:p-6">
                        <form method="POST" class="space-y-4">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 pb-2">
                                    Yeni Kategori Ekle
                                </label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="text" name="name" id="name" required
                                        class="flex-1 min-w-0 block w-full px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <button type="submit" name="add_category"
                                        class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Ekle
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Kategoriler Listesi -->
                <div class="bg-white shadow rounded-lg">
                    <div class="p-4 sm:p-6">
                        <div class="flex flex-col">
                            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Kategori Adı
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Oluşturulma Tarihi
                                                    </th>
                                                    <th scope="col" class="relative px-6 py-3">
                                                        <span class="sr-only">İşlemler</span>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <?php foreach ($categories as $category): ?>
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        <?php echo clean($category['name']); ?>
                                                        <?php if ($category['parent_name']): ?>
                                                            <span class="text-gray-500 text-xs ml-2">
                                                                (<?php echo clean($category['parent_name']); ?>)
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        <?php echo date('d.m.Y H:i', strtotime($category['created_at'])); ?>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <a href="category-edit?id=<?php echo $category['id']; ?>" 
                                                           class="text-indigo-600 hover:text-indigo-900 mr-3">Düzenle</a>
                                                        <a href="?delete=<?php echo $category['id']; ?>" 
                                                           onclick="return confirm('Bu kategoriyi silmek istediğinizden emin misiniz?')"
                                                           class="text-red-600 hover:text-red-900">Sil</a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html> 