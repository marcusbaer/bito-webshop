<?php
session_start();
$sessionId = session_id();

// Validate if cart has items before checkout
$db = new SQLite3('products.db');
$stmt = $db->prepare('SELECT COUNT(*) as count FROM cart WHERE session_id = :session_id');
$stmt->bindValue(':session_id', $sessionId, SQLITE3_TEXT);
$result = $stmt->execute();
$count = $result->fetchArray(SQLITE3_ASSOC)['count'];

if ($count == 0) {
    header('Location: cart.php');
    exit;
}

// Get cart items with product details
$stmt = $db->prepare('
    SELECT products.*, cart.quantity 
    FROM cart
    JOIN products ON cart.product_id = products.id
    WHERE cart.session_id = :session_id
');
$stmt->bindValue(':session_id', $sessionId, SQLITE3_TEXT);
$cartItems = $stmt->execute();

// Calculate total
$total = 0;
$items = [];
while ($item = $cartItems->fetchArray(SQLITE3_ASSOC)) {
    $items[] = $item;
    $total += $item['sale_price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <meta charset="UTF-8">
    <style>
        .cart-items {
            margin-bottom: 2em;
            border: 1px solid #ddd;
            padding: 1em;
        }
        .cart-item {
            border-bottom: 1px solid #eee;
            padding: 0.5em 0;
        }
        .cart-total {
            font-weight: bold;
            margin-top: 1em;
            text-align: right;
        }
    </style>
</head>
<body>
    <h1>Checkout</h1>

    <div class="cart-items">
        <h2>Ihre Bestellung</h2>
        <?php foreach ($items as $item): ?>
            <div class="cart-item">
                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                <p>
                    Preis: €<?php echo number_format($item['sale_price'], 2); ?> x 
                    <?php echo $item['quantity']; ?> = 
                    €<?php echo number_format($item['sale_price'] * $item['quantity'], 2); ?>
                </p>
            </div>
        <?php endforeach; ?>
        <div class="cart-total">
            Gesamtsumme: €<?php echo number_format($total, 2); ?>
        </div>
    </div>

    <form action="process_order.php" method="POST">
        <h2>Rechnungsadresse</h2>
        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div>
            <label for="street">Straße:</label>
            <input type="text" id="street" name="street" required>
        </div>
        <div>
            <label for="city">Stadt:</label>
            <input type="text" id="city" name="city" required>
        </div>
        <div>
            <label for="zip">PLZ:</label>
            <input type="text" id="zip" name="zip" required>
        </div>

        <h2>Zahlungsart</h2>
        <div>
            <select name="payment_method" required>
                <option value="invoice">Rechnung</option>
                <option value="paypal">PayPal</option>
                <option value="visa">VISA</option>
                <option value="mastercard">Mastercard</option>
            </select>
        </div>

        <button type="submit">Bestellung abschließen</button>
    </form>
</body>
</html>