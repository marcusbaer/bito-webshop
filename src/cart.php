<?php
session_start();
$sessionId = session_id();


$db = new SQLite3('products.db');
$stmt = $db->prepare('
    SELECT products.*, cart.quantity 
    FROM cart
    JOIN products ON cart.product_id = products.id
    WHERE cart.session_id = :session_id
');
$stmt->bindValue(':session_id', $sessionId, SQLITE3_TEXT);
$result = $stmt->execute();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Warenkorb</title>
</head>

<body>
    <h1>Warenkorb</h1>
    <?php while ($item = $result->fetchArray(SQLITE3_ASSOC)): ?>
        <div class="cart-item">
            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
            <p>Preis: €<?php echo number_format($item['sale_price'], 2); ?></p>
            <p>Menge: <?php echo $item['quantity']; ?></p>
        </div>
    <?php endwhile; ?>

    <div class="checkout-section">
        <a href="checkout.php" class="checkout-button">
            Zum Checkout
        </a>
        <a href="payment.php" class="checkout-button">Proceed to Payment</a>
    </div>
</body>

</html>