<?php
$products = [
    [
        'id' => 1,
        'title' => 'ASOS DESIGN - Svart extra smal slips i satin',
        'category' => 'Accessoarer',
        'price' => 179.00,
        'image' => '/webshoppen/public/images/products/tie.jpg',
        'url' => '/webshoppen/public/product.php?id=1'
    ],
    [
        'id' => 2,
        'title' => 'ASOS DESIGN - Mattsvarta racer-solglasögon med omlottdesign',
        'category' => 'Accessoarer',
        'price' => 199.00,
        'deal_price' => 150.00,
        'image' => '/webshoppen/public/images/products/sunglasses.jpg',
        'url' => '/webshoppen/public/product.php?id=2',
        'variants' => ['FLER FÄRGER']
    ],
    [
        'id' => 3,
        'title' => 'ASOS DESIGN - Vitt armband med fuskpärlor, 6 mm',
        'category' => 'Accessoarer',
        'price' => 119.00,
        'image' => '/webshoppen/public/images/products/bracelet.jpg',
        'url' => '/webshoppen/public/product.php?id=3'
    ],
    [
        'id' => 4,
        'title' => 'Reclaimed Vintage - Unisex - Svart keps med logga',
        'category' => 'Accessoarer',
        'price' => 189.00,
        'image' => '/webshoppen/public/images/products/cap.jpg',
        'url' => '/webshoppen/public/product.php?id=4',
        'variants' => ['FLER FÄRGER', 'SÄLJER SNABBT']
    ]
];

function getProducts() {
    global $products;
    return $products;
}

function getProductById($id) {
    global $products;
    foreach ($products as $product) {
        if ($product['id'] === $id) {
            return $product;
        }
    }
    return null;
}
?> 