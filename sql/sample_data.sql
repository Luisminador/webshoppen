USE webshoppen;

-- Lägg till några kategorier
INSERT INTO categories (name, description) VALUES
('Elektronik', 'Datorer, mobiler och andra elektroniska produkter'),
('Böcker', 'Fysiska böcker och e-böcker'),
('Kläder', 'Herr- och damkläder');

-- Lägg till några produkter
INSERT INTO products (category_id, name, description, price, stock, image_url) VALUES
(1, 'Gaming Laptop', 'Kraftfull laptop för gaming med RTX 3080', 15999.00, 5, '/uploads/laptop.jpg'),
(1, 'Smartphone', 'Senaste modellen med 5G-stöd', 8999.00, 10, '/uploads/phone.jpg'),
(2, 'PHP för nybörjare', 'Lär dig PHP från grunden', 299.00, 20, '/uploads/php-book.jpg'),
(2, 'MySQL Handbok', 'Komplett guide till MySQL', 399.00, 15, '/uploads/mysql-book.jpg'),
(3, 'T-shirt', 'Bekväm t-shirt i 100% bomull', 199.00, 50, '/uploads/tshirt.jpg'),
(3, 'Jeans', 'Klassiska blå jeans', 599.00, 30, '/uploads/jeans.jpg'); 