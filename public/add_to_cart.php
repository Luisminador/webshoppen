<?php
require_once '../includes/functions.php';
require_once '../includes/db.php';

initSession();

if (!isLoggedIn()) {
    setFlashMessage('Du måste vara inloggad för att lägga till produkter i varukorgen.', 'warning');
    header('Location: ' . BASE_URL . '/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
    
    if (!$productId || !$quantity || $quantity < 1 || $quantity > 10) {
        setFlashMessage('Ogiltigt antal eller produkt.', 'error');
        header('Location: ' . $_SERVER['HTTP_REFERER'] ?? BASE_URL);
        exit();
    }
    
    try {
        $product = getProductById($productId);
        
        if ($product) {
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
                
                setFlashMessage('Antal uppdaterat i varukorgen.', 'success');
            } else {
                $stmt = $pdo->prepare('
                    INSERT INTO cart_items (user_id, product_id, quantity) 
                    VALUES (?, ?, ?)
                ');
                $stmt->execute([$_SESSION['user_id'], $productId, $quantity]);
                
                setFlashMessage($product['title'] . ' har lagts till i din varukorg.', 'success');
            }
            
            if (isset($_POST['buy_now']) && $_POST['buy_now'] === '1') {
                header('Location: ' . BASE_URL . '/cart.php');
                exit();
            }
        } else {
            setFlashMessage('Produkten kunde inte hittas.', 'error');
        }
    } catch (PDOException $e) {
        error_log('Cart error: ' . $e->getMessage());
        setFlashMessage('Ett fel uppstod. Försök igen senare.', 'error');
    }
}

header('Location: ' . $_SERVER['HTTP_REFERER'] ?? BASE_URL);
exit(); 