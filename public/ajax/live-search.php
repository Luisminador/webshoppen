<?php
require_once '../../includes/functions.php';
require_once '../../includes/db.php';

// Hämta sökfrågan
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

if (strlen($search) >= 2) {
    // Utför sökningen
    $products = searchProducts($search);
    
    // Begränsa till max 5 resultat för live-sökning
    $products = array_slice($products, 0, 5);
    
    // Formatera resultaten
    $results = array_map(function($product) {
        return [
            'id' => $product['id'],
            'title' => $product['title'],
            'price' => number_format($product['price'], 0, ',', ' '),
            'category' => $product['category_name'],
            'image_url' => $product['image_url'],
            'url' => '/webshoppen/public/product.php?id=' . $product['id'],
            'deal_price' => isset($product['deal_price']) ? number_format($product['deal_price'], 0, ',', ' ') : null
        ];
    }, $products);
    
    header('Content-Type: application/json');
    echo json_encode($results);
} else {
    header('Content-Type: application/json');
    echo json_encode([]);
} 