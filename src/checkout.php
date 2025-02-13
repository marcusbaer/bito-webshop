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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <meta charset="UTF-8">
</head>
<body>
    <h1>Checkout</h1>
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
  