<?php
require_once '../includes/auth.php';
checkAuth();

$error = '';
$success = '';

// Sadece admin kullanıcıları yönetebilir
if ($_SESSION['role'] !== 'admin') {
    header('Location: index');
    exit;
}

// Kullanıcı Silme
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Admin kendini silemesin
    if ($id === (int)$_SESSION['user_id']) {
        $error = "Kendi hesabınızı silemezsiniz!";
    } else {
        $stmt = $db->prepare('DELETE FROM users WHERE id = :id AND role != "admin"');
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->execute();
        
        header('Location: users');
        exit;
    }
}

// Kullanıcıları Listele
$users = [];
$query = 'SELECT * FROM users ORDER BY created_at DESC';
$result = $db->query($query);
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $users[] = $row;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Kullanıcılar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>


        <!-- Ana İçerik -->
        <div class="flex-1">
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-gray-900">Kullanıcılar</h1>
                    <a href="user-add" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Yeni Kullanıcı Ekle
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

                <!-- Kullanıcılar Listesi -->
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
                                                        Kullanıcı Adı
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        E-posta
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Rol
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Kayıt Tarihi
                                                    </th>
                                                    <th scope="col" class="relative px-6 py-3">
                                                        <span class="sr-only">İşlemler</span>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <?php foreach ($users as $user): ?>
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            <?php echo clean($user['username']); ?>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm text-gray-500">
                                                            <?php echo clean($user['email']); ?>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            <?php 
                                                            echo match($user['role']) {
                                                                'admin' => 'bg-red-100 text-red-800',
                                                                'editor' => 'bg-blue-100 text-blue-800',
                                                                'author' => 'bg-green-100 text-green-800',
                                                                'subscriber' => 'bg-gray-100 text-gray-800',
                                                            };
                                                            ?>">
                                                            <?php 
                                                            echo match($user['role']) {
                                                                'admin' => 'Yönetici',
                                                                'editor' => 'Editör',
                                                                'author' => 'Yazar',
                                                                'subscriber' => 'Abone',
                                                            };
                                                            ?>
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        <?php echo date('d.m.Y H:i', strtotime($user['created_at'])); ?>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                            <a href="user-edit?id=<?php echo $user['id']; ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">Düzenle</a>
                                                            <a href="?delete=<?php echo $user['id']; ?>" 
                                                               onclick="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')"
                                                               class="text-red-600 hover:text-red-900">Sil</a>
                                                        <?php else: ?>
                                                            <span class="text-gray-400">Aktif Kullanıcı</span>
                                                        <?php endif; ?>
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