<?php
require_once __DIR__ . '/db.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit();
    }
}

function getPopularProducts($limit = 10) {
    global $pdo;
    $stmt = $pdo->prepare('
        SELECT * FROM products 
        ORDER BY popularity_factor DESC 
        LIMIT ?
    ');
    $stmt->execute([$limit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProductsByCategory($categoryId) {
    global $pdo;
    $stmt = $pdo->prepare('
        SELECT * FROM products 
        WHERE category_id = ?
        ORDER BY created_at DESC
    ');
    $stmt->execute([$categoryId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProductById($id) {
    global $pdo;
    $stmt = $pdo->prepare('
        SELECT p.*, c.name as category_name 
        FROM products p
        JOIN categories c ON p.category_id = c.category_id
        WHERE p.id = ?
    ');
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function searchProducts($query, $sort = 'title', $order = 'ASC') {
    global $pdo;
    $searchTerm = "%$query%";
    $validSorts = ['title', 'price'];
    $validOrders = ['ASC', 'DESC'];
    
    $sort = in_array($sort, $validSorts) ? $sort : 'title';
    $order = in_array($order, $validOrders) ? $order : 'ASC';
    
    $stmt = $pdo->prepare("
        SELECT p.*, c.name as category_name 
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.title LIKE ? OR p.description LIKE ?
        ORDER BY p.$sort $order
    ");
    $stmt->execute([$searchTerm, $searchTerm]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllCategories() {
    global $pdo;
    $stmt = $pdo->query('SELECT * FROM categories ORDER BY name');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function sanitize($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function getCartItems($pdo, $userId) {
    $stmt = $pdo->prepare('
        SELECT cart_items.*, products.name, products.price 
        FROM cart_items 
        JOIN products ON cart_items.product_id = products.id 
        WHERE cart_items.user_id = ?
    ');
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function calculateCartTotal($cartItems) {
    return array_reduce($cartItems, function($total, $item) {
        return $total + ($item['price'] * $item['quantity']);
    }, 0);
}

function flashMessage($message, $type = 'success') {
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type
    ];
}

function displayFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = isset($_SESSION['flash_type']) ? $_SESSION['flash_type'] : 'info';
        
        // Rensa flash-meddelandet
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        
        return "<div class='flash-message {$type}'>{$message}</div>";
    }
    return '';
}

function setFlashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

function getCategoryById($id) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getProductsByCategory($categoryId, $sort = 'title', $order = 'ASC') {
    global $pdo;
    
    // Validera sorteringsparametrar
    $validSorts = ['title', 'price'];
    $validOrders = ['ASC', 'DESC'];
    
    $sort = in_array($sort, $validSorts) ? $sort : 'title';
    $order = in_array($order, $validOrders) ? $order : 'ASC';
    
    $stmt = $pdo->prepare("
        SELECT p.*, c.name as category_name 
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.category_id = ?
        ORDER BY p.$sort $order
    ");
    $stmt->execute([$categoryId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
} 