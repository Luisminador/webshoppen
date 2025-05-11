<?php
require_once '../includes/header.php';
require_once '../includes/functions.php';

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$product = getProductById($productId);

if (!$product) {
    setFlashMessage('Produkten kunde inte hittas.', 'error');
    header('Location: index.php');
    exit();
}
?>

<div class="main-content">
    <div class="product-detail">
        <div class="product-image-container">
            <img src="<?php echo sanitize($product['image_url']); ?>" 
                 alt="<?php echo sanitize($product['title']); ?>">
            <?php if (isset($product['deal_price']) && $product['deal_price'] > 0 && $product['deal_price'] < $product['price']): ?>
            <div class="sale-badge">REA!</div>
            <?php endif; ?>
        </div>

        <div class="product-info">
            <div class="product-header">
                <p class="category-label"><?php echo sanitize($product['category_name']); ?></p>
                <h1><?php echo sanitize($product['title']); ?></h1>
            </div>
            
            <div class="price-container">
                <?php if (isset($product['deal_price']) && $product['deal_price'] > 0 && $product['deal_price'] < $product['price']): ?>
                    <span class="deal-price"><?php echo number_format($product['deal_price'], 0, ',', ' '); ?> kr</span>
                    <span class="original-price"><?php echo number_format($product['price'], 0, ',', ' '); ?> kr</span>
                <?php else: ?>
                    <span class="price"><?php echo number_format($product['price'], 0, ',', ' '); ?> kr</span>
                <?php endif; ?>
            </div>

            <div class="product-description">
                <p><?php echo sanitize($product['description']); ?></p>
            </div>

            <div class="product-actions">
                <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <div class="quantity-selector">
                        <label for="quantity">Antal:</label>
                        <select name="quantity" id="quantity">
                            <?php for($i = 1; $i <= 10; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <button type="submit" class="add-to-cart-button">
                        Lägg i varukorg
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 