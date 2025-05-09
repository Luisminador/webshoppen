<?php
require_once '../includes/header.php';
require_once '../includes/functions.php';

// Hämta produkt-ID från URL
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Hämta produktinformation
$product = getProductById($productId);

// Om produkten inte hittas
if (!$product) {
    setFlashMessage('Produkten kunde inte hittas.', 'error');
    header('Location: /webshoppen/public/');
    exit();
}
?>

<div class="main-content">
    <div class="product-detail">
        <div class="product-image">
            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                 alt="<?php echo htmlspecialchars($product['title']); ?>">
        </div>
        
        <div class="product-info">
            <h1><?php echo htmlspecialchars($product['title']); ?></h1>
            
            <div class="product-category">
                <?php echo htmlspecialchars($product['category_name']); ?>
            </div>
            
            <div class="product-price-container">
                <?php if (isset($product['deal_price'])): ?>
                    <span class="product-price original"><?php echo number_format($product['price'], 2); ?> SEK</span>
                    <span class="product-price sale"><?php echo number_format($product['deal_price'], 2); ?> SEK</span>
                <?php else: ?>
                    <span class="product-price"><?php echo number_format($product['price'], 2); ?> SEK</span>
                <?php endif; ?>
            </div>
            
            <div class="product-description">
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </div>
            
            <div class="product-actions">
                <button class="add-to-cart-btn">
                    <i class="ri-shopping-cart-line"></i>
                    Lägg i varukorgen
                </button>
                <button class="favorite-btn" aria-label="Lägg till i favoriter">
                    <i class="ri-heart-3-line"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 