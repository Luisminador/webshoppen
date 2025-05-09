<?php
require_once '../includes/header.php';
require_once '../includes/functions.php';
require_once '../includes/product-card.php';

// Hämta kategori-ID från URL
$categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Hämta kategoriinformation
$category = getCategoryById($categoryId);

// Om kategorin inte hittas
if (!$category) {
    setFlashMessage('Kategorin kunde inte hittas.', 'error');
    header('Location: /webshoppen/public/');
    exit();
}

// Hämta sorteringsparametrar
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'title';
$order = isset($_GET['order']) ? strtoupper($_GET['order']) : 'ASC';

// Validera sorteringsparametrar
$validSorts = ['title', 'price'];
$validOrders = ['ASC', 'DESC'];

$sort = in_array($sort, $validSorts) ? $sort : 'title';
$order = in_array($order, $validOrders) ? $order : 'ASC';

// Hämta produkter för kategorin
$products = getProductsByCategory($categoryId, $sort, $order);
?>

<div class="main-content">
    <div class="category-header">
        <h1><?php echo htmlspecialchars($category['name']); ?></h1>
        
        <!-- Sorteringsval -->
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

    <div class="product-grid">
        <?php if (empty($products)): ?>
            <p class="no-products">Inga produkter hittades i denna kategori.</p>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <?php renderProductCard($product); ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
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

<?php require_once '../includes/footer.php'; ?> 