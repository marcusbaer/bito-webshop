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

// Include the checkout header
include 'includes/checkout-header.php';
?>

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

<div id="payment-error"></div>
<button id="payment-button">Jetzt bezahlen</button>

<!-- Fallback payment form for unsupported browsers -->
<div class="fallback-payment" id="fallback-payment">
    <h2>Alternative Zahlungsmethode</h2>
    <p>Ihr Browser unterstützt leider keine moderne Zahlungsabwicklung. Bitte nutzen Sie das folgende Formular:</p>
    <form action="process_order.php" method="POST">
        <h3>Rechnungsadresse</h3>
        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div>
            <label for="email">E-Mail:</label>
            <input type="email" id="email" name="email" required>
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
        <button type="submit">Bestellung abschließen</button>
    </form>
</div>

<script src="js/payment-request.js"></script>
<script>
    // Initialize payment handling
    document.addEventListener('DOMContentLoaded', function() {
        const cartItems = <?php echo json_encode($items); ?>;
        const total = <?php echo $total; ?>;
        
        const payment = new WebshopPayment(cartItems, total);
        const paymentButton = document.getElementById('payment-button');
        const fallbackPayment = document.getElementById('fallback-payment');

        // Check for Payment Request API support
        if (window.PaymentRequest) {
            paymentButton.addEventListener('click', () => {
                payment.initializePayment().catch(error => {
                    console.error('Payment failed:', error);
                    fallbackPayment.style.display = 'block';
                });
            });
        } else {
            // Show fallback payment form if Payment Request API is not supported
            paymentButton.style.display = 'none';
            fallbackPayment.style.display = 'block';
        }
    });
</script>

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
    #payment-button {
        display: block;
        width: 100%;
        max-width: 300px;
        margin: 2em auto;
        padding: 1em;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1.1em;
    }
    #payment-button:hover {
        background-color: #45a049;
    }
    #payment-error {
        display: none;
        color: red;
        margin: 1em 0;
        padding: 1em;
        border: 1px solid red;
        background-color: #fff5f5;
    }
    .fallback-payment {
        display: none;
        margin-top: 2em;
        padding: 1em;
        border: 1px solid #ddd;
    }
</style>
</body>
</html>