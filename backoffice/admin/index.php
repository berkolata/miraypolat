<?php
require_once '../includes/auth.php';
checkAuth(); // Oturum kontrolü

// İstatistikleri al
$stats = [
    'users' => $db->querySingle('SELECT COUNT(*) FROM users'),
    'posts' => $db->querySingle('SELECT COUNT(*) FROM posts'),
    'categories' => $db->querySingle('SELECT COUNT(*) FROM categories'),
    'comments' => $db->querySingle('SELECT COUNT(*) FROM comments')
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Dashboard</title>
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
                    <h1 class="text-2xl font-bold text-gray-900">
                        Hoş Geldin, <?php echo clean($_SESSION['username']); ?>
                    </h1>
                </div>
            </header>

            <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Kullanıcılar -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Kullanıcılar
                                        </dt>
                                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                            <?php echo $stats['users']; ?>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Yazılar -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Yazılar
                                        </dt>
                                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                            <?php echo $stats['posts']; ?>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kategoriler -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Kategoriler
                                        </dt>
                                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                            <?php echo $stats['categories']; ?>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Yorumlar -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Yorumlar
                                        </dt>
                                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                            <?php echo $stats['comments']; ?>
                                        </dd>
                                    </dl>
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