<?php
require_once 'includes/session.php';

// Connect to SQLite database
$db = new SQLite3('products.db');

// Fetch all products
$results = $db->query('SELECT name, sale_price, url_slug FROM products');

// Include the main header
include 'includes/header.php';
?>

<div class="product-grid">
    <?php while ($product = $results->fetchArray(SQLITE3_ASSOC)): ?>
        <a href="view.php?id=<?= htmlspecialchars($product['url_slug']) ?>" class="product-card">
            <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
            <div class="product-price"><?= number_format($product['sale_price'], 2) ?> €</div>
        </a>
    <?php endwhile; ?>
</div>

<style>
    .product-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
        padding: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    @media (min-width: 768px) {
        .product-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .product-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    .product-card {
        border: 1px solid #ddd;
        padding: 15px;
        border-radius: 8px;
        text-decoration: none;
        color: inherit;
        display: block;
        background: white;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .product-name {
        font-size: 1.2em;
        margin-bottom: 10px;
        color: #333;
    }

    .product-price {
        font-weight: bold;
        color: #007bff;
    }
</style>

<?php include 'includes/footer.php'; ?>
</body>
</html>