<?php
require_once 'config.php';
require_once 'functions.php';

// Kullanıcı kaydı fonksiyonu
function registerUser($username, $email, $password) {
    global $db;
    
    // Kullanıcı adı ve email kontrolü
    $stmt = $db->prepare('SELECT id FROM users WHERE username = :username OR email = :email');
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $result = $stmt->execute();
    
    if ($result->fetchArray()) {
        return "Bu kullanıcı adı veya email zaten kullanımda!";
    }
    
    // Şifreyi hashle
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
    // Kullanıcıyı kaydet
    $stmt = $db->prepare('INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)');
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $stmt->bindValue(':password', $hashedPassword, SQLITE3_TEXT);
    $stmt->bindValue(':role', 'subscriber', SQLITE3_TEXT);
    
    if ($stmt->execute()) {
        return true;
    }
    return "Kayıt işlemi başarısız oldu!";
}

// Kullanıcı girişi fonksiyonu
function loginUser($username, $password) {
    global $db;
    
    $stmt = $db->prepare('SELECT id, username, password, role FROM users WHERE username = :username OR email = :username');
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $result = $stmt->execute();
    
    if ($user = $result->fetchArray(SQLITE3_ASSOC)) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            return true;
        }
    }
    return "Kullanıcı adı veya şifre hatalı!";
} 