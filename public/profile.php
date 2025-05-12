<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: /webshoppen/public/login.php');
    exit();
}

$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$orders = [];
if (isset($_GET['tab']) && $_GET['tab'] === 'orders') {
    $stmt = $pdo->prepare('
        SELECT o.*, 
               COUNT(oi.id) as items_count,
               GROUP_CONCAT(CONCAT(oi.quantity, "x ", p.title) SEPARATOR ", ") as order_items
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE o.user_id = ?
        GROUP BY o.id
        ORDER BY o.order_date DESC
    ');
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll();
}

$favorites = [];
if (isset($_GET['tab']) && $_GET['tab'] === 'favorites') {
    $stmt = $pdo->prepare('
        SELECT p.*, f.created_at as favorited_at
        FROM favorites f
        JOIN products p ON f.product_id = p.id
        WHERE f.user_id = ?
        ORDER BY f.created_at DESC
    ');
    $stmt->execute([$_SESSION['user_id']]);
    $favorites = $stmt->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_favorite'])) {
    $product_id = $_POST['product_id'] ?? '';
    if ($product_id) {
        $stmt = $pdo->prepare('DELETE FROM favorites WHERE user_id = ? AND product_id = ?');
        $stmt->execute([$_SESSION['user_id'], $product_id]);
        header('Location: /webshoppen/public/profile.php?tab=favorites');
        exit();
    }
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $postcode = trim($_POST['postcode'] ?? '');
    $city = trim($_POST['city'] ?? '');

    if (!$firstname || !$lastname) {
        $error = 'Förnamn och efternamn är obligatoriska';
    } else {
        $stmt = $pdo->prepare('
            UPDATE users 
            SET firstname = ?, lastname = ?, phone = ?, address = ?, postcode = ?, city = ?
            WHERE id = ?
        ');

        try {
            $stmt->execute([$firstname, $lastname, $phone, $address, $postcode, $city, $_SESSION['user_id']]);
            $success = 'Din profil har uppdaterats!';
            
            $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
        } catch (PDOException $e) {
            $error = 'Ett fel uppstod när profilen skulle uppdateras';
        }
    }
}

$activeTab = $_GET['tab'] ?? 'profile';

require_once '../includes/header.php';
?>

<div class="container">
    <div class="profile-header">
        <h1>Mitt konto</h1>
        <p class="welcome-text">Välkommen <?= htmlspecialchars($user['firstname']) ?>!</p>
    </div>

    <div class="profile-tabs">
        <a href="?tab=profile" class="tab <?= $activeTab === 'profile' ? 'active' : '' ?>">
            <i class="ri-user-3-line"></i> Profil
        </a>
        <a href="?tab=orders" class="tab <?= $activeTab === 'orders' ? 'active' : '' ?>">
            <i class="ri-shopping-bag-3-line"></i> Ordrar
        </a>
        <a href="?tab=favorites" class="tab <?= $activeTab === 'favorites' ? 'active' : '' ?>">
            <i class="ri-heart-3-line"></i> Favoriter
        </a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="profile-content">
        <?php if ($activeTab === 'profile'): ?>
            <div class="profile-section">
                <h2>Mina uppgifter</h2>
                <form method="POST" action="/webshoppen/public/profile.php" class="form">
                    <input type="hidden" name="update_profile" value="1">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstname">Förnamn *</label>
                            <input type="text" 
                                   id="firstname" 
                                   name="firstname" 
                                   value="<?= htmlspecialchars($user['firstname']) ?>" 
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="lastname">Efternamn *</label>
                            <input type="text" 
                                   id="lastname" 
                                   name="lastname" 
                                   value="<?= htmlspecialchars($user['lastname']) ?>" 
                                   required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">E-postadress</label>
                        <input type="email" 
                               id="email" 
                               value="<?= htmlspecialchars($user['email']) ?>" 
                               disabled>
                        <small>E-postadressen kan inte ändras</small>
                    </div>

                    <div class="form-group">
                        <label for="phone">Telefon</label>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="address">Gatuadress</label>
                        <input type="text" 
                               id="address" 
                               name="address" 
                               value="<?= htmlspecialchars($user['address'] ?? '') ?>">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="postcode">Postnummer</label>
                            <input type="text" 
                                   id="postcode" 
                                   name="postcode" 
                                   value="<?= htmlspecialchars($user['postcode'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label for="city">Stad</label>
                            <input type="text" 
                                   id="city" 
                                   name="city" 
                                   value="<?= htmlspecialchars($user['city'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Spara ändringar</button>
                    </div>
                </form>
            </div>

        <?php elseif ($activeTab === 'orders'): ?>
            <div class="orders-section">
                <h2>Mina ordrar</h2>
                <?php if (empty($orders)): ?>
                    <p class="empty-message">Du har inga ordrar än.</p>
                <?php else: ?>
                    <div class="orders-list">
                        <?php foreach ($orders as $order): ?>
                            <div class="order-card">
                                <div class="order-header">
                                    <div class="order-info">
                                        <span class="order-number">Order #<?= htmlspecialchars($order['id']) ?></span>
                                        <span class="order-date"><?= date('Y-m-d H:i', strtotime($order['order_date'])) ?></span>
                                    </div>
                                    <span class="order-status <?= strtolower($order['status']) ?>">
                                        <?= htmlspecialchars($order['status']) ?>
                                    </span>
                                </div>
                                <div class="order-details">
                                    <p class="order-items"><?= htmlspecialchars($order['order_items']) ?></p>
                                    <p class="order-total"><?= number_format($order['total_amount'], 2, ',', ' ') ?> kr</p>
                                </div>
                                <div class="order-address">
                                    <p><?= htmlspecialchars($order['shipping_address']) ?></p>
                                    <p><?= htmlspecialchars($order['shipping_postcode']) ?> <?= htmlspecialchars($order['shipping_city']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        <?php elseif ($activeTab === 'favorites'): ?>
            <div class="favorites-section">
                <h2>Mina favoriter</h2>
                <?php if (empty($favorites)): ?>
                    <p class="empty-message">Du har inga favoritprodukter än.</p>
                <?php else: ?>
                    <div class="favorites-grid">
                        <?php foreach ($favorites as $product): ?>
                            <div class="favorite-card">
                                <a href="/webshoppen/public/product.php?id=<?= $product['id'] ?>" class="product-link">
                                    <div class="product-image">
                                        <img src="<?= htmlspecialchars($product['image_url']) ?>" 
                                             alt="<?= htmlspecialchars($product['title']) ?>">
                                    </div>
                                    <div class="product-info">
                                        <h3><?= htmlspecialchars($product['title']) ?></h3>
                                        <div class="price-container">
                                            <?php if (isset($product['deal_price']) && $product['deal_price'] > 0): ?>
                                                <span class="price sale"><?= number_format($product['deal_price'], 2, ',', ' ') ?> kr</span>
                                                <span class="price original"><?= number_format($product['price'], 2, ',', ' ') ?> kr</span>
                                            <?php else: ?>
                                                <span class="price"><?= number_format($product['price'], 2, ',', ' ') ?> kr</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a>
                                <form method="POST" class="remove-favorite-form">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <input type="hidden" name="remove_favorite" value="1">
                                    <button type="submit" class="btn btn-remove">Ta bort från favoriter</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.profile-header {
    margin-bottom: 2rem;
    text-align: center;
}

.profile-header h1 {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    color: #333;
}

.welcome-text {
    color: #666;
    font-size: 1.1rem;
}

.profile-tabs {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    border-bottom: 1px solid #ddd;
    padding-bottom: 1rem;
}

.tab {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    color: #666;
    text-decoration: none;
    border-radius: 4px;
    transition: all 0.2s;
}

.tab:hover {
    background: #f5f5f5;
    color: #000;
}

.tab.active {
    background: #000;
    color: white;
}

.tab i {
    font-size: 1.2rem;
}

.profile-content {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.profile-section h2,
.orders-section h2,
.favorites-section h2 {
    margin-bottom: 1.5rem;
    color: #333;
}

.form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group label {
    font-weight: 500;
    color: #333;
}

.form-group input {
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.form-group input:focus {
    outline: none;
    border-color: #666;
}

.form-group input:disabled {
    background: #f5f5f5;
    cursor: not-allowed;
}

.form-group small {
    color: #666;
    font-size: 0.875rem;
}

.form-actions {
    margin-top: 1rem;
}

.btn-primary {
    background: #000;
    color: white;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.2s;
}

.btn-primary:hover {
    background: #333;
}

.alert {
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1rem;
}

.alert-success {
    background: #f0fdf4;
    border: 1px solid #16a34a;
    color: #16a34a;
}

.alert-error {
    background: #fef2f2;
    border: 1px solid #dc2626;
    color: #dc2626;
}

/* Order styles */
.orders-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.order-card {
    background: #f8f8f8;
    border-radius: 8px;
    padding: 1.5rem;
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.order-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.order-number {
    font-weight: 600;
    color: #000;
}

.order-date {
    font-size: 0.875rem;
    color: #666;
}

.order-status {
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    font-size: 0.875rem;
    font-weight: 500;
}

.order-status.pending {
    background: #fef3c7;
    color: #92400e;
}

.order-status.completed {
    background: #dcfce7;
    color: #166534;
}

.order-details {
    margin-bottom: 1rem;
}

.order-items {
    color: #666;
    margin-bottom: 0.5rem;
}

.order-total {
    font-weight: 600;
    color: #000;
}

.order-address {
    font-size: 0.875rem;
    color: #666;
}

/* Favorites styles */
.favorites-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 2rem;
}

.favorite-card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.favorite-card:hover {
    transform: translateY(-4px);
}

.product-link {
    text-decoration: none;
    color: inherit;
}

.product-image {
    position: relative;
    padding-top: 100%;
}

.product-image img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-info {
    padding: 1rem;
}

.product-info h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
    color: #333;
}

.price-container {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.price.sale {
    color: #e91e63;
    font-weight: 600;
}

.price.original {
    text-decoration: line-through;
    color: #666;
    font-size: 0.875rem;
}

.remove-favorite-form {
    padding: 1rem;
    border-top: 1px solid #eee;
}

.btn-remove {
    width: 100%;
    background: #fff;
    color: #dc2626;
    border: 1px solid #dc2626;
    padding: 0.5rem;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-remove:hover {
    background: #dc2626;
    color: white;
}

.empty-message {
    text-align: center;
    color: #666;
    padding: 2rem;
}

@media (max-width: 768px) {
    .container {
        padding: 1rem;
    }

    .profile-tabs {
        flex-wrap: wrap;
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .profile-content {
        padding: 1rem;
    }

    .favorites-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }

    .order-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .order-status {
        align-self: flex-start;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?> 