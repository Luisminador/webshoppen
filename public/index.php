<?php
require_once '../includes/header.php';
require_once '../includes/functions.php';
require_once '../includes/product-card.php';

$popularProducts = getPopularProducts(6);

$saleProducts = getDiscountedProducts();
$saleProducts = array_slice($saleProducts, 0, 4); // Visa bara de 4 första reaprodukterna
?>

<div class="main-content">
    <div class="hero-section">
        <h1>Välkommen till Ljus & Harmoni</h1>
        <p>Upptäck vårt handplockade sortiment av doftljus och tillbehör</p>
    </div>

    <section class="product-section">
        <div class="section-header">
            <h2>Våra doftljus</h2>
            <a href="/webshoppen/public/category.php?cat=ljus" class="view-all">Visa alla</a>
        </div>
        <div class="product-grid">
            <?php foreach ($popularProducts as $product): ?>
                <?php renderProductCard($product); ?>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="category-highlights">
        <div class="category-grid">
            <a href="/webshoppen/public/category.php?cat=ljus" class="category-card">
                <img src="https://placehold.co/600x400?text=Doftljus" alt="Doftljus">
                <h3>Doftljus</h3>
                <p>Utforska vårt sortiment</p>
            </a>
            <a href="/webshoppen/public/category.php?cat=tillbehor" class="category-card">
                <img src="https://placehold.co/600x400?text=Tillbehör" alt="Tillbehör">
                <h3>Tillbehör</h3>
                <p>Förhöj din upplevelse</p>
            </a>
        </div>
    </section>

    <?php if (!empty($saleProducts)): ?>
    <section class="product-section sale-section">
        <div class="section-header">
            <h2>Just nu på rea</h2>
            <a href="/webshoppen/public/category.php?cat=rea" class="view-all">Visa alla</a>
        </div>
        <div class="product-grid">
            <?php foreach ($saleProducts as $product): ?>
                <?php renderProductCard($product); ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>

<style>
.hero-section {
    text-align: center;
    padding: 4rem 2rem;
    background: #f8f8f8;
    margin-bottom: 2rem;
}

.hero-section h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.view-all {
    color: #666;
    text-decoration: none;
}

.view-all:hover {
    text-decoration: underline;
}

.category-highlights {
    margin: 4rem 0;
}

.category-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
}

.category-card {
    text-decoration: none;
    color: inherit;
    text-align: center;
    transition: transform 0.2s;
}

.category-card:hover {
    transform: translateY(-5px);
}

.category-card img {
    width: 100%;
    height: auto;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.sale-section {
    margin: 4rem 0;
}
</style>

<?php require_once '../includes/footer.php'; ?> 