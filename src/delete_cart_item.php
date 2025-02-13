<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
if (!$product_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid product ID']);
    exit;
}

$session_id = session_id();
$db = new SQLite3('products.db');

// Delete the item from cart
$stmt = $db->prepare('DELETE FROM cart WHERE session_id = :session_id AND product_id = :product_id');
$stmt->bindValue(':session_id', $session_id, SQLITE3_TEXT);
$stmt->bindValue(':product_id', $product_id, SQLITE3_INTEGER);

if ($stmt->execute()) {
    // Get updated cart total
    $stmt = $db->prepare('
        SELECT SUM(products.sale_price * cart.quantity) as total
        FROM cart
        JOIN products ON cart.product_id = products.id
        WHERE cart.session_id = :session_id
    ');
    $stmt->bindValue(':session_id', $session_id, SQLITE3_TEXT);
    $result = $stmt->execute();
    $total = $result->fetchArray(SQLITE3_ASSOC)['total'] ?? 0;
    
    echo json_encode([
        'success' => true,
        'message' => 'Item removed from cart',
        'total' => number_format($total, 2)
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to remove item']);
}