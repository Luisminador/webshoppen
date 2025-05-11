DELETE FROM cart_items;
DELETE FROM products;
DELETE FROM categories;

ALTER TABLE categories AUTO_INCREMENT = 1;
ALTER TABLE products AUTO_INCREMENT = 1;

INSERT INTO categories (name) VALUES 
('Ljus'),
('Tillbehör');

INSERT INTO products (title, description, price, deal_price, image_url, category_id, popularity_factor) VALUES 
('Vanilla Dream Doftljus', 'Lyxigt doftljus med en varm och söt vaniljdoft som skapar en inbjudande atmosfär', 299.00, NULL, 'https://placehold.co/400x300?text=Vanilla+Dream', 1, 95),
('Ocean Breeze Doftljus', 'Uppfriskande havsbris med inslag av citrus och saltstänk', 249.00, 199.00, 'https://placehold.co/400x300?text=Ocean+Breeze', 1, 88),
('Cinnamon Apple Doftljus', 'Hemtrevlig doft av nybakta äpplen och kanel', 279.00, NULL, 'https://placehold.co/400x300?text=Cinnamon+Apple', 1, 92),
('Lavender Fields Doftljus', 'Lugnande lavendeldoft inspirerad av franska blomsterfält', 259.00, NULL, 'https://placehold.co/400x300?text=Lavender+Fields', 1, 85),
('Fresh Pine Doftljus', 'Krispig barrdoft som påminner om en vintrig skogspromenad', 229.00, 179.00, 'https://placehold.co/400x300?text=Fresh+Pine', 1, 90),
('Coconut Paradise Doftljus', 'Exotisk kokos med inslag av vanilj och mandel', 269.00, NULL, 'https://placehold.co/400x300?text=Coconut+Paradise', 1, 87),

('Ljussläckare i Mässing', 'Elegant ljussläckare i polerad mässing med trähandtag', 149.00, NULL, 'https://placehold.co/400x300?text=Ljussläckare', 2, 82),
('Ljusstake i Kristall', 'Handgjord kristallljusstake för både kronljus och vanliga ljus', 399.00, 299.00, 'https://placehold.co/400x300?text=Kristallljusstake', 2, 94),
('Doftljushållare i Keramik', 'Handmålad keramikhållare i skandinavisk design', 199.00, NULL, 'https://placehold.co/400x300?text=Ljushållare', 2, 89),
('Tändstickor i Presentask', 'Långa tändstickor i dekorativ ask av återvunnet papper', 79.00, 59.00, 'https://placehold.co/400x300?text=Tändstickor', 2, 86); 