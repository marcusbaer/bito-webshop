<?php
session_start();
header('Content-Type: application/json');

// Ensure this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Get JSON payload
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON data']);
    exit;
}

try {
    $db = new SQLite3('products.db');
    $db->exec('BEGIN TRANSACTION');

    // Create order record
    $stmt = $db->prepare('
        INSERT INTO orders (
            session_id,
            total_amount,
            payer_name,
            payer_email,
            payer_phone,
            shipping_address,
            payment_method,
            payment_status,
            created_at
        ) VALUES (
            :session_id,
            :total_amount,
            :payer_name,
            :payer_email,
            :payer_phone,
            :shipping_address,
            :payment_method,
            :payment_status,
            datetime("now")
        )
    ');

    $stmt->bindValue(':session_id', session_id(), SQLITE3_TEXT);
    $stmt->bindValue(':total_amount', $data['total'], SQLITE3_FLOAT);
    $stmt->bindValue(':payer_name', $data['payerName'], SQLITE3_TEXT);
    $stmt->bindValue(':payer_email', $data['payerEmail'], SQLITE3_TEXT);
    $stmt->bindValue(':payer_phone', $data['payerPhone'], SQLITE3_TEXT);
    $stmt->bindValue(':shipping_address', json_encode($data['shippingAddress']), SQLITE3_TEXT);
    $stmt->bindValue(':payment_method', $data['paymentMethod'], SQLITE3_TEXT);
    $stmt->bindValue(':payment_status', 'completed', SQLITE3_TEXT);

    $result = $stmt->execute();
    $orderId = $db->lastInsertRowID();

    // Move cart items to order items
    $stmt = $db->prepare('
        INSERT INTO order_items (
            order_id,
            product_id,
            quantity,
            price
        )
        SELECT
            :order_id,
            cart.product_id,
            cart.quantity,
            products.sale_price
        FROM cart
        JOIN products ON cart.product_id = products.id
        WHERE cart.session_id = :session_id
    ');

    $stmt->bindValue(':order_id', $orderId, SQLITE3_INTEGER);
    $stmt->bindValue(':session_id', session_id(), SQLITE3_TEXT);
    $stmt->execute();

    // Clear the cart
    $stmt = $db->prepare('DELETE FROM cart WHERE session_id = :session_id');
    $stmt->bindValue(':session_id', session_id(), SQLITE3_TEXT);
    $stmt->execute();

    $db->exec('COMMIT');

    echo json_encode([
        'success' => true,
        'orderId' => $orderId
    ]);

} catch (Exception $e) {
    $db->exec('ROLLBACK');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Payment processing failed: ' . $e->getMessage()
    ]);
}