<?php
require_once '../includes/auth.php';
checkAuth();

$error = '';
$success = '';

// Filtreleme parametreleri
$default_per_page = $db->querySingle('SELECT setting_value FROM settings WHERE setting_key = "posts_per_page"') ?: 20;
$per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : (int)$default_per_page;
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$status = isset($_GET['status']) ? clean($_GET['status']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Geçerli per_page değerleri
$allowed_per_page = [(int)$default_per_page, 50, 100];
if (!in_array($per_page, $allowed_per_page)) {
    $per_page = (int)$default_per_page;
}

// Yazı Silme
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    $stmt = $db->prepare('DELETE FROM posts WHERE id = :id');
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $stmt->execute();
    
    header('Location: posts');
    exit;
}

// Yazıları Listele
$posts = [];
$where = [];
$params = [];
if ($category_id > 0) {
    $where[] = "p.category_id = :category_id";
    $params[':category_id'] = $category_id;
}
if ($status !== '') {
    $where[] = "p.status = :status";
    $params[':status'] = $status;
}

$where_sql = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';
$count_sql = "SELECT COUNT(*) FROM posts p" . $where_sql;
$stmt = $db->prepare($count_sql);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$total_posts = $stmt->execute()->fetchArray()[0];

// Toplam sayfa sayısı
$total_pages = ceil($total_posts / $per_page);
if ($page > $total_pages) {
    $page = $total_pages;
}
if ($page < 1) {
    $page = 1;
}

// Offset hesapla
$offset = ($page - 1) * $per_page;

// Yazıları getir
$query = 'SELECT p.*, u.username as author_name, c.name as category_name 
          FROM posts p 
          LEFT JOIN users u ON p.author_id = u.id 
          LEFT JOIN categories c ON p.category_id = c.id'
          . $where_sql . 
          ' ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset';

$stmt = $db->prepare($query);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->bindValue(':limit', $per_page, SQLITE3_INTEGER);
$stmt->bindValue(':offset', $offset, SQLITE3_INTEGER);
$result = $stmt->execute();

$posts = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $posts[] = $row;
}

// Kategorileri getir (filtre için)
$categories = [];
$cat_result = $db->query('SELECT * FROM categories ORDER BY name ASC');
while ($row = $cat_result->fetchArray(SQLITE3_ASSOC)) {
    $categories[] = $row;
}

// Yazıları listelerken SEO puanını hesaplama fonksiyonu
function calculateSEOScore($post) {
    $score = 0;
    $maxScore = 0;
    
    // Meta başlık kontrolü
    $maxScore += 10;
    $metaTitleLength = strlen($post['meta_title']);
    if ($metaTitleLength >= 50 && $metaTitleLength <= 60) {
        $score += 10;
    }

    // Meta açıklama kontrolü
    $maxScore += 10;
    $metaDescLength = strlen($post['meta_description']);
    if ($metaDescLength >= 150 && $metaDescLength <= 160) {
        $score += 10;
    }

    // İçerik uzunluğu kontrolü
    $maxScore += 10;
    $wordCount = str_word_count(strip_tags($post['content']));
    if ($wordCount >= 300) {
        $score += 10;
    }

    // Anahtar kelime kontrolleri
    if (!empty($post['focus_keyword'])) {
        $focusKeyword = strtolower($post['focus_keyword']);
        $content = strtolower(strip_tags($post['content']));
        
        // Anahtar kelime yoğunluğu
        $maxScore += 10;
        $keywordCount = substr_count($content, $focusKeyword);
        $keywordDensity = ($keywordCount / $wordCount) * 100;
        if ($keywordDensity >= 0.5 && $keywordDensity <= 2.5) {
            $score += 10;
        }

        // Meta başlıkta anahtar kelime
        $maxScore += 10;
        if (stripos($post['meta_title'], $focusKeyword) !== false) {
            $score += 10;
        }

        // Meta açıklamada anahtar kelime
        $maxScore += 10;
        if (stripos($post['meta_description'], $focusKeyword) !== false) {
            $score += 10;
        }

        // URL'de anahtar kelime
        $maxScore += 10;
        if (stripos($post['slug'], str_replace(' ', '-', $focusKeyword)) !== false) {
            $score += 10;
        }
    }

    // Alt başlık kontrolü
    $maxScore += 10;
    if (strpos($post['content'], '<h2') !== false || strpos($post['content'], '<h3') !== false) {
        $score += 10;
    }

    return $maxScore > 0 ? round(($score / $maxScore) * 100) : 0;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Yazılar</title>
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
                    <h1 class="text-2xl font-bold text-gray-900">Yazılar</h1>
                    <a href="post-add" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Yeni Yazı Ekle
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

                <!-- Filtre alanını ekleyelim -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="p-4">
                        <form method="GET" class="flex items-center space-x-4">
                            <div>
                                <label for="per_page" class="block text-sm font-medium text-gray-700">Sayfa Başına</label>
                                <select name="per_page" id="per_page" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <?php foreach ($allowed_per_page as $value): ?>
                                        <option value="<?php echo $value; ?>" <?php echo $per_page == $value ? 'selected' : ''; ?>>
                                            <?php echo $value; ?> öğe
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                                <select name="category_id" id="category_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="0">Tüm Kategoriler</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" <?php echo $category_id == $category['id'] ? 'selected' : ''; ?>>
                                            <?php echo clean($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Durum</label>
                                <select name="status" id="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">Tüm Durumlar</option>
                                    <option value="draft" <?php echo $status == 'draft' ? 'selected' : ''; ?>>Taslak</option>
                                    <option value="pending" <?php echo $status == 'pending' ? 'selected' : ''; ?>>İncelemede</option>
                                    <option value="published" <?php echo $status == 'published' ? 'selected' : ''; ?>>Yayında</option>
                                </select>
                            </div>

                            <div class="flex items-end">
                                <button type="submit" class="bg-indigo-600 px-4 py-2 mt-5 text-white rounded-md hover:bg-indigo-700">
                                    Filtrele
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Yazılar Listesi -->
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
                                                        Başlık
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Yazar
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Kategori
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Durum
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        SEO
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
                                                <?php foreach ($posts as $post): 
                                                    $seoScore = calculateSEOScore($post);
                                                ?>
                                                <tr>
                                                    <td class="px-6 py-4">
                                                        <div class="flex items-center">
                                                            <?php if (!empty($post['featured_image'])): ?>
                                                                <div class="flex-shrink-0 h-16 w-16 mr-4">
                                                                    <img src="<?php echo $post['featured_image']; ?>" 
                                                                         alt="<?php echo clean($post['title']); ?>"
                                                                         class="h-16 w-16 object-cover rounded">
                                                                </div>
                                                            <?php endif; ?>
                                                            <div class="text-sm font-medium text-gray-900">
                                                                <?php echo clean($post['title']); ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        <?php echo clean($post['author_name']); ?>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        <?php echo clean($post['category_name']); ?>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            <?php 
                                                            echo match($post['status']) {
                                                                'published' => 'bg-green-100 text-green-800',
                                                                'draft' => 'bg-yellow-100 text-yellow-800',
                                                                'pending' => 'bg-blue-100 text-blue-800',
                                                            };
                                                            ?>">
                                                            <?php 
                                                            echo match($post['status']) {
                                                                'published' => 'Yayında',
                                                                'draft' => 'Taslak',
                                                                'pending' => 'İncelemede',
                                                            };
                                                            ?>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-full 
                                                                <?php 
                                                                echo $seoScore >= 90 ? 'bg-green-100 text-green-800' : 
                                                                     ($seoScore >= 70 ? 'bg-yellow-100 text-yellow-800' : 
                                                                               'bg-red-100 text-red-800'); 
                                                                ?>">
                                                                <?php echo $seoScore; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        <?php echo date('d.m.Y H:i', strtotime($post['created_at'])); ?>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <a href="post-edit?id=<?php echo $post['id']; ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">Düzenle</a>
                                                        <a href="?delete=<?php echo $post['id']; ?>" 
                                                           onclick="return confirm('Bu yazıyı silmek istediğinizden emin misiniz?')"
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

                <!-- Sayfalama -->
                <?php if ($total_pages > 1): ?>
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 mb-6">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page-1; ?>&per_page=<?php echo $per_page; ?>&category_id=<?php echo $category_id; ?>&status=<?php echo $status; ?>" 
                               class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Önceki
                            </a>
                        <?php endif; ?>
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page+1; ?>&per_page=<?php echo $per_page; ?>&category_id=<?php echo $category_id; ?>&status=<?php echo $status; ?>" 
                               class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Sonraki
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Toplam <span class="font-medium"><?php echo $total_posts; ?></span> yazıdan 
                                <span class="font-medium"><?php echo ($offset + 1); ?></span> -
                                <span class="font-medium"><?php echo min($offset + $per_page, $total_posts); ?></span> arası gösteriliyor
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                    <a href="?page=<?php echo $i; ?>&per_page=<?php echo $per_page; ?>&category_id=<?php echo $category_id; ?>&status=<?php echo $status; ?>" 
                                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 <?php echo $page == $i ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>
                            </nav>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
</body>
</html> 