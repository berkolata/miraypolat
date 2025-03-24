<?php
require_once '../includes/auth.php';
checkAuth();

$error = '';
$success = '';

// Kullanıcı bilgilerini getir
$stmt = $db->prepare('SELECT * FROM users WHERE id = :id');
$stmt->bindValue(':id', $_SESSION['user_id'], SQLITE3_INTEGER);
$result = $stmt->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);

// Profil güncelleme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    if (checkCSRFToken($_POST['csrf_token'])) {
        $username = clean($_POST['username']);
        $email = clean($_POST['email']);
        $password = $_POST['password'];
        
        // Şifre değişikliği yapılacak mı?
        if (!empty($password)) {
            if (strlen($password) < 6) {
                $error = "Şifre en az 6 karakter olmalıdır!";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $db->prepare('UPDATE users SET username = :username, email = :email, password = :password WHERE id = :id');
                $stmt->bindValue(':password', $hashedPassword, SQLITE3_TEXT);
            }
        } else {
            $stmt = $db->prepare('UPDATE users SET username = :username, email = :email WHERE id = :id');
        }
        
        if (!$error) {
            $stmt->bindValue(':username', $username, SQLITE3_TEXT);
            $stmt->bindValue(':email', $email, SQLITE3_TEXT);
            $stmt->bindValue(':id', $_SESSION['user_id'], SQLITE3_INTEGER);
            
            try {
                if ($stmt->execute()) {
                    $_SESSION['username'] = $username; // Session'ı güncelle
                    $success = "Profil başarıyla güncellendi!";
                }
            } catch (Exception $e) {
                $error = "Bu kullanıcı adı veya e-posta zaten kullanımda!";
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
    <title>Admin Panel - Profilim</title>
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
                    <h1 class="text-2xl font-bold text-gray-900">Profilim</h1>
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

                <div class="bg-white shadow rounded-lg">
                    <div class="p-6">
                        <form method="POST" class="space-y-6">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Kullanıcı Adı
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="username" required
                                           value="<?php echo clean($user['username']); ?>"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    E-posta
                                </label>
                                <div class="mt-1">
                                    <input type="email" name="email" required
                                           value="<?php echo clean($user['email']); ?>"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Yeni Şifre
                                </label>
                                <div class="mt-1">
                                    <input type="password" name="password"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Şifreyi değiştirmek istemiyorsanız boş bırakın. Yeni şifre en az 6 karakter olmalıdır.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Rol
                                </label>
                                <div class="mt-1">
                                    <input type="text" value="<?php 
                                        echo match($user['role']) {
                                            'admin' => 'Yönetici',
                                            'editor' => 'Editör',
                                            'author' => 'Yazar',
                                            'subscriber' => 'Abone',
                                        }; 
                                    ?>" disabled
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 sm:text-sm">
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" name="update_profile" 
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
</body>
</html> 