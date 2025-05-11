<?php
require_once '../includes/header.php';
require_once '../includes/functions.php';

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'title';
$order = isset($_GET['order']) ? strtoupper($_GET['order']) : 'ASC';

if (empty($search)) {
    header('Location: /webshoppen/public/');
    exit();
}

$products = searchProducts($search, $sort, $order);
?>

<div class="main-content">
    <div class="search-results-header">
        <h1>Sökresultat för "<?php echo htmlspecialchars($search); ?>"</h1>
        <p>Hittade <?php echo count($products); ?> produkt(er)</p>
        
        <div class="sorting-options">
            <label for="sort">Sortera efter:</label>
            <select id="sort" onchange="updateSort(this.value)">
                <option value="title-asc" <?php echo ($sort === 'title' && $order === 'ASC') ? 'selected' : ''; ?>>
                    Namn (A-Ö)
                </option>
                <option value="title-desc" <?php echo ($sort === 'title' && $order === 'DESC') ? 'selected' : ''; ?>>
                    Namn (Ö-A)
                </option>
                <option value="price-asc" <?php echo ($sort === 'price' && $order === 'ASC') ? 'selected' : ''; ?>>
                    Pris (Lägst först)
                </option>
                <option value="price-desc" <?php echo ($sort === 'price' && $order === 'DESC') ? 'selected' : ''; ?>>
                    Pris (Högst först)
                </option>
            </select>
        </div>
    </div>

    <?php if (empty($products)): ?>
        <div class="no-results">
            <p>Inga produkter matchade din sökning.</p>
            <div class="search-tips">
                <h2>Söktips:</h2>
                <ul>
                    <li>Kontrollera stavningen</li>
                    <li>Prova med färre sökord</li>
                    <li>Använd mer generella söktermer</li>
                    <li>Sök på produktkategori</li>
                </ul>
            </div>
        </div>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="image-container">
                        <?php if ($product['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['title']); ?>">
                        <?php endif; ?>
                        
                        <span class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></span>
                        
                        <?php if (isset($product['deal_price']) && $product['deal_price'] > 0): ?>
                            <span class="deal-badge">REA</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-info">
                        <a href="product.php?id=<?php echo $product['id']; ?>" class="product-title">
                            <?php echo htmlspecialchars($product['title']); ?>
                        </a>
                        
                        <div class="product-price-container">
                            <?php if (isset($product['deal_price']) && $product['deal_price'] > 0): ?>
                                <span class="product-price sale"><?php echo number_format($product['deal_price'], 0, ',', ' '); ?> kr</span>
                                <span class="product-price original"><?php echo number_format($product['price'], 0, ',', ' '); ?> kr</span>
                            <?php else: ?>
                                <span class="product-price"><?php echo number_format($product['price'], 0, ',', ' '); ?> kr</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function updateSort(value) {
    const [sort, order] = value.split('-');
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('sort', sort);
    currentUrl.searchParams.set('order', order);
    window.location.href = currentUrl.toString();
}
</script>

<style>
.search-results-header {
    margin-bottom: 2rem;
}

.search-results-header h1 {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.sorting-options {
    margin-top: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.sorting-options label {
    color: #666;
}

.sorting-options select {
    padding: 0.5rem;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    background-color: white;
    font-size: 0.875rem;
}

.no-results {
    text-align: center;
    padding: 3rem 0;
}

.search-tips {
    margin-top: 2rem;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

.search-tips h2 {
    font-size: 1.25rem;
    margin-bottom: 1rem;
}

.search-tips ul {
    list-style: disc;
    padding-left: 1.5rem;
    color: #666;
}

.search-tips li {
    margin-bottom: 0.5rem;
}

@media (max-width: 768px) {
    .search-results-header {
        margin-bottom: 1.5rem;
    }
    
    .sorting-options {
        flex-direction: column;
        align-items: stretch;
        gap: 0.5rem;
    }
    
    .sorting-options select {
        width: 100%;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?> 