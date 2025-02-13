<?php
session_start();

if (!isset($_GET['order_id'])) {
    header('Location: cart.php');
    exit;
}

$orderId = $_GET['order_id'];
$db = new SQLite3('products.db');

// Get order details
$stmt = $db->prepare('
    SELECT * FROM orders 
    WHERE id = :order_id
');
$stmt->bindValue(':order_id', $orderId, SQLITE3_INTEGER);
$order = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

if (!$order) {
    header('Location: cart.php');
    exit;
}

// Get order items
$stmt = $db->prepare('
    SELECT order_items.*, products.name 
    FROM order_items 
    JOIN products ON order_items.product_id = products.id
    WHERE order_items.order_id = :order_id
');
$stmt->bindValue(':order_id', $orderId, SQLITE3_INTEGER);
$items = $stmt->execute();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bestellung erfolgreich</title>
    <meta charset="UTF-8">
    <style>
        .success-message {
            text-align: center;
            margin: 2em auto;
            max-width: 600px;
            padding: 2em;
            background-color: #f8fff8;
            border: 1px solid #4CAF50;
            border-radius: 4px;
        }
        .order-details {
            margin: 2em auto;
            max-width: 600px;
            padding: 1em;
            border: 1px solid #ddd;
        }
        .order-item {
            padding: 0.5em 0;
            border-bottom: 1px solid #eee;
        }
        .order-total {
            margin-top: 1em;
            text-align: right;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="success-message">
        <h1>🎉 Bestellung erfolgreich!</h1>
        <p>Vielen Dank für Ihre Bestellung. Ihre Bestellnummer lautet: #<?php echo $orderId; ?></p>
    </div>

    <div class="order-details">
        <h2>Bestellübersicht</h2>
        <?php while ($item = $items->fetchArray(SQLITE3_ASSOC)): ?>
            <div class="order-item">
                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                <p>
                    Anzahl: <?php echo $item['quantity']; ?><br>
                    Preis: €<?php echo number_format($item['price'], 2); ?>
                </p>
            </div>
        <?php endwhile; ?>
        <div class="order-total">
            Gesamtsumme: €<?php echo number_format($order['total_amount'], 2); ?>
        </div>

        <h2>Versanddetails</h2>
        <?php 
        $shippingAddress = json_decode($order['shipping_address'], true);
        ?>
        <p>
            Name: <?php echo htmlspecialchars($order['payer_name']); ?><br>
            Email: <?php echo htmlspecialchars($order['payer_email']); ?><br>
            Telefon: <?php echo htmlspecialchars($order['payer_phone']); ?><br>
            Adresse: <?php echo htmlspecialchars($shippingAddress['addressLine1']); ?><br>
            PLZ: <?php echo htmlspecialchars($shippingAddress['postalCode']); ?><br>
            Stadt: <?php echo htmlspecialchars($shippingAddress['city']); ?>
        </p>
    </div>

    <div style="text-align: center; margin: 2em;">
        <a href="products.php">Zurück zum Shop</a>
    </div>
</body>
</html>