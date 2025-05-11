<?php
require_once '../includes/header.php';
require_once '../includes/functions.php';
require_once '../includes/product-card.php';

$categorySlug = isset($_GET['cat']) ? $_GET['cat'] : '';

$pageTitle = '';
$pageDescription = '';
$products = [];

switch($categorySlug) {
    case 'ljus':
        $pageTitle = 'Doftljus';
        $pageDescription = 'Utforska vårt breda sortiment av handgjorda doftljus';
        $products = getProductsByCategory(1); // Kategori ID 1 är Ljus
        break;
        
    case 'tillbehor':
        $pageTitle = 'Tillbehör';
        $pageDescription = 'Allt du behöver för att förhöja din ljusupplevelse';
        $products = getProductsByCategory(2); // Kategori ID 2 är Tillbehör
        break;
        
    case 'rea':
        $pageTitle = 'REA';
        $pageDescription = 'Fynda bland våra reavaror';
        $products = getDiscountedProducts();
        break;
        
    default:
        setFlashMessage('Kategorin kunde inte hittas.', 'error');
        header('Location: /webshoppen/public/');
        exit();
}

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'title';
$order = isset($_GET['order']) ? strtoupper($_GET['order']) : 'ASC';

$validSorts = ['title', 'price', 'popularity'];
$validOrders = ['ASC', 'DESC'];

$sort = in_array($sort, $validSorts) ? $sort : 'title';
$order = in_array($order, $validOrders) ? $order : 'ASC';

if ($sort !== 'popularity') {
    $products = sortProducts($products, $sort, $order);
}
?>

<div class="main-content">
    <div class="category-header">
        <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
        <p class="category-description"><?php echo htmlspecialchars($pageDescription); ?></p>
        
        <div class="sorting-options">
            <label for="sort">Sortera efter:</label>
            <select id="sort" onchange="updateSort(this.value)">
                <option value="popularity-desc" <?php echo ($sort === 'popularity' && $order === 'DESC') ? 'selected' : ''; ?>>
                    Popularitet
                </option>
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