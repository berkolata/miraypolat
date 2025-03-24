<?php
require_once '../includes/auth.php';
checkAuth();

// Medya Listesi
$media = [];
$query = 'SELECT * FROM media ORDER BY created_at DESC';
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
    <title>Medya Seç</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-4">
    <div class="mb-4">
        <h2 class="text-lg font-medium text-gray-900">Medya Seç</h2>
    </div>
    
    <div class="grid grid-cols-3 gap-4">
        <?php foreach ($media as $item): ?>
            <div class="relative group cursor-pointer hover:opacity-75" onclick="selectMedia('<?php echo $item['path']; ?>')">
                <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg bg-gray-200">
                    <img src="<?php echo $item['path']; ?>" 
                         alt="<?php echo clean($item['original_name']); ?>"
                         class="object-cover object-center w-full h-full">
                </div>
                <div class="mt-2">
                    <p class="text-sm text-gray-500 truncate"><?php echo clean($item['original_name']); ?></p>
                </div>
                <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 opacity-0 group-hover:opacity-100">
                    <span class="text-white font-medium">Seç</span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
    function selectMedia(url) {
        if (window.opener && window.opener.setFeaturedImage) {
            window.opener.setFeaturedImage(url);
            window.close();
        }
    }
    </script>
</body>
</html> 