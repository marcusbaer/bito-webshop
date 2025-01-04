<?php
session_start();

$productId = $_POST['product_id'] ?? null;
$quantity = $_POST['quantity'] ?? 1;
$sessionId = session_id();

if ($productId) {
    $db = new SQLite3('products.db');
    $stmt = $db->prepare('INSERT INTO cart (session_id, product_id, quantity) VALUES (:session_id, :product_id, :quantity)');
    $stmt->bindValue(':session_id', $sessionId, SQLITE3_TEXT);
    $stmt->bindValue(':product_id', $productId, SQLITE3_INTEGER);
    $stmt->bindValue(':quantity', $quantity, SQLITE3_INTEGER);
    $stmt->execute();
    
    header('Location: cart.php');
    exit;
}