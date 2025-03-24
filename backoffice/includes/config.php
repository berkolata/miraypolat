<?php
// Hata raporlama
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Functions dosyasını dahil et
require_once 'functions.php';

// Session başlatma
session_start();

// SQLite veritabanı bağlantısı
try {
    $db_path = dirname(__DIR__) . '/BAC2917SJASD/database.sqlite';
    error_log("Veritabanı bağlantısı başlatılıyor - Yol: " . $db_path);

    if (!file_exists($db_path)) {
        error_log("HATA: Veritabanı dosyası bulunamadı: " . $db_path);
        die("Veritabanı dosyası bulunamadı!");
    }

    // Veritabanı bağlantısını kur
    $db = new SQLite3($db_path);
    
    // Windows için temel SQLite ayarları
    $db->exec('PRAGMA journal_mode = DELETE');
    $db->exec('PRAGMA synchronous = OFF');
    $db->exec('PRAGMA cache_size = -2000'); // Negatif değer KB cinsinden
    $db->exec('PRAGMA page_size = 4096');
    $db->exec('PRAGMA temp_store = MEMORY');
    $db->exec('PRAGMA mmap_size = 30000000000');
    $db->exec('PRAGMA busy_timeout = 60000'); // 60 saniye timeout
    
    // Veritabanı tabloları ilk kez oluşturulacaksa
    $result = $db->query("SELECT name FROM sqlite_master WHERE type='table'");
    if (!$result->fetchArray()) {
        error_log("Veritabanı tabloları oluşturuluyor...");
        
        // Tüm CREATE TABLE sorgularını tek bir transaction içinde çalıştır
        $db->exec('BEGIN EXCLUSIVE TRANSACTION');
        
        // Tablolar oluştur
        foreach ($queries as $query) {
            if (!$db->exec($query)) {
                throw new Exception($db->lastErrorMsg());
            }
        }
        
        // Transaction'ı tamamla
        $db->exec('COMMIT');
        error_log("Veritabanı tabloları oluşturuldu");
    }

    // Bağlantıyı test et
    $test = $db->query('SELECT COUNT(*) as count FROM pages');
    if (!$test) {
        throw new Exception($db->lastErrorMsg());
    }
    
    $row = $test->fetchArray(SQLITE3_ASSOC);
    error_log("Veritabanı test sorgusu - Toplam sayfa sayısı: " . $row['count']);

} catch (Exception $e) {
    error_log("Veritabanı bağlantı hatası: " . $e->getMessage());
    die('Veritabanı bağlantı hatası: ' . $e->getMessage());
}

// Veritabanı durumunu kontrol et ve logla
try {
    $pragmaResults = [
        'journal_mode' => $db->query("PRAGMA journal_mode")->fetchArray(SQLITE3_ASSOC),
        'synchronous' => $db->query("PRAGMA synchronous")->fetchArray(SQLITE3_ASSOC),
        'locking_mode' => $db->query("PRAGMA locking_mode")->fetchArray(SQLITE3_ASSOC),
        'cache_size' => $db->query("PRAGMA cache_size")->fetchArray(SQLITE3_ASSOC),
        'page_size' => $db->query("PRAGMA page_size")->fetchArray(SQLITE3_ASSOC)
    ];
    
    error_log("SQLite Ayarları: " . print_r($pragmaResults, true));
} catch (Exception $e) {
    error_log("PRAGMA kontrol hatası: " . $e->getMessage());
}

// Global transaction yönetimi kaldırıldı
// Her işlem kendi transaction'ını yönetecek

// Veritabanı tablolarını oluştur
$queries = [
    // Sadece CREATE TABLE ifadeleri olmalı
    'CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        role TEXT CHECK(role IN ("admin", "editor", "author", "subscriber")) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )',

    'CREATE TABLE IF NOT EXISTS categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT UNIQUE NOT NULL,
        slug TEXT UNIQUE NOT NULL,
        description TEXT,
        parent_id INTEGER DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (parent_id) REFERENCES categories(id)
    )',

    'CREATE TABLE IF NOT EXISTS posts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        meta_title TEXT,
        slug TEXT UNIQUE NOT NULL,
        content TEXT NOT NULL,
        meta_description TEXT,
        focus_keyword TEXT,
        featured_image TEXT,
        status TEXT CHECK(status IN ("draft", "published", "pending")) NOT NULL,
        author_id INTEGER,
        category_id INTEGER,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (author_id) REFERENCES users(id),
        FOREIGN KEY (category_id) REFERENCES categories(id)
    )',

    'CREATE TABLE IF NOT EXISTS comments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        post_id INTEGER,
        user_id INTEGER,
        comment TEXT NOT NULL,
        status TEXT CHECK(status IN ("approved", "pending", "spam")) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES posts(id),
        FOREIGN KEY (user_id) REFERENCES users(id)
    )',

    'CREATE TABLE IF NOT EXISTS settings (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        setting_key TEXT UNIQUE NOT NULL,
        setting_value TEXT,
        setting_group TEXT NOT NULL,
        setting_label TEXT NOT NULL,
        setting_type TEXT NOT NULL DEFAULT "text",
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )',

    'CREATE TABLE IF NOT EXISTS media (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        filename TEXT NOT NULL,
        original_name TEXT NOT NULL,
        mime_type TEXT NOT NULL,
        file_size INTEGER NOT NULL,
        path TEXT NOT NULL,
        uploaded_by INTEGER,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (uploaded_by) REFERENCES users(id)
    )',

    'CREATE TABLE IF NOT EXISTS menus (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        location TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )',

    'CREATE TABLE IF NOT EXISTS menu_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        menu_id INTEGER NOT NULL,
        parent_id INTEGER DEFAULT NULL,
        title TEXT NOT NULL,
        url TEXT,
        type TEXT NOT NULL, /* post, category, custom */
        item_id INTEGER, /* post_id veya category_id */
        order_number INTEGER DEFAULT 0,
        target TEXT DEFAULT "_self",
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE CASCADE,
        FOREIGN KEY (parent_id) REFERENCES menu_items(id) ON DELETE SET NULL
    )',

    'DROP TABLE IF EXISTS pages',

    'CREATE TABLE IF NOT EXISTS pages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        meta_title TEXT,
        slug TEXT UNIQUE NOT NULL,
        content TEXT NOT NULL,
        meta_description TEXT,
        focus_keyword TEXT,
        featured_image TEXT,
        status TEXT CHECK(status IN ("draft", "published", "pending")) NOT NULL DEFAULT "draft",
        author_id INTEGER,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (author_id) REFERENCES users(id)
    )'
];

// Media tablosuna yeni alanlar ekle - sadece yoksa ekle
$columns = [];
$result = $db->query("PRAGMA table_info(media)");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $columns[] = $row['name'];
}

if (!in_array('title', $columns)) {
    $db->exec('ALTER TABLE media ADD COLUMN title TEXT');
}
if (!in_array('alt', $columns)) {
    $db->exec('ALTER TABLE media ADD COLUMN alt TEXT');
}
if (!in_array('description', $columns)) {
    $db->exec('ALTER TABLE media ADD COLUMN description TEXT');
}

// İlk admin kullanıcısını oluştur
$adminCheck = $db->querySingle('SELECT COUNT(*) FROM users');
if ($adminCheck == 0) {
    $adminPassword = password_hash('admin123', PASSWORD_BCRYPT);
    $db->exec('INSERT INTO users (username, email, password, role) 
               VALUES ("admin", "admin@localhost", "' . $adminPassword . '", "admin")');
}

// Varsayılan kategoriyi oluştur
$categoryCheck = $db->querySingle('SELECT COUNT(*) FROM categories');
if ($categoryCheck == 0) {
    $db->exec('INSERT INTO categories (name, slug) VALUES ("Genel", "genel")');
}

// Örnek yazıları ekle
$postCheck = $db->querySingle('SELECT COUNT(*) FROM posts');
if ($postCheck == 0) {
    $posts = [
        
    ];

    foreach ($posts as $post) {
        $stmt = $db->prepare('INSERT INTO posts (
            title,
            meta_title,
            slug,
            content,
            category_id,
            author_id,
            status,
            meta_description,
            focus_keyword
        ) VALUES (
            :title,
            :meta_title,
            :slug,
            :content,
            :category_id,
            :author_id,
            :status,
            :meta_description,
            :focus_keyword
        )');

        $stmt->bindValue(':title', $post['title'], SQLITE3_TEXT);
        $stmt->bindValue(':meta_title', $post['meta_title'], SQLITE3_TEXT);
        $stmt->bindValue(':slug', createSlug($post['title']), SQLITE3_TEXT);
        $stmt->bindValue(':content', $post['content'], SQLITE3_TEXT);
        $stmt->bindValue(':category_id', $post['category_id'], SQLITE3_INTEGER);
        $stmt->bindValue(':author_id', 1, SQLITE3_INTEGER);
        $stmt->bindValue(':status', $post['status'], SQLITE3_TEXT);
        $stmt->bindValue(':meta_description', $post['meta_description'], SQLITE3_TEXT);
        $stmt->bindValue(':focus_keyword', $post['focus_keyword'], SQLITE3_TEXT);
        
        $stmt->execute();
    }
}

// Varsayılan ayarları ekle
$settingsCheck = $db->querySingle('SELECT COUNT(*) FROM settings');
if ($settingsCheck == 0) {
    $defaultSettings = [
        [
            'key' => 'site_title',
            'value' => 'Blog Sitesi',
            'group' => 'general',
            'label' => 'Site Başlığı',
            'type' => 'text'
        ],
        [
            'key' => 'site_description',
            'value' => 'Blog sitesi açıklaması',
            'group' => 'general',
            'label' => 'Site Açıklaması',
            'type' => 'textarea'
        ],
        [
            'key' => 'site_email',
            'value' => 'admin@localhost',
            'group' => 'general',
            'label' => 'Site E-posta Adresi',
            'type' => 'email'
        ],
        [
            'key' => 'posts_per_page',
            'value' => '10',
            'group' => 'content',
            'label' => 'Sayfa Başına Yazı Sayısı',
            'type' => 'number'
        ],
        [
            'key' => 'comments_auto_approve',
            'value' => '0',
            'group' => 'comments',
            'label' => 'Yorumları Otomatik Onayla',
            'type' => 'checkbox'
        ]
    ];

    foreach ($defaultSettings as $setting) {
        $stmt = $db->prepare('INSERT INTO settings (setting_key, setting_value, setting_group, setting_label, setting_type) VALUES (:key, :value, :group, :label, :type)');
        $stmt->bindValue(':key', $setting['key'], SQLITE3_TEXT);
        $stmt->bindValue(':value', $setting['value'], SQLITE3_TEXT);
        $stmt->bindValue(':group', $setting['group'], SQLITE3_TEXT);
        $stmt->bindValue(':label', $setting['label'], SQLITE3_TEXT);
        $stmt->bindValue(':type', $setting['type'], SQLITE3_TEXT);
        $stmt->execute();
    }
}

// Örnek yorumları ekle
$commentCheck = $db->querySingle('SELECT COUNT(*) FROM comments');
if ($commentCheck == 0) {
    $comments = [
    ];

    // Rastgele yazıları al
    $posts = $db->query('SELECT id FROM posts ORDER BY RANDOM() LIMIT 5');
    $post_ids = [];
    while ($row = $posts->fetchArray(SQLITE3_ASSOC)) {
        $post_ids[] = $row['id'];
    }

    // Rastgele kullanıcıları al
    $users = $db->query('SELECT id FROM users ORDER BY RANDOM() LIMIT 3');
    $user_ids = [];
    while ($row = $users->fetchArray(SQLITE3_ASSOC)) {
        $user_ids[] = $row['id'];
    }

    foreach ($comments as $comment) {
        $stmt = $db->prepare('INSERT INTO comments (
            post_id,
            user_id,
            comment,
            status
        ) VALUES (
            :post_id,
            :user_id,
            :comment,
            :status
        )');

        $stmt->bindValue(':post_id', $post_ids[array_rand($post_ids)], SQLITE3_INTEGER);
        $stmt->bindValue(':user_id', $user_ids[array_rand($user_ids)], SQLITE3_INTEGER);
        $stmt->bindValue(':comment', $comment['comment'], SQLITE3_TEXT);
        $stmt->bindValue(':status', $comment['status'], SQLITE3_TEXT);
        
        $stmt->execute();
    }
}

// Örnek sayfaları ekle (sadece pages tablosu boşsa)
$pageCheck = $db->querySingle('SELECT COUNT(*) FROM pages');
if ($pageCheck == 0) {
    $pages = [
    ];

    foreach ($pages as $page) {
        $stmt = $db->prepare('INSERT INTO pages (
            title,
            meta_title,
            slug,
            content,
            status,
            author_id
        ) VALUES (
            :title,
            :meta_title,
            :slug,
            :content,
            :status,
            :author_id
        )');

        $stmt->bindValue(':title', $page['title'], SQLITE3_TEXT);
        $stmt->bindValue(':meta_title', $page['meta_title'], SQLITE3_TEXT);
        $stmt->bindValue(':slug', createSlug($page['title']), SQLITE3_TEXT);
        $stmt->bindValue(':content', $page['content'], SQLITE3_TEXT);
        $stmt->bindValue(':status', $page['status'], SQLITE3_TEXT);
        $stmt->bindValue(':author_id', 1, SQLITE3_INTEGER);
        
        $stmt->execute();
    }
} 