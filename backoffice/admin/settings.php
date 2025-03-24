<?php
require_once '../includes/auth.php';
checkAuth();

// Sadece admin ayarları düzenleyebilir
if ($_SESSION['role'] !== 'admin') {
    header('Location: index');
    exit;
}

$error = '';
$success = '';

// Ayarları Güncelle
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_settings'])) {
    if (checkCSRFToken($_POST['csrf_token'])) {
        foreach ($_POST['settings'] as $key => $value) {
            $stmt = $db->prepare('UPDATE settings SET setting_value = :value WHERE setting_key = :key');
            $stmt->bindValue(':value', clean($value), SQLITE3_TEXT);
            $stmt->bindValue(':key', $key, SQLITE3_TEXT);
            $stmt->execute();
        }
        $success = "Ayarlar başarıyla güncellendi!";
    }
}

// Ayarları Gruplandırarak Al
$settings = [];
$query = 'SELECT * FROM settings ORDER BY setting_group, id ASC';
$result = $db->query($query);
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $settings[$row['setting_group']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Ayarlar</title>
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
                    <h1 class="text-2xl font-bold text-gray-900">Site Ayarları</h1>
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

                <!-- Ayarlar Formu -->
                <form method="POST" class="space-y-8">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <?php foreach ($settings as $group => $group_settings): ?>
                        <div class="bg-white shadow rounded-lg overflow-hidden">
                            <div class="px-4 py-5 bg-gray-50 sm:px-6">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">
                                    <?php 
                                    echo match($group) {
                                        'general' => 'Genel Ayarlar',
                                        'content' => 'İçerik Ayarları',
                                        'comments' => 'Yorum Ayarları',
                                        default => ucfirst($group)
                                    };
                                    ?>
                                </h3>
                            </div>

                            <div class="px-4 py-5 sm:p-6 space-y-6">
                                <?php foreach ($group_settings as $setting): ?>
                                    <div>
                                        <label for="<?php echo $setting['setting_key']; ?>" class="block text-sm font-medium text-gray-700">
                                            <?php echo clean($setting['setting_label']); ?>
                                        </label>
                                        <div class="mt-1">
                                            <?php if ($setting['setting_type'] == 'textarea'): ?>
                                                <textarea
                                                    name="settings[<?php echo $setting['setting_key']; ?>]"
                                                    id="<?php echo $setting['setting_key']; ?>"
                                                    rows="3"
                                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm p-2 border border-gray-300 rounded-md"
                                                ><?php echo clean($setting['setting_value']); ?></textarea>
                                            <?php elseif ($setting['setting_type'] == 'checkbox'): ?>
                                                <input
                                                    type="checkbox"
                                                    name="settings[<?php echo $setting['setting_key']; ?>]"
                                                    id="<?php echo $setting['setting_key']; ?>"
                                                    value="1"
                                                    <?php echo $setting['setting_value'] == '1' ? 'checked' : ''; ?>
                                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 p-2 border border-gray-300 rounded"
                                                >
                                            <?php else: ?>
                                                <input
                                                    type="<?php echo $setting['setting_type']; ?>"
                                                    name="settings[<?php echo $setting['setting_key']; ?>]"
                                                    id="<?php echo $setting['setting_key']; ?>"
                                                    value="<?php echo clean($setting['setting_value']); ?>"
                                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm p-2 border border-gray-300 rounded-md"
                                                >
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="flex justify-end">
                        <button type="submit" name="update_settings" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Ayarları Kaydet
                        </button>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>
</html> 