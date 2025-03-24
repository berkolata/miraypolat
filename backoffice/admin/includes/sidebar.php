<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<div class="bg-gray-800 w-64 flex-shrink-0">
    <div class="flex items-center justify-center h-16 bg-gray-900">
        <span class="text-white font-bold text-lg">Admin Panel</span>
    </div>
    <nav class="mt-5 flex flex-col h-[calc(100vh-4rem)] justify-between">
        <!-- Üst Menü -->
        <div>
            <a href="index" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white <?php echo $current_page == 'index' ? 'bg-gray-700 text-white' : ''; ?>">
                <span>Dashboard</span>
            </a>
            <a href="pages" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white <?php echo $current_page == 'pages' ? 'bg-gray-700 text-white' : ''; ?>">
                <span>Sayfalar</span>
            </a>
            <a href="posts" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white <?php echo $current_page == 'posts' ? 'bg-gray-700 text-white' : ''; ?>">
                <span>Yazılar</span>
            </a>
            <a href="categories" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white <?php echo $current_page == 'categories' ? 'bg-gray-700 text-white' : ''; ?>">
                <span>Kategoriler</span>
            </a>
            <a href="media" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white <?php echo $current_page == 'media' ? 'bg-gray-700 text-white' : ''; ?>">
                <span>Medya</span>
            </a>
            <a href="menus" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white <?php echo $current_page == 'menus' ? 'bg-gray-700 text-white' : ''; ?>">
                <span>Menüler</span>
            </a>
            <a href="users" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white <?php echo $current_page == 'users' ? 'bg-gray-700 text-white' : ''; ?>">
                <span>Kullanıcılar</span>
            </a>
            <a href="comments" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white <?php echo $current_page == 'comments' ? 'bg-gray-700 text-white' : ''; ?>">
                <span>Yorumlar</span>
            </a>
            <a href="settings" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white <?php echo $current_page == 'settings' ? 'bg-gray-700 text-white' : ''; ?>">
                <span>Ayarlar</span>
            </a>
        </div>

        <!-- Alt Menü -->
        <div class="border-t border-gray-700 py-4">
            <div class="px-6 py-2 text-gray-400 text-sm">
                Hoşgeldin, <?php echo clean($_SESSION['username']); ?>
            </div>
            <a href="profile" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white <?php echo $current_page == 'profile' ? 'bg-gray-700 text-white' : ''; ?>">
                <span>Profilim</span>
            </a>
            <a href="logout" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white">
                <span>Güvenli Çıkış</span>
            </a>
        </div>
    </nav>
</div> 