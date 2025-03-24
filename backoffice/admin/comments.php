<?php
require_once '../includes/auth.php';
checkAuth();

$error = '';
$success = '';

// Yorum Silme
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    $stmt = $db->prepare('DELETE FROM comments WHERE id = :id');
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $stmt->execute();
    
    header('Location: comments');
    exit;
}

// Yorum Durumu Güncelleme
if (isset($_GET['status']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $status = clean($_GET['status']);
    
    if (in_array($status, ['approved', 'pending', 'spam'])) {
        $stmt = $db->prepare('UPDATE comments SET status = :status WHERE id = :id');
        $stmt->bindValue(':status', $status, SQLITE3_TEXT);
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->execute();
    }
    
    header('Location: comments');
    exit;
}

// Yorumları Listele
$comments = [];
$query = 'SELECT c.*, p.title as post_title, u.username as user_name 
          FROM comments c 
          LEFT JOIN posts p ON c.post_id = p.id 
          LEFT JOIN users u ON c.user_id = u.id 
          ORDER BY c.created_at DESC';
$result = $db->query($query);
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $comments[] = $row;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Yorumlar</title>
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
                    <h1 class="text-2xl font-bold text-gray-900">Yorumlar</h1>
                </div>
            </header>

            <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <!-- Bildirimler -->
                <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?php echo $error; ?></span>
                    </div>
                <?php endif; ?>

                <!-- Yorumlar Listesi -->
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
                                                        Yorum
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Yazar
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Yazı
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
                                                <?php foreach ($comments as $comment): ?>
                                                <tr>
                                                    <td class="px-6 py-4">
                                                        <div class="text-sm text-gray-900">
                                                            <?php echo clean($comment['comment']); ?>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm text-gray-900">
                                                            <?php echo clean($comment['user_name']); ?>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm text-gray-900">
                                                            <a href="post-edit?id=<?php echo $comment['post_id']; ?>" class="text-indigo-600 hover:text-indigo-900">
                                                                <?php echo clean($comment['post_title']); ?>
                                                            </a>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            <?php 
                                                            echo match($comment['status']) {
                                                                'approved' => 'bg-green-100 text-green-800',
                                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                                'spam' => 'bg-red-100 text-red-800',
                                                            };
                                                            ?>">
                                                            <?php 
                                                            echo match($comment['status']) {
                                                                'approved' => 'Onaylı',
                                                                'pending' => 'Beklemede',
                                                                'spam' => 'Spam',
                                                            };
                                                            ?>
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        <?php echo date('d.m.Y H:i', strtotime($comment['created_at'])); ?>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <div class="flex justify-end space-x-2">
                                                            <?php if ($comment['status'] != 'approved'): ?>
                                                                <a href="?status=approved&id=<?php echo $comment['id']; ?>" 
                                                                   class="text-green-600 hover:text-green-900">Onayla</a>
                                                            <?php endif; ?>
                                                            
                                                            <?php if ($comment['status'] != 'pending'): ?>
                                                                <a href="?status=pending&id=<?php echo $comment['id']; ?>" 
                                                                   class="text-yellow-600 hover:text-yellow-900">Beklet</a>
                                                            <?php endif; ?>
                                                            
                                                            <?php if ($comment['status'] != 'spam'): ?>
                                                                <a href="?status=spam&id=<?php echo $comment['id']; ?>" 
                                                                   class="text-red-600 hover:text-red-900">Spam</a>
                                                            <?php endif; ?>
                                                            
                                                            <a href="?delete=<?php echo $comment['id']; ?>" 
                                                               onclick="return confirm('Bu yorumu silmek istediğinizden emin misiniz?')"
                                                               class="text-red-600 hover:text-red-900 ml-3">Sil</a>
                                                        </div>
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