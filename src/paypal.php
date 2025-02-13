<?php
require_once 'includes/session.php';
$sessionId = session_id();

// Connect to database
$db = new SQLite3('products.db');

// Calculate total amount from cart
$stmt = $db->prepare('
    SELECT SUM(products.sale_price * cart.quantity) as total
    FROM cart
    JOIN products ON cart.product_id = products.id
    WHERE cart.session_id = :session_id
');
$stmt->bindValue(':session_id', $sessionId, SQLITE3_TEXT);
$result = $stmt->execute();
$total = $result->fetchArray(SQLITE3_ASSOC)['total'];

// PayPal Configuration
$paypal_config = [
    'client_id' => 'YOUR_PAYPAL_CLIENT_ID',
    'client_secret' => 'YOUR_PAYPAL_SECRET',
    'environment' => 'sandbox' // Change to 'production' for live
];

// Add PayPal JavaScript SDK
?>
<!DOCTYPE html>
<html>
<head>
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo $paypal_config['client_id']; ?>&currency=EUR"></script>
</head>
<body>
    <div id="paypal-button-container"></div>
    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?php echo $total; ?>'
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    window.location.href = 'order-confirmation.php?order_id=' + details.id;
                });
            }
        }).render('#paypal-button-container');
    </script>
</body>
</html>
  