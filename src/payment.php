<?php
session_start();
$sessionId = session_id();

// Get cart total from database
$db = new SQLite3('products.db');
$stmt = $db->prepare('
    SELECT SUM(products.sale_price * cart.quantity) as total
    FROM cart
    JOIN products ON cart.product_id = products.id
    WHERE cart.session_id = :session_id
');
$stmt->bindValue(':session_id', $sessionId, SQLITE3_TEXT);
$result = $stmt->execute();
$total = $result->fetchArray(SQLITE3_ASSOC)['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment</title>
</head>
<body>
    <div id="payment-form">
        <h2>Payment Details</h2>
        <div id="payment-element"></div>
        <button id="submit-payment">Pay €<?php echo number_format($total, 2); ?></button>
    </div>

    <script>
        // Payment API implementation
        const paymentRequest = new PaymentRequest(
            [{
                supportedM
                    supportedNetworks: ['visa', 'mastercard'],
                    supportedTypes: ['credit']
                }
            }],
            {
                total: {
                    label: 'Total',
                    amount: {
                        currency: 'EUR',
                        value: '<?php echo $total; ?>'
                    }
                }
            }
        );

        document.getElementById('submit-payment').addEventListener('click', async () => {
            try {
                const paymentResponse = await paymentRequest.show();
                // Process the payment
                await paymentResponse.complete('success');
                // Redirect to success page
                window.location.href = 'payment-success.php';
            } catch (err) {
                console.error('Payment failed:', err);
            }
        });
    </script>
</body>
</html>
  