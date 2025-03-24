<?php
require_once '../../includes/auth.php';
checkAuth();

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['menu_id']) && isset($data['items'])) {
        $menu_id = (int)$data['menu_id'];
        
        // Önce mevcut öğeleri sil
        $stmt = $db->prepare('DELETE FROM menu_items WHERE menu_id = :menu_id');
        $stmt->bindValue(':menu_id', $menu_id, SQLITE3_INTEGER);
        $stmt->execute();
        
        // Yeni öğeleri ekle
        foreach ($data['items'] as $item) {
            $url = null;
            if ($item['type'] == 'post') {
                $url = '/post/' . $item['item_id'];
            } elseif ($item['type'] == 'category') {
                $url = '/category/' . $item['item_id'];
            } elseif ($item['type'] == 'page') {
                $url = '/page/' . $item['item_id'];
            } else {
                $url = $item['url'];
            }
            
            $stmt = $db->prepare('INSERT INTO menu_items (menu_id, title, url, type, item_id, order_number) 
                                 VALUES (:menu_id, :title, :url, :type, :item_id, :order_number)');
            
            $stmt->bindValue(':menu_id', $menu_id, SQLITE3_INTEGER);
            $stmt->bindValue(':title', $item['title'], SQLITE3_TEXT);
            $stmt->bindValue(':url', $url, SQLITE3_TEXT);
            $stmt->bindValue(':type', $item['type'], SQLITE3_TEXT);
            $stmt->bindValue(':item_id', $item['item_id'] ?? null, SQLITE3_INTEGER);
            $stmt->bindValue(':order_number', $item['order_number'], SQLITE3_INTEGER);
            
            $stmt->execute();
        }
        
        $response['success'] = true;
    }
}

header('Content-Type: application/json');
echo json_encode($response); 