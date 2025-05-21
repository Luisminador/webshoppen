<?php
require_once '../includes/functions.php';
session_start();

if (!isLoggedIn()) {
    setFlashMessage('Du måste vara inloggad för att lägga till produkter i varukorgen.', 'error');
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    if ($quantity < 1 || $quantity > 10) {
        $quantity = 1;
    }
    
    $product = getProductById($productId);
    
    if ($product) {
        global $pdo;
        
        $stmt = $pdo->prepare('
            SELECT id, quantity 
            FROM cart_items 
            WHERE user_id = ? AND product_id = ?
        ');
        $stmt->execute([$_SESSION['user_id'], $productId]);
        $existingItem = $stmt->fetch();
        
        if ($existingItem) {
            $newQuantity = min(10, $existingItem['quantity'] + $quantity);
            $stmt = $pdo->prepare('
                UPDATE cart_items 
                SET quantity = ? 
                WHERE id = ?
            ');
            $stmt->execute([$newQuantity, $existingItem['id']]);
        } else {
            $stmt = $pdo->prepare('
                INSERT INTO cart_items (user_id, product_id, quantity) 
                VALUES (?, ?, ?)
            ');
            $stmt->execute([$_SESSION['user_id'], $productId, $quantity]);
        }
        
        setFlashMessage($product['title'] . ' har lagts till i din varukorg.', 'success');
    } else {
        setFlashMessage('Produkten kunde inte hittas.', 'error');
    }
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit(); 