<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Kontrollera om användaren är inloggad
if (!isLoggedIn()) {
    header('Location: /webshoppen/public/login.php');
    exit();
}

// Hämta användarinformation
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch();

require_once '../includes/header.php';
?>

<div class="profile-container">
    <h1>Mitt Konto</h1>
    
    <div class="profile-info">
        <h2>Välkommen <?= sanitize($user['username']) ?>!</h2>
        
        <div class="profile-section">
            <h3>Mina uppgifter</h3>
            <p><strong>Användarnamn:</strong> <?= sanitize($user['username']) ?></p>
            <p><strong>E-post:</strong> <?= sanitize($user['email'] ?? 'Ingen e-post angiven') ?></p>
        </div>

        <div class="profile-section">
            <h3>Mina val</h3>
            <ul class="profile-links">
                <li><a href="/webshoppen/public/orders.php" class="profile-link">
                    <i class="ri-shopping-bag-line"></i> Mina beställningar
                </a></li>
                <li><a href="/webshoppen/public/favorites.php" class="profile-link">
                    <i class="ri-heart-line"></i> Mina favoriter
                </a></li>
                <li><a href="/webshoppen/public/logout.php" class="profile-link">
                    <i class="ri-logout-box-line"></i> Logga ut
                </a></li>
            </ul>
        </div>
    </div>
</div>

<style>
.profile-container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.profile-info {
    background: #fff;
    border-radius: 8px;
    padding: 2rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.profile-section {
    margin: 2rem 0;
    padding: 1.5rem;
    border: 1px solid #eee;
    border-radius: 8px;
}

.profile-section h3 {
    margin-top: 0;
    color: #333;
    font-size: 1.2rem;
    margin-bottom: 1rem;
}

.profile-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.profile-links li {
    margin: 0.5rem 0;
}

.profile-link {
    display: flex;
    align-items: center;
    padding: 1rem;
    color: #333;
    text-decoration: none;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.profile-link:hover {
    background-color: #f5f5f5;
    transform: translateX(5px);
}

.profile-link i {
    margin-right: 10px;
    font-size: 1.2rem;
}

h1 {
    margin-bottom: 2rem;
    color: #333;
}

h2 {
    color: #666;
    font-size: 1.3rem;
    margin-bottom: 1.5rem;
}
</style>

<?php require_once '../includes/footer.php'; ?> 