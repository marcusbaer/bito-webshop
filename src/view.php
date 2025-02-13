<?php
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

// Include the main header
include 'includes/header.php';
?>

<div class="product-detail">
    <div class="product-content">
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>

        <div class="pricing">
            <?php if ($product['regular_price'] > $product['sale_price']): ?>
                <p class="regular-price">Regulärer Preis: <span>€<?php echo number_format($product['regular_price'], 2); ?></span></p>
            <?php endif; ?>
            <p class="sale-price">Preis: <span>€<?php echo number_format($product['sale_price'], 2); ?></span></p>
        </div>

        <div class="description">
            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
        </div>

        <div class="availability">
            <p>Verfügbarkeit: <span><?php echo htmlspecialchars($product['availability']); ?></span></p>
        </div>

        <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
            <div class="quantity-input">
                <label for="quantity">Menge:</label>
                <input type="number" id="quantity" name="quantity" value="1" min="1">
            </div>
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <button type="submit" class="add-to-cart-button">In den Warenkorb</button>
        </form>
    </div>
</div>

<style>
    .product-detail {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .product-content {
        background: white;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .product-detail h1 {
        color: #333;
        margin: 0 0 20px 0;
        font-size: 2em;
    }

    .pricing {
        margin-bottom: 30px;
    }

    .regular-price {
        color: #666;
        text-decoration: line-through;
        margin: 0;
    }

    .regular-price span {
        font-weight: bold;
    }

    .sale-price {
        color: #007bff;
        font-size: 1.5em;
        margin: 5px 0;
    }

    .sale-price span {
        font-weight: bold;
    }

    .description {
        margin: 20px 0;
        line-height: 1.6;
        color: #444;
    }

    .availability {
        margin: 20px 0;
        padding: 10px 0;
        border-top: 1px solid #eee;
        border-bottom: 1px solid #eee;
    }

    .availability span {
        font-weight: bold;
        color: #28a745;
    }

    .add-to-cart-form {
        margin-top: 30px;
        display: flex;
        gap: 15px;
        align-items: center;
    }

    .quantity-input {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .quantity-input label {
        color: #666;
    }

    .quantity-input input {
        width: 80px;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1em;
    }

    .add-to-cart-button {
        background: #007bff;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1em;
        transition: background-color 0.2s;
    }

    .add-to-cart-button:hover {
        background: #0056b3;
    }

    @media (max-width: 768px) {
        .product-detail {
            padding: 15px;
        }

        .product-content {
            padding: 20px;
        }

        .add-to-cart-form {
            flex-direction: column;
            align-items: stretch;
        }

        .quantity-input {
            justify-content: space-between;
        }

        .add-to-cart-button {
            width: 100%;
        }
    }
</style>
</body>
</html>