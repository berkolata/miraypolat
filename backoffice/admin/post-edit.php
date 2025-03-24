<?php
require_once '../includes/auth.php';
checkAuth();

$error = '';
$success = '';

// ID kontrolü
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: posts');
    exit;
}

$post_id = (int)$_GET['id'];

// Yazıyı getir
$stmt = $db->prepare('SELECT * FROM posts WHERE id = :id');
$stmt->bindValue(':id', $post_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$post = $result->fetchArray(SQLITE3_ASSOC);

if (!$post) {
    header('Location: posts');
    exit;
}

// Kategorileri Getir
$categories = [];
$result = $db->query('SELECT * FROM categories ORDER BY name ASC');
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $categories[] = $row;
}

// Yazı Güncelleme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_post'])) {
    if (checkCSRFToken($_POST['csrf_token'])) {
        $title = clean($_POST['title']);
        $meta_title = clean($_POST['meta_title']);
        $content = $_POST['content'];
        $category_id = (int)$_POST['category_id'];
        $status = clean($_POST['status']);
        $meta_description = clean($_POST['meta_description']);
        $focus_keyword = clean($_POST['focus_keyword']);
        $featured_image = clean($_POST['featured_image']);
        
        // Slug kontrolü
        $slug = !empty($_POST['slug']) ? createSlug($_POST['slug']) : createSlug($title);
        
        $stmt = $db->prepare('UPDATE posts SET 
            title = :title,
            meta_title = :meta_title,
            slug = :slug,
            content = :content,
            category_id = :category_id,
            status = :status,
            meta_description = :meta_description,
            focus_keyword = :focus_keyword,
            featured_image = :featured_image
            WHERE id = :id');
        
        $stmt->bindValue(':title', $title, SQLITE3_TEXT);
        $stmt->bindValue(':meta_title', $meta_title, SQLITE3_TEXT);
        $stmt->bindValue(':slug', $slug, SQLITE3_TEXT);
        $stmt->bindValue(':content', $content, SQLITE3_TEXT);
        $stmt->bindValue(':category_id', $category_id, SQLITE3_INTEGER);
        $stmt->bindValue(':status', $status, SQLITE3_TEXT);
        $stmt->bindValue(':meta_description', $meta_description, SQLITE3_TEXT);
        $stmt->bindValue(':focus_keyword', $focus_keyword, SQLITE3_TEXT);
        $stmt->bindValue(':featured_image', $featured_image, SQLITE3_TEXT);
        $stmt->bindValue(':id', $post_id, SQLITE3_INTEGER);
        
        try {
            if ($stmt->execute()) {
                $success = "Yazı başarıyla güncellendi!";
                // Güncel veriyi yeniden yükle
                $stmt = $db->prepare('SELECT * FROM posts WHERE id = :id');
                $stmt->bindValue(':id', $post_id, SQLITE3_INTEGER);
                $result = $stmt->execute();
                $post = $result->fetchArray(SQLITE3_ASSOC);
            }
        } catch (Exception $e) {
            $error = "Bu yazı başlığı veya SEO URL'si zaten kullanımda!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Yazı Düzenle</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Ana İçerik -->
        <div class="flex-1">
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-gray-900">Yazı Düzenle</h1>
                        <div class="flex space-x-3">
                            <a href="posts" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                İptal
                            </a>
                            <button type="submit" name="update_post" form="pageForm"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Değişiklikleri Kaydet
                            </button>
                        </div>
                    </div>
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

                <!-- Yazı Düzenleme Formu -->
                <div class="p-0">
                    <form id="pageForm" method="POST" class="space-y-6">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="flex gap-6">
                            <!-- Sol Sütun -->
                            <div class="flex-1 space-y-6">
                                <div class="bg-white shadow rounded-lg p-6">
                                    <!-- Başlık -->
                                    <div class="mb-6">
                                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Başlık</label>
                                        <input type="text" name="title" id="title" required
                                            value="<?php echo clean($post['title']); ?>"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>

                                    <!-- İçerik Editörü -->
                                    <div>
                                        <label for="editor" class="block text-sm font-medium text-gray-700 mb-1">İçerik</label>
                                        <div id="editor" style="height: 600px;"></div>
                                        <input type="hidden" name="content" id="content">
                                    </div>
                                </div>
                                
                                <!-- SEO Ayarları -->
                                <div class="bg-white shadow rounded-lg p-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">SEO</h3>
                                    <div class="space-y-4">
                                        <div class="px-4 py-2 bg-yellow-50 rounded-md">
                                            <label class="block text-sm font-medium text-gray-700">Odak Anahtar Kelime</label>
                                            <input type="text" name="focus_keyword" value="<?php echo clean($post['focus_keyword'] ?? ''); ?>"
                                                   class="mt-1 block w-full rounded-md p-2 border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <p class="mt-1 text-xs text-gray-500">Yazının hedeflediği ana anahtar kelime</p>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">SEO URL</label>
                                            <input type="text" name="slug" value="<?php echo clean($post['slug']); ?>"
                                                   class="mt-1 block w-full rounded-md p-2 border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <p class="mt-1 text-xs text-gray-500">Boş bırakırsanız başlıktan otomatik oluşturulur</p>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Meta Başlık</label>
                                            <input type="text" name="meta_title" value="<?php echo clean($post['meta_title'] ?? $post['title'] ?? ''); ?>"
                                                   class="mt-1 block w-full rounded-md p-2 border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <p class="mt-1 text-xs text-gray-500">
                                                <span class="meta-title-count">0</span>/60 karakter (50-60 arası olmalı)
                                            </p>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Meta Açıklama</label>
                                            <textarea name="meta_description" rows="3"
                                                      class="mt-1 block w-full rounded-md p-2 border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"><?php echo clean($post['meta_description'] ?? ''); ?></textarea>
                                            <p class="mt-1 text-xs text-gray-500">
                                                <span class="meta-desc-count">0</span>/160 karakter (150-160 arası olmalı)
                                            </p>
                                        </div>

                                        <!-- SEO Analiz Sonuçları -->
                                        <div class="seo-analysis mt-4">
                                            <!-- JavaScript ile doldurulacak -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sağ Sütun -->
                            <div class="w-96 space-y-6">
                                <!-- Durum ve Kategori -->
                                <div class="bg-white shadow rounded-lg p-6">
                                    <div class="mb-6">
                                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
                                        <select name="status" id="status" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="draft" <?php echo ($post['status'] == 'draft') ? 'selected' : ''; ?>>Taslak</option>
                                            <option value="pending" <?php echo ($post['status'] == 'pending') ? 'selected' : ''; ?>>İncelemede</option>
                                            <option value="published" <?php echo ($post['status'] == 'published') ? 'selected' : ''; ?>>Yayında</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                        <select name="category_id" id="category" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['id']; ?>" <?php echo ($category['id'] == $post['category_id']) ? 'selected' : ''; ?>>
                                                    <?php echo clean($category['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>


                                <!-- Öne Çıkan Görsel -->
                                <div class="bg-white shadow rounded-lg p-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Öne Çıkan Görsel</h3>
                                    
                                    <div>
                                        <input type="hidden" name="featured_image" id="featured_image" value="<?php echo clean($post['featured_image']); ?>">
                                        <div id="featured_image_preview" class="mb-4 <?php echo empty($post['featured_image']) ? 'hidden' : ''; ?>">
                                            <img src="<?php echo $post['featured_image']; ?>" alt="" class="w-full h-48 object-cover rounded-lg">
                                        </div>
                                        <button type="button" id="selectImageBtn"
                                            class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            Görsel Seç
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal -->
    <div id="mediaModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl">
                <div class="flex justify-between items-center p-4 border-b">
                    <h3 class="text-lg font-medium">Medya Seç</h3>
                    <button id="closeModal" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Kapat</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-4">
                    <div class="mb-4">
                        <form id="uploadForm" class="flex items-center space-x-4">
                            <input type="file" name="file" accept="image/*" class="flex-1">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Yükle
                            </button>
                        </form>
                    </div>
                    <div id="mediaGrid" class="grid grid-cols-3 gap-4">
                        <!-- Medya öğeleri buraya yüklenecek -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        // Quill editör
        var quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote', 'code-block'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'align': [] }],
                    ['link', 'image'],
                    ['clean']
                ]
            }
        });

        // Mevcut içeriği editöre yükle
        quill.root.innerHTML = <?php echo json_encode($post['content']); ?>;

        // Başlıktan otomatik slug oluşturma
        document.getElementById('title').addEventListener('input', function() {
            let slug = this.value.toLowerCase()
                .replace(/[ıİğĞüÜşŞöÖçÇ]/g, function(match) {
                    return {
                        'ı': 'i', 'İ': 'i',
                        'ğ': 'g', 'Ğ': 'g',
                        'ü': 'u', 'Ü': 'u',
                        'ş': 's', 'Ş': 's',
                        'ö': 'o', 'Ö': 'o',
                        'ç': 'c', 'Ç': 'c'
                    }[match];
                })
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            document.getElementById('slug').value = slug;
        });

        // Form gönderilmeden önce editör içeriğini gizli alana aktar
        document.querySelector('form').addEventListener('submit', function() {
            document.getElementById('content').value = quill.root.innerHTML;
        });

        document.addEventListener('DOMContentLoaded', function() {
            const mediaModal = document.getElementById('mediaModal');
            const selectImageBtn = document.getElementById('selectImageBtn');
            const closeModal = document.getElementById('closeModal');
            const uploadForm = document.getElementById('uploadForm');
            const mediaGrid = document.getElementById('mediaGrid');

            // Modal açma
            selectImageBtn.addEventListener('click', function() {
                mediaModal.classList.remove('hidden');
                loadMedia();
            });

            // Modal kapatma
            closeModal.addEventListener('click', function() {
                mediaModal.classList.add('hidden');
            });

            // Medya yükleme
            function loadMedia() {
                fetch('ajax/get-media.php')
                    .then(response => response.json())
                    .then(data => {
                        mediaGrid.innerHTML = data.map(item => `
                            <div class="relative group cursor-pointer" onclick="selectMedia('${item.path}')">
                                <img src="${item.path}" alt="${item.original_name}" class="w-full h-32 object-cover rounded-lg">
                                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 flex items-center justify-center">
                                    <span class="text-white">Seç</span>
                                </div>
                            </div>
                        `).join('');
                    });
            }

            // Görsel seçme
            window.selectMedia = function(url) {
                document.getElementById('featured_image').value = url;
                const preview = document.getElementById('featured_image_preview');
                preview.classList.remove('hidden');
                preview.querySelector('img').src = url;
                mediaModal.classList.add('hidden');
            };

            // Dosya yükleme
            uploadForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch('ajax/upload-media.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadMedia();
                        this.reset();
                    } else {
                        alert(data.error || 'Yükleme başarısız');
                    }
                });
            });
        });

        // SEO Analiz Fonksiyonları
        function analyzeSEO() {
            try {
                const elements = {
                    title: document.querySelector('input[name="title"]'),
                    metaTitle: document.querySelector('input[name="meta_title"]'),
                    metaDesc: document.querySelector('textarea[name="meta_description"]'),
                    focusKeyword: document.querySelector('input[name="focus_keyword"]'),
                    slug: document.querySelector('input[name="slug"]'),
                    analysis: document.querySelector('.seo-analysis')
                };

                // Tüm gerekli elementlerin varlığını kontrol et
                for (let key in elements) {
                    if (!elements[key]) {
                        console.error(`${key} elementi bulunamadı`);
                        return;
                    }
                }

                const title = elements.title.value;
                const metaTitle = elements.metaTitle.value;
                const content = quill.root.innerHTML;
                const plainContent = quill.getText();
                const metaDesc = elements.metaDesc.value;
                const focusKeyword = elements.focusKeyword.value.toLowerCase().trim();
                const slug = elements.slug.value;
                
                let score = 0;
                let maxScore = 0;
                let results = [];

                // Meta başlık kontrolü
                maxScore += 10;
                if (metaTitle.length >= 50 && metaTitle.length <= 60) {
                    score += 10;
                    results.push({type: 'success', message: `Meta başlık uzunluğu ideal (${metaTitle.length}/60 karakter)`});
                } else {
                    results.push({type: 'error', message: `Meta başlık ${metaTitle.length} karakter. 50-60 arası olmalı`});
                }

                // Meta açıklama kontrolü
                maxScore += 10;
                if (metaDesc.length >= 150 && metaDesc.length <= 160) {
                    score += 10;
                    results.push({type: 'success', message: `Meta açıklama uzunluğu ideal (${metaDesc.length}/160 karakter)`});
                } else {
                    results.push({type: 'error', message: `Meta açıklama ${metaDesc.length} karakter. 150-160 arası olmalı`});
                }

                // İçerik uzunluğu kontrolü
                maxScore += 10;
                const wordCount = plainContent.trim().split(/\s+/).length;
                if (wordCount >= 300) {
                    score += 10;
                    results.push({type: 'success', message: `İçerik uzunluğu yeterli (${wordCount} kelime)`});
                } else {
                    results.push({type: 'error', message: `İçerik ${wordCount} kelime. En az 300 kelime olmalı`});
                }

                if (focusKeyword) {
                    // Anahtar kelime yoğunluğu
                    maxScore += 10;
                    const keywordCount = (plainContent.toLowerCase().match(new RegExp(focusKeyword, 'g')) || []).length;
                    const keywordDensity = (keywordCount / wordCount) * 100;
                    
                    if (keywordDensity >= 0.5 && keywordDensity <= 2.5) {
                        score += 10;
                        results.push({type: 'success', message: `Anahtar kelime yoğunluğu ideal (%${keywordDensity.toFixed(1)})`});
                    } else {
                        results.push({type: 'error', message: `Anahtar kelime yoğunluğu %${keywordDensity.toFixed(1)}. %0.5-%2.5 arası olmalı`});
                    }

                    // Meta başlıkta anahtar kelime kontrolü
                    maxScore += 10;
                    if (metaTitle.toLowerCase().includes(focusKeyword)) {
                        score += 10;
                        results.push({type: 'success', message: 'Meta başlıkta anahtar kelime kullanılmış'});
                    } else {
                        results.push({type: 'warning', message: 'Meta başlıkta anahtar kelime kullanılmamış'});
                    }

                    // Meta açıklamada anahtar kelime kontrolü
                    maxScore += 10;
                    if (metaDesc.toLowerCase().includes(focusKeyword)) {
                        score += 10;
                        results.push({type: 'success', message: 'Meta açıklamada anahtar kelime kullanılmış'});
                    } else {
                        results.push({type: 'warning', message: 'Meta açıklamada anahtar kelime kullanılmamış'});
                    }

                    // URL'de anahtar kelime kontrolü
                    maxScore += 10;
                    if (slug.includes(focusKeyword.replace(/\s+/g, '-'))) {
                        score += 10;
                        results.push({type: 'success', message: 'URL\'de anahtar kelime kullanılmış'});
                    } else {
                        results.push({type: 'warning', message: 'URL\'de anahtar kelime kullanılmamış'});
                    }
                } else {
                    results.push({type: 'warning', message: 'Odak anahtar kelime belirlenmemiş'});
                }

                // Alt başlık kontrolü
                maxScore += 10;
                const hasSubheadings = content.includes('<h2') || content.includes('<h3');
                if (hasSubheadings) {
                    score += 10;
                    results.push({type: 'success', message: 'Alt başlıklar kullanılmış'});
                } else {
                    results.push({type: 'warning', message: 'Alt başlık kullanılmamış'});
                }

                // Skor yüzdesini hesapla ve göster
                const percentage = maxScore > 0 ? Math.round((score / maxScore) * 100) : 0;
                updateSEOScore(percentage, results);
            } catch (error) {
                console.error('SEO analizi sırasında hata:', error);
            }
        }

        // SEO skorunu ve önerileri göster
        function updateSEOScore(score, results) {
            const analysisEl = document.querySelector('.seo-analysis');
            if (!analysisEl) return;

            let color = score >= 90 ? '#22c55e' : score >= 70 ? '#f97316' : '#ef4444';
            let html = `
                <div class="border rounded-lg p-4 bg-gray-50">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="flex items-center justify-center w-16 h-16 rounded-full text-white text-xl font-bold" 
                             style="background-color: ${color}">
                            ${score}
                        </div>
                        <div class="text-sm">
                            <div class="font-medium mb-1">SEO Puanı</div>
                            <div class="text-gray-600">100 üzerinden ${score} puan</div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        ${results.map(result => `
                            <div class="flex items-start gap-2 text-sm">
                                <span style="color: ${result.type === 'success' ? '#22c55e' : result.type === 'warning' ? '#f97316' : '#ef4444'}">
                                    ${result.type === 'success' ? '✓' : '⚠'}
                                </span>
                                <span>${result.message}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;

            analysisEl.innerHTML = html;
        }

        // Debounce fonksiyonu
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Sayfa yüklendiğinde başlat
        document.addEventListener('DOMContentLoaded', function() {
            // Event listener'ları ekle
            const inputs = document.querySelectorAll('input[name], textarea[name]');
            inputs.forEach(input => {
                input.addEventListener('input', debounce(analyzeSEO, 500));
            });

            // Quill editör değişikliklerini dinle
            quill.on('text-change', debounce(analyzeSEO, 500));

            // İlk analizi yap
            setTimeout(analyzeSEO, 1000);
        });
    </script>
</body>
</html> 