<?php
require_once '../includes/functions.php';
require_once '../includes/db.php';

// Kräv inloggning
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $cart_item_id = filter_input(INPUT_POST, 'cart_item_id', FILTER_VALIDATE_INT);
        
        if ($cart_item_id) {
            if ($_POST['action'] === 'update') {
                $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
                if ($quantity > 0 && $quantity <= 10) {
                    $stmt = $pdo->prepare('
                        UPDATE cart_items 
                        SET quantity = ? 
                        WHERE id = ? AND user_id = ?
                    ');
                    $stmt->execute([$quantity, $cart_item_id, $_SESSION['user_id']]);
                    setFlashMessage('Antal uppdaterat', 'success');
                }
            } elseif ($_POST['action'] === 'remove') {
                $stmt = $pdo->prepare('
                    DELETE FROM cart_items 
                    WHERE id = ? AND user_id = ?
                ');
                $stmt->execute([$cart_item_id, $_SESSION['user_id']]);
                setFlashMessage('Produkten har tagits bort från varukorgen', 'success');
            }
        }
        
        // Omdirigera till samma sida för att undvika form resubmission
        header('Location: ' . BASE_URL . '/cart.php');
        exit();
    }
}

// Hämta varukorgens innehåll
$cart_items = getCartItems($_SESSION['user_id']);
$total = calculateCartTotal($cart_items);

// Inkludera header
require_once '../includes/header.php';
?>

<div class="container">
    <h1 class="page-title">Din varukorg</h1>

    <?php if (empty($cart_items)): ?>
        <div class="empty-cart">
            <p>Din varukorg är tom.</p>
            <a href="<?php echo BASE_URL; ?>" class="btn btn-primary">Fortsätt handla</a>
        </div>
    <?php else: ?>
        <div class="cart-container">
            <div class="cart-items">
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        <div class="cart-item-image">
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['title']); ?>">
                        </div>
                        
                        <div class="cart-item-details">
                            <h3 class="cart-item-title">
                                <a href="<?php echo BASE_URL; ?>/product.php?id=<?php echo $item['product_id']; ?>">
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </a>
                            </h3>
                            <div class="cart-item-price">
                                <span class="price"><?php echo number_format($item['price'], 2, ',', ' '); ?> kr/st</span>
                                <span class="total">Totalt: <?php echo number_format($item['price'] * $item['quantity'], 2, ',', ' '); ?> kr</span>
                            </div>
                        </div>

                        <div class="cart-item-actions">
                            <form method="post" action="<?php echo BASE_URL; ?>/cart.php" class="quantity-form">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="cart_item_id" value="<?php echo $item['id']; ?>">
                                <div class="quantity-selector">
                                    <button type="button" onclick="updateQuantity(this, -1)" class="quantity-btn">-</button>
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                           min="1" max="10" class="quantity-input" readonly>
                                    <button type="button" onclick="updateQuantity(this, 1)" class="quantity-btn">+</button>
                                </div>
                            </form>

                            <form method="post" action="<?php echo BASE_URL; ?>/cart.php" class="remove-form">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="cart_item_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" class="remove-item" aria-label="Ta bort produkt">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="cart-summary">
                <div class="cart-total">
                    <span class="label">Totalsumma:</span>
                    <span class="amount"><?php echo number_format($total, 2, ',', ' '); ?> kr</span>
                </div>
                
                <div class="cart-actions">
                    <a href="<?php echo BASE_URL; ?>" class="btn">Fortsätt handla</a>
                    <a href="<?php echo BASE_URL; ?>/checkout.php" class="btn btn-primary">Gå till kassan</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function updateQuantity(button, change) {
    const form = button.closest('.quantity-form');
    const input = form.querySelector('.quantity-input');
    const currentValue = parseInt(input.value);
    const newValue = currentValue + change;
    
    // Kontrollera att värdet är inom gränserna (1-10)
    if (newValue >= 1 && newValue <= 10) {
        input.value = newValue;
        // Skicka formuläret automatiskt
        form.submit();
    }
}
</script>

<style>
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

.page-title {
    margin-bottom: 2rem;
    font-size: 2rem;
    font-weight: 600;
}

.empty-cart {
    text-align: center;
    padding: 3rem;
}

.empty-cart p {
    margin-bottom: 1.5rem;
    color: #666;
}

.cart-container {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 2rem;
}

.cart-items {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.cart-item {
    display: grid;
    gap: 1.5rem;
    padding: 1.5rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.cart-item-image {
    width: 120px;
    height: 120px;
    overflow: hidden;
    border-radius: 4px;
}

.cart-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.cart-item-details {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.cart-item-title {
    margin: 0;
    font-size: 1.125rem;
}

.cart-item-title a {
    color: inherit;
    text-decoration: none;
}

.cart-item-title a:hover {
    text-decoration: underline;
}

.cart-item-price {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.cart-item-price .price {
    font-size: 1rem;
    color: #666;
}

.cart-item-price .total {
    font-size: 1.125rem;
    font-weight: 600;
}

.cart-item-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-top: auto;
}

.quantity-selector {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.quantity-selector label {
    color: #666;
}

.quantity-selector select {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: white;
}

.btn-remove {
    padding: 0.5rem 1rem;
    background: none;
    border: 1px solid #dc2626;
    color: #dc2626;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-remove:hover {
    background: #dc2626;
    color: white;
}

.cart-summary {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    height: fit-content;
}

.cart-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 1.5rem;
    margin-bottom: 1.5rem;
    border-bottom: 1px solid #eee;
}

.cart-total .label {
    font-size: 1.125rem;
    color: #666;
}

.cart-total .amount {
    font-size: 1.5rem;
    font-weight: 600;
}

.cart-actions {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.cart-actions .btn {
    width: 100%;
    padding: 0.875rem;
    text-align: center;
    border-radius: 4px;
    text-decoration: none;
    transition: all 0.2s;
}

.btn {
    background: #f5f5f5;
    color: #333;
    border: none;
}

.btn:hover {
    background: #eee;
}

.btn-primary {
    background: #000;
    color: white;
}

.btn-primary:hover {
    background: #333;
}

@media (max-width: 768px) {
    .cart-container {
        grid-template-columns: 1fr;
    }

    .cart-item {
        grid-template-columns: 80px 1fr;
        gap: 1rem;
        padding: 1rem;
    }

    .cart-item-image {
        width: 80px;
        height: 80px;
    }

    .cart-item-actions {
        flex-direction: column;
        align-items: flex-start;
    }

    .quantity-selector {
        width: 100%;
    }

    .quantity-selector select {
        width: 100%;
    }

    .btn-remove {
        width: 100%;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?> 