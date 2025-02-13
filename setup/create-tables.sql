-- Create products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2),
    url_slug VARCHAR(255) NOT NULL UNIQUE,
    availability VARCHAR(50) DEFAULT 'sofort lieferbar'
);

-- Insert sample products
INSERT INTO products (name, description, price, sale_price, url_slug, availability) VALUES
('Premium T-Shirt', 'Hochwertiges T-Shirt aus 100% Baumwolle', 29.99, 24.99, 'premium-t-shirt', 'sofort lieferbar'),
('Vintage Jeans', 'Klassische Jeans im Vintage-Look', 89.99, NULL, 'vintage-jeans', 'sofort lieferbar'),
('Sport Sneaker', 'Leichte Sneaker für optimalen Komfort', 79.99, 69.99, 'sport-sneaker', 'sofort lieferbar'),
('Test Product', 'A test product with minimal price', 0.01, 0.01, 'test-product', 'sofort lieferbar');

-- Create cart table
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Create orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create order_items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);