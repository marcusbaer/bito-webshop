<?php
session_start();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Bito Webshop</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .checkout-header {
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        .checkout-header-content {
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
        .back-to-cart {
            color: #007bff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .back-to-cart:hover {
            text-decoration: underline;
        }
        .back-to-cart::before {
            content: "←";
        }
        @media (max-width: 768px) {
            .checkout-header-content {
                padding: 0 1rem;
            }
        }
    </style>
</head>
<body>
    <header class="checkout-header">
        <div class="checkout-header-content">
            <span class="logo">Bito Webshop</span>
            <a href="/cart.php" class="back-to-cart">
                Zurück zum Warenkorb
            </a>
        </div>
    </header>
    <div style="margin-top: 80px;"><!-- Spacer for fixed header --></div>