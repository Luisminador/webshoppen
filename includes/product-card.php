<?php
function renderProductCard($product) {
    $requiredFields = ['id', 'title', 'price', 'image_url', 'category_name'];
    foreach ($requiredFields as $field) {
        if (!isset($product[$field])) {
            error_log("Saknat fält i produkt: $field");
            return;
        }
    }

    $hasDeal = isset($product['deal_price']) && $product['deal_price'] > 0;
    $hasVariants = isset($product['variants']) && !empty($product['variants']);
    
    $productUrl = '/webshoppen/public/product.php?id=' . htmlspecialchars($product['id']);
    
    $defaultImage = 'https://placehold.co/400x300?text=Ingen+bild';
    $imageUrl = !empty($product['image_url']) && filter_var($product['image_url'], FILTER_VALIDATE_URL) 
        ? $product['image_url'] 
        : $defaultImage;
?>
    <div class="product-card">
        <div class="image-container">
            <a href="<?php echo $productUrl; ?>">
                <img src="<?php echo htmlspecialchars($imageUrl); ?>" 
                     alt="<?php echo htmlspecialchars($product['title']); ?>"
                     loading="lazy"
                     onerror="this.src='<?php echo htmlspecialchars($defaultImage); ?>'">
            </a>
            
            <?php if ($hasDeal): ?>
                <div class="outlet-badge">REA</div>
            <?php endif; ?>
            
            <button class="favorite-btn" 
                    data-product-id="<?php echo htmlspecialchars($product['id']); ?>"
                    aria-label="Lägg till i favoriter"
                    onclick="toggleFavorite(event, this)">
                <i class="ri-heart-3-line"></i>
            </button>
        </div>
        
        <div class="product-info">
            <span class="brand"><?php echo htmlspecialchars($product['category_name']); ?></span>
            <a href="<?php echo $productUrl; ?>" class="product-title">
                <?php echo htmlspecialchars($product['title']); ?>
            </a>
            
            <div class="price-container">
                <?php if ($hasDeal): ?>
                    <span class="price sale"><?php echo number_format($product['deal_price'], 0, ',', ' '); ?> kr</span>
                    <span class="price original"><?php echo number_format($product['price'], 0, ',', ' '); ?> kr</span>
                <?php else: ?>
                    <span class="price"><?php echo number_format($product['price'], 0, ',', ' '); ?> kr</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php
}

?>
<script>
function toggleFavorite(event, button) {
    event.preventDefault();
    event.stopPropagation();
    
    const productId = button.dataset.productId;
    const icon = button.querySelector('i');
    
    button.classList.toggle('active');
    if (icon.classList.contains('ri-heart-3-line')) {
        icon.classList.remove('ri-heart-3-line');
        icon.classList.add('ri-heart-3-fill');
    } else {
        icon.classList.remove('ri-heart-3-fill');
        icon.classList.add('ri-heart-3-line');
    }
    
    fetch('/webshoppen/public/api/favorites.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            button.classList.toggle('active');
            if (icon.classList.contains('ri-heart-3-fill')) {
                icon.classList.remove('ri-heart-3-fill');
                icon.classList.add('ri-heart-3-line');
            } else {
                icon.classList.remove('ri-heart-3-line');
                icon.classList.add('ri-heart-3-fill');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.classList.toggle('active');
        if (icon.classList.contains('ri-heart-3-fill')) {
            icon.classList.remove('ri-heart-3-fill');
            icon.classList.add('ri-heart-3-line');
        } else {
            icon.classList.remove('ri-heart-3-line');
            icon.classList.add('ri-heart-3-fill');
        }
    });
}
</script> 