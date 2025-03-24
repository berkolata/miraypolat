<?php
require_once '../../includes/auth.php';
checkAuth();

$media = [];
$query = 'SELECT * FROM media ORDER BY created_at DESC';
$result = $db->query($query);

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $media[] = $row;
}

header('Content-Type: application/json');
echo json_encode($media); 