<?php
require_once '../../includes/auth.php';
checkAuth();

$response = ['success' => false];

if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/avif'];
    
    if (in_array($file['type'], $allowed_types)) {
        $upload_dir = '../../../uploads/';
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
                $response = [
                    'success' => true,
                    'path' => '/uploads/' . $filename
                ];
            }
        }
    } else {
        $response['error'] = 'Desteklenmeyen dosya türü!';
    }
}

header('Content-Type: application/json');
echo json_encode($response); 