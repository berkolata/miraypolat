<?php
require_once 'includes/auth.php';

if (isset($_SESSION['user_id'])) {
    header('Location: admin/index');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    if (checkCSRFToken($_POST['csrf_token'])) {
        $username = clean($_POST['username']);
        $password = $_POST['password'];
        
        $result = loginUser($username, $password);
        if ($result === true) {
            header('Location: admin/index');
            exit;
        } else {
            $error = $result;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="relative h-full">
    <div class="absolute top-0 left-0 bottom-0 right-0 w-full h-screen z-40" style="background:url('../uploads/pattern.png'); opacity:0.5;""></div>
    <div class="relative z-50 min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-3xl font-bold text-blue-900">
                Hoşgeldin Admine
            </h2>
        </div>

        <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-2 px-4 shadow sm:rounded-lg sm:px-8">
                <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?php echo $error; ?></span>
                    </div>
                <?php endif; ?>

                <form class="space-y-6 mb-10" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">
                            Kullanıcı Adı veya E-posta
                        </label>
                        <div class="mt-1">
                            <input id="username" name="username" type="text" required 
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Şifre
                        </label>
                        <div class="mt-1">
                            <input id="password" name="password" type="password" required 
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <button type="submit" name="login" 
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Giriş Yap
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 