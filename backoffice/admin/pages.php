<?php
require_once '../includes/auth.php';
checkAuth();

$error = '';
$success = '';

// Sayfa Silme
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    $stmt = $db->prepare('DELETE FROM pages WHERE id = :id');
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $stmt->execute();
    
    header('Location: pages');
    exit;
}

// Sayfaları Listele
try {
    $pages = [];
    $query = 'SELECT p.*, u.username as author_name 
              FROM pages p 
              LEFT JOIN users u ON p.author_id = u.id 
              ORDER BY p.created_at DESC';

    error_log("Pages Query: " . $query);
    
    // Statement hazırla
    $stmt = $db->prepare($query);
    $result = $stmt->execute();
    
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $pages[] = $row;
        error_log("Loaded Page ID: " . $row['id'] . " - Title: " . $row['title']);
    }
    
    error_log("Total Pages Loaded: " . count($pages));
    
} catch (Exception $e) {
    error_log("HATA: Sayfalar listelenirken bir hata oluştu - " . $e->getMessage());
    $error = "Sayfalar listelenirken bir hata oluştu";
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Sayfalar</title>
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
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-gray-900">Sayfalar</h1>
                        <a href="page-add" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Yeni Sayfa Ekle
                        </a>
                    </div>
                </div>
            </header>

            <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
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

                <!-- Sayfa Listesi -->
                <div class="bg-white shadow rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Başlık
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Yazar
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Durum
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tarih
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">İşlemler</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($pages as $page): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo clean($page['title']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            /<?php echo clean($page['slug']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo clean($page['author_name']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php 
                                            echo match($page['status']) {
                                                'published' => 'bg-green-100 text-green-800',
                                                'draft' => 'bg-gray-100 text-gray-800',
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                            };
                                            ?>">
                                            <?php 
                                            echo match($page['status']) {
                                                'published' => 'Yayında',
                                                'draft' => 'Taslak',
                                                'pending' => 'Beklemede',
                                            };
                                            ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('d.m.Y H:i', strtotime($page['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="page-edit?id=<?php echo $page['id']; ?>" 
                                           class="text-indigo-600 hover:text-indigo-900 mr-3">Düzenle</a>
                                        <a href="?delete=<?php echo $page['id']; ?>" 
                                           onclick="return confirm('Bu sayfayı silmek istediğinizden emin misiniz?')"
                                           class="text-red-600 hover:text-red-900">Sil</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html> 