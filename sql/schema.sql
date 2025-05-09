-- Skapa databas om den inte finns
CREATE DATABASE IF NOT EXISTS webshoppen;
USE webshoppen;

-- Radera existerande tabeller om de finns
DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

-- Skapa categories tabell
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Skapa products tabell
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    deal_price DECIMAL(10, 2),
    image_url VARCHAR(255),
    category_id INT NOT NULL,
    popularity_factor INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Skapa users tabell
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Skapa cart_items tabell
CREATE TABLE cart_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Lägg till exempelkategorier
INSERT INTO categories (name) VALUES 
('Accessoarer'),
('Kläder'),
('Skor'),
('Väskor');

-- Lägg till exempelprodukter
INSERT INTO products (title, description, price, deal_price, image_url, category_id, popularity_factor) VALUES 
('ASOS DESIGN - Svart extra smal slips i satin', 'En elegant svart slips perfekt för formella tillfällen', 179.00, NULL, '/webshoppen/public/images/products/tie.jpg', 1, 85),
('ASOS DESIGN - Mattsvarta racer-solglasögon', 'Snygga solglasögon med modern design', 199.00, 150.00, '/webshoppen/public/images/products/sunglasses.jpg', 1, 95),
('ASOS DESIGN - Vitt armband med fuskpärlor', 'Elegant armband med 6 mm fuskpärlor', 119.00, NULL, '/webshoppen/public/images/products/bracelet.jpg', 1, 75),
('Reclaimed Vintage - Unisex - Svart keps', 'Stilren svart keps med broderad logga', 189.00, NULL, '/webshoppen/public/images/products/cap.jpg', 1, 90),
('ASOS DESIGN - Svart t-shirt', 'Klassisk svart t-shirt i bomull', 149.00, NULL, '/webshoppen/public/images/products/tshirt.jpg', 2, 100),
('Nike - Löparskor Air Zoom', 'Bekväma löparskor med bra stötdämpning', 999.00, 799.00, '/webshoppen/public/images/products/shoes.jpg', 3, 88),
('ASOS DESIGN - Jeansjacka', 'Trendig jeansjacka i klassisk design', 599.00, NULL, '/webshoppen/public/images/products/jacket.jpg', 2, 82),
('Adidas - Ryggsäck', 'Rymlig ryggsäck perfekt för vardagsbruk', 399.00, 299.00, '/webshoppen/public/images/products/backpack.jpg', 4, 93),
('ASOS DESIGN - Stickad tröja', 'Varm och mysig stickad tröja', 449.00, NULL, '/webshoppen/public/images/products/sweater.jpg', 2, 87),
('Puma - Träningsväska', 'Praktisk träningsväska med flera fack', 299.00, NULL, '/webshoppen/public/images/products/bag.jpg', 4, 79); 