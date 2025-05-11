<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $cart_item_id = filter_input(INPUT_POST, 'cart_item_id', FILTER_VALIDATE_INT);
        
        if ($cart_item_id) {
            if ($_POST['action'] === 'update') {
                $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
                if ($quantity > 0) {
                    $stmt = $pdo->prepare('
                        UPDATE cart_items 
                        SET quantity = ? 
                        WHERE id = ? AND user_id = ?
                    ');
                    $stmt->execute([$quantity, $cart_item_id, $_SESSION['user_id']]);
                }
            } elseif ($_POST['action'] === 'remove') {
                $stmt = $pdo->prepare('
                    DELETE FROM cart_items 
                    WHERE id = ? AND user_id = ?
                ');
                $stmt->execute([$cart_item_id, $_SESSION['user_id']]);
            }
        }
        
        header('Location: /cart.php');
        exit();
    }
}

$cart_items = getCartItems($pdo, $_SESSION['user_id']);
$total = calculateCartTotal($cart_items);

require_once '../includes/header.php';
?>

<h1>Din kundvagn</h1>

<?php if (empty($cart_items)): ?>
    <p>Din kundvagn är tom.</p>
    <p><a href="/" class="btn">Fortsätt handla</a></p>
<?php else: ?>
    <div class="cart-items">
        <?php foreach ($cart_items as $item): ?>
            <div class="cart-item">
                <h3><?= sanitize($item['name']) ?></h3>
                <p class="price">
                    <?= number_format($item['price'], 2) ?> kr/st
                    <span class="subtotal">
                        Totalt: <?= number_format($item['price'] * $item['quantity'], 2) ?> kr
                    </span>
                </p>
                
                <div class="cart-item-actions">
                    <form method="POST" class="update-quantity">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="cart_item_id" value="<?= $item['id'] ?>">
                        
                        <div class="form-group">
                            <label for="quantity_<?= $item['id'] ?>">Antal:</label>
                            <input type="number" 
                                   id="quantity_<?= $item['id'] ?>" 
                                   name="quantity" 
                                   value="<?= $item['quantity'] ?>" 
                                   min="1" 
                                   onchange="this.form.submit()">
                        </div>
                    </form>
                    
                    <form method="POST" class="remove-item">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="cart_item_id" value="<?= $item['id'] ?>">
                        <button type="submit" class="btn btn-danger">Ta bort</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
        
        <div class="cart-summary">
            <p class="total">Totalsumma: <?= number_format($total, 2) ?> kr</p>
            
            <div class="cart-actions">
                <a href="/" class="btn">Fortsätt handla</a>
                <a href="/checkout.php" class="btn btn-primary">Gå till kassan</a>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?> 