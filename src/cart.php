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

// Calculate total
$stmt = $db->prepare('
    SELECT SUM(products.sale_price * cart.quantity) as total
    FROM cart
    JOIN products ON cart.product_id = products.id
    WHERE cart.session_id = :session_id
');
$stmt->bindValue(':session_id', $sessionId, SQLITE3_TEXT);
$totalResult = $stmt->execute();
$total = $totalResult->fetchArray(SQLITE3_ASSOC)['total'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Warenkorb</title>
    <style>
        .cart-item {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .cart-item-info {
            flex-grow: 1;
        }
        .delete-button {
            background-color: #ff4444;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .delete-button:hover {
            background-color: #cc0000;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            display: none;
        }
        .message.success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }
        .message.error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
        .checkout-section {
            margin-top: 20px;
            text-align: right;
        }
        .checkout-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-left: 10px;
        }
        .checkout-button:hover {
            background-color: #45a049;
        }
        .cart-total {
            font-size: 1.2em;
            font-weight: bold;
            margin: 20px 0;
            text-align: right;
        }
    </style>
</head>
<body>
    <h1>Warenkorb</h1>
    
    <div id="message" class="message"></div>

    <?php while ($item = $result->fetchArray(SQLITE3_ASSOC)): ?>
        <div class="cart-item" data-product-id="<?php echo $item['id']; ?>">
            <div class="cart-item-info">
                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                <p>Preis: €<?php echo number_format($item['sale_price'], 2); ?></p>
                <p>Menge: <?php echo $item['quantity']; ?></p>
                <p>Zwischensumme: €<?php echo number_format($item['sale_price'] * $item['quantity'], 2); ?></p>
            </div>
            <button class="delete-button" onclick="deleteCartItem(<?php echo $item['id']; ?>)">
                Entfernen
            </button>
        </div>
    <?php endwhile; ?>

    <div class="cart-total">
        Gesamtsumme: <span id="cart-total">€<?php echo number_format($total, 2); ?></span>
    </div>

    <div class="checkout-section">
        <a href="checkout.php" class="checkout-button">
            Zum Checkout
        </a>
    </div>

    <script src="js/cart.js"></script>
</body>
</html>