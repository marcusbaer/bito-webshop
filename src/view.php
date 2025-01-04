<?php
// product-detail.php
session_start();

// Get the Product ID from the request
$urlSlug = $_GET['id'] ?? '';

// Connect to the database
$db = new SQLite3('products.db');

// Prepare and execute query to fetch product
$stmt = $db->prepare('SELECT * FROM products WHERE url_slug = :url_slug');
$stmt->bindValue(':url_slug', $urlSlug, SQLITE3_TEXT);
$result = $stmt->execute();
$product = $result->fetchArray(SQLITE3_ASSOC);

// Check if product exists
if (!$product) {
    http_response_code(404);
    die('Product not found');
}
?>

<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($product['name']); ?></title>
    <meta charset="UTF-8">
</head>

<body>
    <div class="product-detail">
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>

        <div class="pricing">
            <p class="regular-price">Regulärer Preis: €<?php echo number_format($product['regular_price'], 2); ?></p>
            <p class="sale-price">Angebotspreis: €<?php echo number_format($product['sale_price'], 2); ?></p>
        </div>

        <div class="description">
            <p><?php echo htmlspecialchars($product['description']); ?></p>
        </div>

        <div class="availability">
            <p>Verfügbarkeit: <?php echo htmlspecialchars($product['availability']); ?></p>
        </div>
    </div>
    <form action="add_to_cart.php" method="POST">
        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
        <input type="number" name="quantity" value="1" min="1">
        <button type="submit">In den Warenkorb</button>
    </form>
</body>

</html>