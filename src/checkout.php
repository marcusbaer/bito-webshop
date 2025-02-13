<?php
require_once 'includes/session.php';
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

// Include the checkout header
include 'includes/checkout-header.php';
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Bito Webshop</title>
    <link rel="stylesheet" href="/css/styles.css">
    
    <!-- PayPal SDK -->
    <script src="https://www.paypal.com/sdk/js?client-id=AZQt9GIKFIvyYGSCu_yBDxVggNIJGDVF-xGH6rRorQEZG8fPvRGSDiamYatIxrm1Cu9CFxYkMZtoSqKK&currency=EUR"></script>
</head>
<body>
    <main class="checkout-page">
        <div class="cart-items" id="cart-items">
            <h2>Ihre Bestellung</h2>
            <?php foreach ($items as $item): ?>
                <div class="cart-item" 
                     data-id="<?php echo htmlspecialchars($item['id']); ?>"
                     data-name="<?php echo htmlspecialchars($item['name']); ?>"
                     data-price="<?php echo htmlspecialchars($item['sale_price']); ?>"
                     data-quantity="<?php echo htmlspecialchars($item['quantity']); ?>">
                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                    <p>
                        Preis: €<?php echo number_format($item['sale_price'], 2); ?> x 
                        <?php echo $item['quantity']; ?> = 
                        €<?php echo number_format($item['sale_price'] * $item['quantity'], 2); ?>
                    </p>
                </div>
            <?php endforeach; ?>
            <div class="cart-total">
                <span>Gesamtsumme:</span>
                <span id="cart-total" data-total="<?php echo $total; ?>">
                    €<?php echo number_format($total, 2); ?>
                </span>
            </div>
        </div>

        <div class="payment-section">
            <h2>Zahlungsmethode wählen</h2>
            
            <!-- Payment error container -->
            <div id="payment-error-container" class="payment-error"></div>
            
            <!-- Payment methods container -->
            <div id="payment-methods-container" class="payment-methods">
                <!-- Payment buttons will be dynamically inserted here -->
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Payment handling -->
    <script type="module" src="/js/payment-handler.js"></script>

    <style>
        .checkout-page {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }

        .cart-items {
            margin-bottom: 2rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1.5rem;
        }

        .cart-item {
            border-bottom: 1px solid #eee;
            padding: 1rem 0;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-total {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 2px solid #eee;
            font-size: 1.2rem;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
        }

        .payment-section {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1.5rem;
        }

        .payment-error {
            display: none;
            background: #fff5f5;
            border: 1px solid #feb2b2;
            color: #c53030;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }

        .payment-methods {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .payment-button {
            padding: 1rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .googlepay-button {
            background: #000;
            color: #fff;
        }

        .applepay-button {
            background: #000;
            color: #fff;
        }

        .payment-form {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #eee;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-row {
            display: flex;
            gap: 1rem;
        }

        .form-row .form-group {
            flex: 1;
        }

        .submit-button {
            background: #4CAF50;
            color: white;
            padding: 1rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            margin-top: 1rem;
        }

        .submit-button:hover {
            background: #45a049;
        }

        #paypal-button-container {
            margin-top: 1rem;
        }
    </style>
</body>
</html>