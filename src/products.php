<?php
session_start();

// Connect to SQLite database
$db = new SQLite3('products.db');

// Fetch all products
$results = $db->query('SELECT name, sale_price, url_slug FROM products');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <style>
        .product-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            padding: 20px;
        }

        @media (min-width: 768px) {
            .product-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .product-card {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .product-card:hover {
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .product-name {
            font-size: 1.2em;
            margin-bottom: 10px;
        }

        .product-price {
            font-weight: bold;
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="product-grid">
        <?php while ($product = $results->fetchArray(SQLITE3_ASSOC)): ?>
            <a href="view.php?id=<?= htmlspecialchars($product['url_slug']) ?>" class="product-card">
                <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                <div class="product-price"><?= number_format($product['sale_price'], 2) ?> €</div>
            </a>
        <?php endwhile; ?>
    </div>
</body>
</html>
  