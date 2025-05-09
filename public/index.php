<?php
require_once '../includes/header.php';
require_once '../includes/functions.php';
require_once '../includes/product-card.php';

$products = getPopularProducts(10);
?>

<div class="main-content">
    <h1>Välkommen till Webshoppen</h1>
    <h2>Populära produkter</h2>

    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <?php renderProductCard($product); ?>
        <?php endforeach; ?>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?> 