<?php
try {
    // Create/Connect to SQLite database
    $db = new SQLite3('products.db');

    // Create cart table
    $db->exec('CREATE TABLE IF NOT EXISTS cart (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        session_id VARCHAR(128) NOT NULL,
        product_id INTEGER,
        quantity INTEGER,
        FOREIGN KEY (product_id) REFERENCES products(id)
    )');

    // Create products table
    $db->exec('CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        url_slug VARCHAR(255) NOT NULL,
        name TEXT NOT NULL,
        regular_price DECIMAL(10,2) NOT NULL,
        sale_price DECIMAL(10,2) NOT NULL,
        description TEXT,
        availability TEXT CHECK(availability IN 
            ("sofort lieferbar",
             "lieferbar in 1-3 Werktagen",
             "lieferbar in 4-7 Werktagen",
             "verzögert lieferbar",
             "nicht lieferbar"))
    )');

    // Sample product data
    $products = [
        [generateUrlSlug('Laptop Pro'), 'Laptop Pro', 1299.99, 1199.99, 'High-performance laptop', 'sofort lieferbar'],
        [generateUrlSlug('Wireless Mouse'), 'Wireless Mouse', 29.99, 24.99, 'Ergonomic wireless mouse', 'sofort lieferbar'],
        [generateUrlSlug('Gaming Monitor'), 'Gaming Monitor', 499.99, 449.99, '27-inch 4K display', 'lieferbar in 1-3 Werktagen'],
        [generateUrlSlug('Premium T-Shirt'), 'Premium T-Shirt', 29.99, 24.99, 'Hochwertiges T-Shirt aus 100% Baumwolle', 'sofort lieferbar'],
        [generateUrlSlug('Vintage Jeans'), 'Vintage Jeans', 89.99, 89.99, 'Klassische Jeans im Vintage-Look', 'sofort lieferbar'],
        [generateUrlSlug('Sport Sneaker'), 'Sport Sneaker', 79.99, 69.99, 'Leichte Sneaker für optimalen Komfort', 'sofort lieferbar'],
        [generateUrlSlug('Test Product'), 'Test Product', 0.01, 0.01, 'A test product with minimal price', 'sofort lieferbar'],
        // Add more products to reach 20 items
    ];

    // Insert products
    $stmt = $db->prepare('INSERT INTO products (url_slug, name, regular_price, sale_price, description, availability) 
                         VALUES (:url_slug, :name, :regular_price, :sale_price, :description, :availability)');

    foreach ($products as $product) {
        $stmt->bindValue(':url_slug', $product[0], SQLITE3_TEXT);
        $stmt->bindValue(':name', $product[1], SQLITE3_TEXT);
        $stmt->bindValue(':regular_price', $product[2], SQLITE3_FLOAT);
        $stmt->bindValue(':sale_price', $product[3], SQLITE3_FLOAT);
        $stmt->bindValue(':description', $product[4], SQLITE3_TEXT);
        $stmt->bindValue(':availability', $product[5], SQLITE3_TEXT);
        $stmt->execute();
    }

    echo "Database setup completed successfully!";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

function generateUrlSlug($productName) {
    // Convert to lowercase and replace spaces with hyphens
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $productName)));
    return $slug;
}
?>