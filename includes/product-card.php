<?php
function renderProductCard($product) {
    $hasDeal = isset($product['deal_price']);
    $hasVariants = isset($product['variants']) && !empty($product['variants']);
?>
    <div class="product-card">
        <div class="image-container">
            <?php if ($hasDeal): ?>
                <span class="deal-badge">Deal</span>
            <?php endif; ?>
            
            <button class="favorite-btn" aria-label="Lägg till i favoriter">
                <i class="ri-heart-3-line"></i>
            </button>
            
            <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                 alt="<?php echo htmlspecialchars($product['title']); ?>"
                 loading="lazy">
        </div>
        
        <div class="product-info">
            <a href="<?php echo htmlspecialchars($product['url']); ?>" class="product-title">
                <?php echo htmlspecialchars($product['title']); ?>
            </a>
            
            <div class="product-category">
                <?php echo htmlspecialchars($product['category']); ?>
            </div>
            
            <div class="product-price-container">
                <?php if ($hasDeal): ?>
                    <span class="product-price original"><?php echo number_format($product['price'], 2); ?> SEK</span>
                    <span class="product-price sale"><?php echo number_format($product['deal_price'], 2); ?> SEK</span>
                <?php else: ?>
                    <span class="product-price"><?php echo number_format($product['price'], 2); ?> SEK</span>
                <?php endif; ?>
            </div>

            <?php if ($hasVariants): ?>
            <div class="variant-tags">
                <?php foreach ($product['variants'] as $variant): ?>
                    <span class="variant-tag"><?php echo htmlspecialchars($variant); ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
<?php
}
?> 