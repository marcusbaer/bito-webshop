<?php
$current_page = basename($_SERVER['PHP_SELF']);
$cart_count = 0;

// Get cart count from session
require_once 'session.php';
if (isset($_SESSION['cart'])) {
    $cart_count = array_sum($_SESSION['cart']);
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bito Webshop</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .header {
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
            text-decoration: none;
        }
        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        .nav-links a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
        }
        .nav-links a:hover {
            color: #007bff;
        }
        .nav-links a.active {
            color: #007bff;
        }
        .cart-link {
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .cart-count {
            background: #007bff;
            color: white;
            border-radius: 50%;
            padding: 0.2rem 0.5rem;
            font-size: 0.8rem;
        }
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
            }
            .nav-links {
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="/products.php" class="logo">Bito Webshop</a>
            <nav class="nav-links">
                <a href="/products.php" <?php echo $current_page == 'products.php' ? 'class="active"' : ''; ?>>
                    Produkte
                </a>
                <a href="/cart.php" class="cart-link <?php echo $current_page == 'cart.php' ? 'active' : ''; ?>">
                    Warenkorb
                    <?php if ($cart_count > 0): ?>
                        <span class="cart-count"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>
            </nav>
        </div>
    </header>
    <div style="margin-top: 80px;"><!-- Spacer for fixed header --></div>