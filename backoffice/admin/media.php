<?php
require_once '../includes/auth.php';
checkAuth();

$error = '';
$success = '';

// Kabul edilen dosya türleri
$allowed_types = [
    'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/avif',
    'IMAGE/JPEG', 'IMAGE/JPG', 'IMAGE/PNG', 'IMAGE/GIF', 'IMAGE/WEBP', 'IMAGE/AVIF'
];

// Dosya yükleme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload'])) {
    if (checkCSRFToken($_POST['csrf_token'])) {
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $file = $_FILES['file'];
            
            if (in_array($file['type'], $allowed_types)) {
                $upload_dir = '../../uploads/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $filename = uniqid() . '_' . clean(basename($file['name']));
                $filepath = $upload_dir . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $filepath)) {
                    $stmt = $db->prepare('INSERT INTO media (filename, original_name, mime_type, file_size, path, uploaded_by) 
                                        VALUES (:filename, :original_name, :mime_type, :file_size, :path, :uploaded_by)');
                    
                    $stmt->bindValue(':filename', $filename, SQLITE3_TEXT);
                    $stmt->bindValue(':original_name', $file['name'], SQLITE3_TEXT);
                    $stmt->bindValue(':mime_type', $file['type'], SQLITE3_TEXT);
                    $stmt->bindValue(':file_size', $file['size'], SQLITE3_INTEGER);
                    $stmt->bindValue(':path', '/uploads/' . $filename, SQLITE3_TEXT);
                    $stmt->bindValue(':uploaded_by', $_SESSION['user_id'], SQLITE3_INTEGER);
                    
                    if ($stmt->execute()) {
                        $success = "Dosya başarıyla yüklendi!";
                    }
                }
            } else {
                $error = "Desteklenmeyen dosya türü!";
            }
        }
    }
}

// Dosya silme
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Önce dosya bilgilerini alalım
    $stmt = $db->prepare('SELECT * FROM media WHERE id = :id');
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $media = $result->fetchArray(SQLITE3_ASSOC);
    
    if ($media) {
        // Dosyayı fiziksel olarak sil
        $filepath = $_SERVER['DOCUMENT_ROOT'] . $media['path'];
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        
        // Veritabanından sil
        $stmt = $db->prepare('DELETE FROM media WHERE id = :id');
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->execute();
        
        $success = "Görsel başarıyla silindi!";
        header('Location: media');
        exit;
    }
}

// Medya Güncelleme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_media'])) {
    if (checkCSRFToken($_POST['csrf_token'])) {
        $id = (int)$_POST['media_id'];
        $title = clean($_POST['title']);
        $alt = clean($_POST['alt']);
        $description = clean($_POST['description']);
        
        $stmt = $db->prepare('UPDATE media SET title = :title, alt = :alt, description = :description WHERE id = :id');
        $stmt->bindValue(':title', $title, SQLITE3_TEXT);
        $stmt->bindValue(':alt', $alt, SQLITE3_TEXT);
        $stmt->bindValue(':description', $description, SQLITE3_TEXT);
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        
        if ($stmt->execute()) {
            $success = "Medya başarıyla güncellendi!";
        }
    }
}

// Medyaları Listele
$media = [];
$query = 'SELECT m.*, u.username as uploader 
          FROM media m 
          LEFT JOIN users u ON m.uploaded_by = u.id 
          ORDER BY m.created_at DESC';
$result = $db->query($query);
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $media[] = $row;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Medya Kütüphanesi</title>
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
                    <h1 class="text-2xl font-bold text-gray-900">Medya Kütüphanesi</h1>
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

                <!-- Dosya Yükleme Formu -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="p-4 sm:p-6">
                        <form method="POST" enctype="multipart/form-data" class="space-y-4">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Dosya Yükle
                                </label>
                                <div class="mt-1 flex items-center">
                                    <input type="file" name="file" accept="image/*" required
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                    <button type="submit" name="upload"
                                        class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Yükle
                                    </button>
                                </div>
                                <p class="mt-2 text-xs text-gray-500">JPG, JPEG, PNG, GIF, WEBP veya AVIF. Max 2MB.</p>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Medya Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <?php foreach ($media as $item): ?>
                    <div class="relative group cursor-pointer" onclick="openMediaModal(<?php echo htmlspecialchars(json_encode($item)); ?>)">
                        <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg bg-gray-200">
                            <img src="<?php echo $item['path']; ?>" 
                                 alt="<?php echo $item['alt'] ?? $item['original_name']; ?>"
                                 class="object-cover w-full h-48">
                        </div>
                        <div class="mt-2 text-sm text-gray-700 truncate">
                            <?php echo $item['title'] ?? $item['original_name']; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Medya Modal -->
                <div id="mediaModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
                    <div class="flex min-h-screen items-center justify-center p-4">
                        <div class="bg-white rounded-lg shadow-xl w-full max-w-6xl">
                            <div class="flex justify-between items-center p-4 border-b">
                                <h3 class="text-lg font-medium" id="modalTitle">Medya Düzenle</h3>
                                <button onclick="closeMediaModal()" class="text-gray-400 hover:text-gray-500">
                                    <span class="sr-only">Kapat</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div class="flex p-4">
                                <!-- Sol taraf - Resim -->
                                <div class="w-1/2 px-4">
                                    <img id="modalImage" src="" alt="" class="aspect-auto max-h-[75vh]">

                                    <div class="flex my-4">
                                        <button type="button" onclick="deleteMedia()" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                                            Görseli Sil
                                        </button>
                                    </div>
                                </div>
                                <!-- Sağ taraf - Form -->
                                <div class="w-1/2 pr-8">
                                    <form id="mediaForm" method="POST" class="space-y-4">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        <input type="hidden" name="media_id" id="mediaId">
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Dosya Adı</label>
                                            <input type="text" name="title" id="mediaTitle" 
                                                   class="mt-1 block w-full rounded-md p-2 border-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Alt Etiketi</label>
                                            <input type="text" name="alt" id="mediaAlt" 
                                                   class="mt-1 block w-full rounded-md p-2 border-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Açıklama</label>
                                            <textarea name="description" id="mediaDescription" rows="3" 
                                                      class="mt-1 block w-full rounded-md p-2 border-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                        </div>
                                        
                                        <div class="flex justify-end space-x-3">
                                            <button type="button" onclick="closeMediaModal()" 
                                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                                                İptal
                                            </button>
                                            <button type="submit" name="update_media" 
                                                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md">
                                                Kaydet
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
    let currentMediaId = null; // Global değişken olarak medya ID'sini tutalım

    function openMediaModal(media) {
        document.getElementById('mediaModal').classList.remove('hidden');
        document.getElementById('modalImage').src = media.path;
        document.getElementById('mediaId').value = media.id;
        document.getElementById('mediaTitle').value = media.title || media.original_name;
        document.getElementById('mediaAlt').value = media.alt || '';
        document.getElementById('mediaDescription').value = media.description || '';
        currentMediaId = media.id; // ID'yi global değişkene atayalım
    }

    function closeMediaModal() {
        document.getElementById('mediaModal').classList.add('hidden');
    }

    function deleteMedia() {
        if (confirm('Bu görseli silmek istediğinizden emin misiniz?')) {
            window.location.href = `?delete=${currentMediaId}`;
        }
    }

    // Modal dışına tıklandığında kapatma
    document.getElementById('mediaModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeMediaModal();
        }
    });
    </script>
</body>
</html> 