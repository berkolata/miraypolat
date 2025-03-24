<?php
// XSS koruması için
function clean($data) {
    if ($data === null) {
        return '';
    }
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// CSRF token oluşturma
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// CSRF token kontrolü
function checkCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        die('CSRF token doğrulama hatası!');
    }
    return true;
}

// Oturum kontrolü
function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
}

// Slug oluşturma fonksiyonu
function createSlug($str) {
    $str = mb_strtolower($str, 'UTF-8');
    $str = str_replace(
        ['ı', 'ğ', 'ü', 'ş', 'ö', 'ç', ' ', '_'],
        ['i', 'g', 'u', 's', 'o', 'c', '-', '-'],
        $str
    );
    $str = preg_replace('/[^a-z0-9\-]/', '', $str);
    $str = preg_replace('/-+/', '-', $str);
    return trim($str, '-');
} 