<?php
require_once __DIR__ . '/db.php';

function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Regenerera session-ID var 30:e minut, men bara om inga headers har skickats
    if (!headers_sent() && 
        (!isset($_SESSION['last_regeneration']) || 
        time() - $_SESSION['last_regeneration'] > 1800)) {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

function isLoggedIn() {
    initSession();
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['flash_message'] = "Du måste vara inloggad för att komma åt denna sida.";
        $_SESSION['flash_type'] = 'warning';
        header('Location: ' . BASE_URL . '/login.php');
        exit();
    }
}

function logout() {
    initSession();
    session_unset();
    session_destroy();
    setcookie(session_name(), '', time() - 3600, '/');
}

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    if (empty($_SESSION['csrf_token']) || empty($token) || 
        !hash_equals($_SESSION['csrf_token'], $token)) {
        error_log("CSRF verification failed");
        return false;
    }
    return true;
}

function getPopularProducts($limit = 10) {
    global $pdo;
    $stmt = $pdo->prepare('
        SELECT p.id, p.title, p.description, p.price, p.deal_price, p.image_url, 
               p.popularity_factor, p.created_at, c.name as category_name 
        FROM products p
        JOIN categories c ON p.category_id = c.id
        ORDER BY p.popularity_factor DESC 
        LIMIT :limit
    ');
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProductById($id) {
    global $pdo;
    $stmt = $pdo->prepare('
        SELECT p.*, c.name as category_name 
        FROM products p
        JOIN categories c ON p.category_id = c.id
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
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function getCartItems($userId) {
    global $pdo;
    $stmt = $pdo->prepare('
        SELECT cart_items.*, products.title, products.price, products.image_url 
        FROM cart_items 
        JOIN products ON cart_items.product_id = products.id 
        WHERE cart_items.user_id = ?
    ');
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function calculateCartTotal($cartItems) {
    return array_reduce($cartItems, function($total, $item) {
        $price = isset($item['deal_price']) && $item['deal_price'] > 0 ? $item['deal_price'] : $item['price'];
        return $total + ($price * $item['quantity']);
    }, 0);
}

function setFlashMessage($message, $type = 'info') {
    initSession();
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

function displayFlashMessage() {
    initSession();
    if (isset($_SESSION['flash_message'])) {
        $message = sanitize($_SESSION['flash_message']);
        $type = isset($_SESSION['flash_type']) ? sanitize($_SESSION['flash_type']) : 'info';
        
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        
        return "<div class='flash-message {$type}' role='alert'>{$message}</div>";
    }
    return '';
}

function getProductsByCategory($categoryId, $sort = 'title', $order = 'ASC') {
    global $pdo;
    
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

function getDiscountedProducts() {
    global $pdo;
    $stmt = $pdo->prepare('
        SELECT p.*, c.name as category_name 
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.deal_price IS NOT NULL 
        AND p.deal_price > 0 
        AND p.deal_price < p.price
        ORDER BY (p.price - p.deal_price) / p.price DESC
    ');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function sortProducts($products, $sort, $order) {
    if (empty($products)) {
        return $products;
    }

    usort($products, function($a, $b) use ($sort, $order) {
        $compareValue = 0;
        
        switch($sort) {
            case 'title':
                $compareValue = strcmp($a['title'], $b['title']);
                break;
            case 'price':
                $priceA = isset($a['deal_price']) && $a['deal_price'] > 0 ? $a['deal_price'] : $a['price'];
                $priceB = isset($b['deal_price']) && $b['deal_price'] > 0 ? $b['deal_price'] : $b['price'];
                $compareValue = $priceA - $priceB;
                break;
        }
        
        return $order === 'DESC' ? -$compareValue : $compareValue;
    });
    
    return $products;
}

function getLatestProducts($limit = 12) {
    global $pdo;
    $stmt = $pdo->prepare('
        SELECT p.*, c.name as category_name 
        FROM products p
        JOIN categories c ON p.category_id = c.id
        ORDER BY p.created_at DESC 
        LIMIT :limit
    ');
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getGiftSuggestions() {
    global $pdo;
    $stmt = $pdo->prepare('
        SELECT p.*, c.name as category_name 
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.popularity_factor >= 85
        ORDER BY RAND()
        LIMIT 12
    ');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCategoryById($id) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getUserById($id) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function updateUserProfile($userId, $data) {
    global $pdo;
    $allowedFields = ['firstname', 'lastname', 'phone', 'address', 'postcode', 'city'];
    $updates = [];
    $values = [];

    foreach ($data as $field => $value) {
        if (in_array($field, $allowedFields)) {
            $updates[] = "$field = ?";
            $values[] = $value;
        }
    }

    if (empty($updates)) {
        return false;
    }

    $values[] = $userId;
    $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($values);
    } catch (PDOException $e) {
        error_log("Fel vid uppdatering av användarprofil: " . $e->getMessage());
        return false;
    }
}

function validatePassword($password) {
    // Minst 8 tecken, minst en stor bokstav, en liten bokstav och en siffra
    return strlen($password) >= 8 && 
           preg_match('/[A-Z]/', $password) && 
           preg_match('/[a-z]/', $password) && 
           preg_match('/[0-9]/', $password);
} 