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

// Include the main header
include 'includes/header.php';
?>

<div class="cart-container">
    <h1>Warenkorb</h1>
    
    <div id="message" class="message"></div>

    <div class="cart-items">
        <?php 
        $hasItems = false;
        while ($item = $result->fetchArray(SQLITE3_ASSOC)):
            $hasItems = true;
        ?>
            <div class="cart-item" data-product-id="<?php echo $item['id']; ?>">
                <div class="cart-item-info">
                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                    <div class="cart-item-details">
                        <p class="item-price">Preis: <span>€<?php echo number_format($item['sale_price'], 2); ?></span></p>
                        <p class="item-quantity">Menge: <span><?php echo $item['quantity']; ?></span></p>
                        <p class="item-subtotal">Zwischensumme: <span>€<?php echo number_format($item['sale_price'] * $item['quantity'], 2); ?></span></p>
                    </div>
                </div>
                <button class="delete-button" onclick="deleteCartItem(<?php echo $item['id']; ?>)">
                    Entfernen
                </button>
            </div>
        <?php endwhile; ?>

        <?php if (!$hasItems): ?>
            <div class="empty-cart">
                <p>Ihr Warenkorb ist leer</p>
                <a href="products.php" class="continue-shopping">Weiter einkaufen</a>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($hasItems): ?>
        <div class="cart-summary">
            <div class="cart-total">
                Gesamtsumme: <span id="cart-total">€<?php echo number_format($total, 2); ?></span>
            </div>

            <div class="checkout-section">
                <a href="products.php" class="continue-shopping">Weiter einkaufen</a>
                <a href="checkout.php" class="checkout-button">
                    Zur Kasse
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .cart-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .cart-container h1 {
        color: #333;
        margin-bottom: 30px;
    }

    .message {
        padding: 15px;
        margin: 10px 0;
        border-radius: 4px;
        display: none;
    }

    .message.success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .message.error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .cart-items {
        margin-bottom: 30px;
    }

    .cart-item {
        background: white;
        border: 1px solid #eee;
        padding: 20px;
        margin-bottom: 15px;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: box-shadow 0.2s;
    }

    .cart-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .cart-item-info {
        flex-grow: 1;
    }

    .cart-item h3 {
        margin: 0 0 15px 0;
        color: #333;
    }

    .cart-item-details {
        display: grid;
        gap: 8px;
        color: #666;
    }

    .cart-item-details span {
        color: #333;
        font-weight: 500;
    }

    .delete-button {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.2s;
        font-size: 0.9em;
    }

    .delete-button:hover {
        background-color: #c82333;
    }

    .cart-summary {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .cart-total {
        font-size: 1.3em;
        color: #333;
        margin-bottom: 20px;
        text-align: right;
    }

    .cart-total span {
        font-weight: bold;
        color: #007bff;
    }

    .checkout-section {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        align-items: center;
    }

    .continue-shopping {
        padding: 12px 24px;
        color: #007bff;
        text-decoration: none;
        border: 1px solid #007bff;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .continue-shopping:hover {
        background: #f8f9fa;
    }

    .checkout-button {
        padding: 12px 30px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        transition: background-color 0.2s;
    }

    .checkout-button:hover {
        background-color: #0056b3;
    }

    .empty-cart {
        text-align: center;
        padding: 40px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .empty-cart p {
        color: #666;
        font-size: 1.2em;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .cart-container {
            padding: 15px;
        }

        .cart-item {
            flex-direction: column;
            gap: 15px;
        }

        .cart-item-info {
            width: 100%;
        }

        .delete-button {
            width: 100%;
        }

        .checkout-section {
            flex-direction: column;
            gap: 10px;
        }

        .continue-shopping,
        .checkout-button {
            width: 100%;
            text-align: center;
        }
    }
</style>

<script src="js/cart.js"></script>
</body>
</html>