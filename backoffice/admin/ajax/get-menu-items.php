<?php
require_once '../../includes/auth.php';
checkAuth();

$items = [];

if (isset($_GET['menu_id'])) {
    $menu_id = (int)$_GET['menu_id'];
    
    $stmt = $db->prepare('SELECT * FROM menu_items WHERE menu_id = :menu_id ORDER BY order_number ASC');
    $stmt->bindValue(':menu_id', $menu_id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $items[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($items); 