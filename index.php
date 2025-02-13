<?php
include 'includes/session.php';
include 'includes/header.php';
?>

<main class="container mx-auto px-4 py-8">
    <!-- Hero Section -->
    <section class="text-center py-12 bg-gray-50 rounded-lg mb-12">
        <h1 class="text-4xl font-bold mb-4">Willkommen bei Bito Webshop</h1>
        <p class="text-xl text-gray-600 mb-8">Entdecken Sie unsere ausgewählten Produkte zu unschlagbaren Preisen!</p>
        <a href="products.php" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition-colors">
            Jetzt einkaufen
        </a>
    </section>

    <!-- Value Propositions -->
    <section class="grid md:grid-cols-3 gap-8 mb-12">
        <div class="text-center p-6 bg-white rounded-lg shadow-sm">
            <h3 class="text-xl font-semibold mb-2">Kostenloser Versand</h3>
            <p class="text-gray-600">Für alle Bestellungen über 50€</p>
        </div>
        <div class="text-center p-6 bg-white rounded-lg shadow-sm">
            <h3 class="text-xl font-semibold mb-2">Sichere Bezahlung</h3>
            <p class="text-gray-600">Verschiedene Zahlungsmöglichkeiten</p>
        </div>
        <div class="text-center p-6 bg-white rounded-lg shadow-sm">
            <h3 class="text-xl font-semibold mb-2">24/7 Support</h3>
            <p class="text-gray-600">Wir sind immer für Sie da</p>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="mb-12">
        <h2 class="text-3xl font-bold mb-8 text-center">Neueste Produkte</h2>
        <div class="grid md:grid-cols-2 gap-8">
            <?php
            require_once 'includes/db.php';

            // Get the two most recent products
            $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 2");
            while ($product = $stmt->fetch()) {
                $regularPrice = number_format($product['regular_price'], 2, ',', '.');
                $salePrice = $product['sale_price'] ? number_format($product['sale_price'], 2, ',', '.') : null;
                ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">
                            <a href="view.php?product=<?php echo htmlspecialchars($product['url_slug']); ?>" 
                               class="text-blue-600 hover:text-blue-800">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </a>
                        </h3>
                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($product['description']); ?></p>
                        <div class="flex justify-between items-center">
                            <div class="text-lg">
                                <?php if ($salePrice): ?>
                                    <span class="text-red-600 font-bold"><?php echo $salePrice; ?> €</span>
                                    <span class="text-gray-400 line-through ml-2"><?php echo $regularPrice; ?> €</span>
                                <?php else: ?>
                                    <span class="font-bold"><?php echo $regularPrice; ?> €</span>
                                <?php endif; ?>
                            </div>
                            <a href="view.php?product=<?php echo htmlspecialchars($product['url_slug']); ?>" 
                               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                                Details
                            </a>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        <div class="text-center mt-8">
            <a href="products.php" class="inline-block bg-gray-800 text-white px-8 py-3 rounded-lg hover:bg-gray-900 transition-colors">
                Alle Produkte ansehen
            </a>
        </div>
    </section>

    <!-- Marketing Banner -->
    <section class="bg-blue-50 p-8 rounded-lg text-center">
        <h2 class="text-2xl font-bold mb-4">Warum Bito Webshop?</h2>
        <p class="text-gray-600 mb-6">
            Bei uns finden Sie hochwertige Produkte zu fairen Preisen. 
            Unser Kundenservice steht Ihnen jederzeit zur Verfügung, 
            und wir garantieren eine schnelle und sichere Lieferung.
        </p>
        <p class="text-gray-600 mb-6">
            ✓ Schneller Versand<br>
            ✓ Sichere Bezahlung<br>
            ✓ 14 Tage Rückgaberecht<br>
            ✓ Beste Qualität
        </p>
    </section>
</main>

<?php include 'includes/footer.php'; ?>