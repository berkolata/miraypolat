<?php
require_once '../includes/auth.php';
checkAuth();

$error = '';
$success = '';

// Yeni Menü Oluşturma
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_menu'])) {
    if (checkCSRFToken($_POST['csrf_token'])) {
        $menu_name = clean($_POST['menu_name']);
        
        $stmt = $db->prepare('INSERT INTO menus (name) VALUES (:name)');
        $stmt->bindValue(':name', $menu_name, SQLITE3_TEXT);
        
        if ($stmt->execute()) {
            $success = "Menü başarıyla oluşturuldu!";
        }
    }
}

// Menü Öğesi Ekleme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_menu_item'])) {
    if (checkCSRFToken($_POST['csrf_token'])) {
        $menu_id = (int)$_POST['menu_id'];
        $type = clean($_POST['type']);
        $title = clean($_POST['title']);
        $url = clean($_POST['url']);
        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : null;
        
        $stmt = $db->prepare('INSERT INTO menu_items (menu_id, title, url, type, item_id) 
                             VALUES (:menu_id, :title, :url, :type, :item_id)');
        
        $stmt->bindValue(':menu_id', $menu_id, SQLITE3_INTEGER);
        $stmt->bindValue(':title', $title, SQLITE3_TEXT);
        $stmt->bindValue(':url', $url, SQLITE3_TEXT);
        $stmt->bindValue(':type', $type, SQLITE3_TEXT);
        $stmt->bindValue(':item_id', $item_id, $item_id ? SQLITE3_INTEGER : SQLITE3_NULL);
        
        if ($stmt->execute()) {
            $success = "Menü öğesi başarıyla eklendi!";
        }
    }
}

// Menüleri Getir
$menus = [];
$result = $db->query('SELECT * FROM menus ORDER BY created_at DESC');
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $menus[] = $row;
}

// Yazıları Getir
$posts = [];
$result = $db->query('SELECT id, title FROM posts WHERE status = "published" ORDER BY title ASC');
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $posts[] = $row;
}

// Kategorileri Getir
$categories = [];
$result = $db->query('SELECT id, name FROM categories ORDER BY name ASC');
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $categories[] = $row;
}

// Sayfaları Getir
$pages = [];
$result = $db->query('SELECT id, title FROM pages WHERE status = "published" ORDER BY title ASC');
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $pages[] = $row;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Menüler</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Ana İçerik -->
        <div class="flex-1">
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h1 class="text-2xl font-bold text-gray-900">Menüler</h1>
                </div>
            </header>

            <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <!-- Bildirimler -->
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

                <div class="flex gap-6">
                    <!-- Sol Taraf - Menü Oluşturma ve İçerik Seçenekleri -->
                    <div class="w-1/3 space-y-6">
                        <!-- Yeni Menü Oluşturma -->
                        <div class="bg-white shadow rounded-lg p-6">
                            <h2 class="text-lg font-medium mb-4">Yeni Menü Oluştur</h2>
                            <form method="POST" class="space-y-4">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Menü Adı</label>
                                    <input type="text" name="menu_name" required
                                           class="mt-1 block w-full rounded-md p-2 border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                
                                <button type="submit" name="create_menu" 
                                        class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                    Menü Oluştur
                                </button>
                            </form>
                        </div>

                        <!-- Yazılar -->
                        <div class="bg-white shadow rounded-lg p-6">
                            <h2 class="text-lg font-medium mb-4">Yazılar</h2>
                            <div class="max-h-60 overflow-y-auto space-y-2">
                                <?php foreach ($posts as $post): ?>
                                    <div class="flex items-center">
                                        <input type="checkbox" value="<?php echo $post['id']; ?>" 
                                               class="post-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 p-2 border border-gray-300 rounded">
                                        <label class="ml-2 text-sm text-gray-700">
                                            <?php echo clean($post['title']); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button onclick="addSelectedPosts()" 
                                    class="mt-4 w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200">
                                Seçili Yazıları Ekle
                            </button>
                        </div>

                        <!-- Kategoriler -->
                        <div class="bg-white shadow rounded-lg p-6">
                            <h2 class="text-lg font-medium mb-4">Kategoriler</h2>
                            <div class="max-h-60 overflow-y-auto space-y-2">
                                <?php foreach ($categories as $category): ?>
                                    <div class="flex items-center">
                                        <input type="checkbox" value="<?php echo $category['id']; ?>" 
                                               class="category-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 p-2 border border-gray-300 rounded">
                                        <label class="ml-2 text-sm text-gray-700">
                                            <?php echo clean($category['name']); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button onclick="addSelectedCategories()" 
                                    class="mt-4 w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200">
                                Seçili Kategorileri Ekle
                            </button>
                        </div>

                        <!-- Sayfalar -->
                        <div class="bg-white shadow rounded-lg p-6">
                            <h2 class="text-lg font-medium mb-4">Sayfalar</h2>
                            <div class="max-h-60 overflow-y-auto space-y-2">
                                <?php foreach ($pages as $page): ?>
                                    <div class="flex items-center">
                                        <input type="checkbox" value="<?php echo $page['id']; ?>" 
                                               class="page-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 p-2 border border-gray-300 rounded">
                                        <label class="ml-2 text-sm text-gray-700">
                                            <?php echo clean($page['title']); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button onclick="addSelectedPages()" 
                                    class="mt-4 w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200">
                                Seçili Sayfaları Ekle
                            </button>
                        </div>

                        <!-- Özel Bağlantı -->
                        <div class="bg-white shadow rounded-lg p-6">
                            <h2 class="text-lg font-medium mb-4">Özel Bağlantı</h2>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Bağlantı Metni</label>
                                    <input type="text" id="custom_title" 
                                           class="mt-1 block w-full rounded-md p-2 border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">URL</label>
                                    <input type="url" id="custom_url" 
                                           class="mt-1 block w-full rounded-md p-2 border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <button onclick="addCustomLink()" 
                                        class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200">
                                    Bağlantı Ekle
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sağ Taraf - Menü Yapısı -->
                    <div class="w-2/3">
                        <div class="bg-white shadow rounded-lg p-6">
                            <div class="flex items-center justify-between mb-6">
                                <select id="menu_selector" onchange="loadMenuItems(this.value)" 
                                        class="rounded-md p-2 border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Menü Seçin</option>
                                    <?php foreach ($menus as $menu): ?>
                                        <option value="<?php echo $menu['id']; ?>"><?php echo clean($menu['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                
                                <button onclick="saveMenuStructure()" 
                                        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                    Menüyü Kaydet
                                </button>
                            </div>

                            <!-- Menü Öğeleri -->
                            <div id="menu_items" class="space-y-2">
                                <!-- Menü öğeleri buraya JavaScript ile eklenecek -->
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
    // Menü öğelerini sürükle-bırak ile sıralama
    new Sortable(document.getElementById('menu_items'), {
        animation: 150,
        ghostClass: 'bg-gray-100'
    });

    // Seçili yazıları menüye ekle
    function addSelectedPosts() {
        const checkboxes = document.querySelectorAll('.post-checkbox:checked');
        const menuId = document.getElementById('menu_selector').value;
        
        if (!menuId) {
            alert('Lütfen önce bir menü seçin!');
            return;
        }

        checkboxes.forEach(checkbox => {
            const title = checkbox.nextElementSibling.textContent.trim();
            addMenuItem({
                type: 'post',
                item_id: checkbox.value,
                title: title,
                menu_id: menuId
            });
            checkbox.checked = false;
        });
    }

    // Seçili kategorileri menüye ekle
    function addSelectedCategories() {
        const checkboxes = document.querySelectorAll('.category-checkbox:checked');
        const menuId = document.getElementById('menu_selector').value;
        
        if (!menuId) {
            alert('Lütfen önce bir menü seçin!');
            return;
        }

        checkboxes.forEach(checkbox => {
            const title = checkbox.nextElementSibling.textContent.trim();
            addMenuItem({
                type: 'category',
                item_id: checkbox.value,
                title: title,
                menu_id: menuId
            });
            checkbox.checked = false;
        });
    }

    // Seçili sayfaları menüye ekle
    function addSelectedPages() {
        const checkboxes = document.querySelectorAll('.page-checkbox:checked');
        const menuId = document.getElementById('menu_selector').value;
        
        if (!menuId) {
            alert('Lütfen önce bir menü seçin!');
            return;
        }

        checkboxes.forEach(checkbox => {
            const title = checkbox.nextElementSibling.textContent.trim();
            addMenuItem({
                type: 'page',
                item_id: checkbox.value,
                title: title,
                menu_id: menuId
            });
            checkbox.checked = false;
        });
    }

    // Özel bağlantı ekle
    function addCustomLink() {
        const menuId = document.getElementById('menu_selector').value;
        const title = document.getElementById('custom_title').value;
        const url = document.getElementById('custom_url').value;
        
        if (!menuId) {
            alert('Lütfen önce bir menü seçin!');
            return;
        }

        if (!title || !url) {
            alert('Lütfen başlık ve URL alanlarını doldurun!');
            return;
        }

        addMenuItem({
            type: 'custom',
            title: title,
            url: url,
            menu_id: menuId
        });

        // Formu temizle
        document.getElementById('custom_title').value = '';
        document.getElementById('custom_url').value = '';
    }

    // Menü öğesi ekle
    function addMenuItem(data) {
        const menuItems = document.getElementById('menu_items');
        const item = document.createElement('div');
        item.className = 'bg-gray-50 p-4 rounded-lg shadow-sm';
        item.setAttribute('data-type', data.type);
        item.setAttribute('data-id', data.item_id || '');
        item.setAttribute('data-url', data.url || '');
        
        item.innerHTML = `
            <div class="flex items-center justify-between">
                <span class="font-medium">${data.title}</span>
                <button onclick="this.parentElement.parentElement.remove()" 
                        class="text-red-600 hover:text-red-700">Sil</button>
            </div>
        `;
        
        menuItems.appendChild(item);
    }

    // Menü yapısını kaydet
    function saveMenuStructure() {
        const menuId = document.getElementById('menu_selector').value;
        if (!menuId) {
            alert('Lütfen bir menü seçin!');
            return;
        }

        const items = Array.from(document.getElementById('menu_items').children).map((item, index) => ({
            type: item.dataset.type,
            item_id: item.dataset.id,
            title: item.querySelector('span').textContent,
            url: item.dataset.url,
            order_number: index
        }));

        // AJAX ile kaydet
        fetch('ajax/save-menu.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                menu_id: menuId,
                items: items
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Menü yapısı başarıyla kaydedildi!');
            } else {
                alert('Bir hata oluştu!');
            }
        });
    }

    // Menü öğelerini yükle
    function loadMenuItems(menuId) {
        if (!menuId) return;

        fetch(`ajax/get-menu-items.php?menu_id=${menuId}`)
            .then(response => response.json())
            .then(items => {
                const menuItems = document.getElementById('menu_items');
                menuItems.innerHTML = '';
                items.forEach(item => addMenuItem(item));
            });
    }

    // Sayfa yüklendiğinde ilk menüyü seç
    document.addEventListener('DOMContentLoaded', function() {
        const menuSelector = document.getElementById('menu_selector');
        if (menuSelector.options.length > 1) { // İlk option "Menü Seçin" olduğu için 1'den büyük olmalı
            menuSelector.selectedIndex = 1; // İlk menüyü seç
            loadMenuItems(menuSelector.value); // Menü öğelerini yükle
        }
    });
    </script>
</body>
</html> 