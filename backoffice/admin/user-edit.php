<?php
require_once '../includes/auth.php';
checkAuth();

// Sadece admin kullanıcıları düzenleyebilir
if ($_SESSION['role'] !== 'admin') {
    header('Location: index');
    exit;
}

$error = '';
$success = '';

// ID kontrolü
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: users');
    exit;
}

$user_id = (int)$_GET['id'];

// Kullanıcıyı getir
$stmt = $db->prepare('SELECT * FROM users WHERE id = :id');
$stmt->bindValue(':id', $user_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);

if (!$user) {
    header('Location: users');
    exit;
}

// Kullanıcı güncelleme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    if (checkCSRFToken($_POST['csrf_token'])) {
        $username = clean($_POST['username']);
        $email = clean($_POST['email']);
        $role = clean($_POST['role']);
        $password = $_POST['password'];
        
        // Şifre değişikliği yapılacak mı?
        if (!empty($password)) {
            if (strlen($password) < 6) {
                $error = "Şifre en az 6 karakter olmalıdır!";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $db->prepare('UPDATE users SET username = :username, email = :email, password = :password, role = :role WHERE id = :id');
                $stmt->bindValue(':password', $hashedPassword, SQLITE3_TEXT);
            }
        } else {
            $stmt = $db->prepare('UPDATE users SET username = :username, email = :email, role = :role WHERE id = :id');
        }
        
        if (!$error) {
            $stmt->bindValue(':username', $username, SQLITE3_TEXT);
            $stmt->bindValue(':email', $email, SQLITE3_TEXT);
            $stmt->bindValue(':role', $role, SQLITE3_TEXT);
            $stmt->bindValue(':id', $user_id, SQLITE3_INTEGER);
            
            try {
                if ($stmt->execute()) {
                    $success = "Kullanıcı başarıyla güncellendi!";
                    // Güncel veriyi yeniden yükle
                    $stmt = $db->prepare('SELECT * FROM users WHERE id = :id');
                    $stmt->bindValue(':id', $user_id, SQLITE3_INTEGER);
                    $result = $stmt->execute();
                    $user = $result->fetchArray(SQLITE3_ASSOC);
                } else {
                    $error = "Güncelleme sırasında bir hata oluştu!";
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
    <title>Admin Panel - Kullanıcı Düzenle</title>
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
                    <h1 class="text-2xl font-bold text-gray-900">Kullanıcı Düzenle</h1>
                    <a href="users" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
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

                <!-- Kullanıcı Düzenleme Formu -->
                <div class="bg-white shadow rounded-lg">
                    <div class="p-4 sm:p-6">
                        <form method="POST" class="space-y-6">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700">
                                    Kullanıcı Adı
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="username" id="username" required
                                        value="<?php echo clean($user['username']); ?>"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">
                                    E-posta
                                </label>
                                <div class="mt-1">
                                    <input type="email" name="email" id="email" required
                                        value="<?php echo clean($user['email']); ?>"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    Şifre
                                </label>
                                <div class="mt-1">
                                    <input type="password" name="password" id="password"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Şifreyi değiştirmek istemiyorsanız boş bırakın. Yeni şifre en az 6 karakter olmalıdır.</p>
                            </div>

                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700">
                                    Rol
                                </label>
                                <div class="mt-1">
                                    <select name="role" id="role" required
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="subscriber" <?php echo ($user['role'] == 'subscriber') ? 'selected' : ''; ?>>Abone</option>
                                        <option value="author" <?php echo ($user['role'] == 'author') ? 'selected' : ''; ?>>Yazar</option>
                                        <option value="editor" <?php echo ($user['role'] == 'editor') ? 'selected' : ''; ?>>Editör</option>
                                        <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Yönetici</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <a href="users" 
                                   class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    İptal
                                </a>
                                <button type="submit" name="update_user" 
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