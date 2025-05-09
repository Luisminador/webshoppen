<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

$search = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_STRING);
$category_id = filter_input(INPUT_GET, 'category', FILTER_VALIDATE_INT);

if (!$search) {
    header('Location: /');
    exit();
}

// Förbered sökfrågan
$query = '
    SELECT products.*, categories.name as category_name 
    FROM products 
    LEFT JOIN categories ON products.category_id = categories.id 
    WHERE products.name LIKE :search 
    OR products.description LIKE :search
';

$params = [':search' => "%$search%"];

// Lägg till kategorifiltret om det finns
if ($category_id) {
    $query .= ' AND category_id = :category_id';
    $params[':category_id'] = $category_id;
}

$query .= ' ORDER BY products.name ASC';

// Hämta alla kategorier för filtret
$categories = $pdo->query('SELECT * FROM categories ORDER BY name ASC')->fetchAll();

// Utför sökningen
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<div class="search-header">
    <h1>Sökresultat för "<?= sanitize($search) ?>"</h1>
    
    <form method="GET" class="search-filters">
        <input type="hidden" name="q" value="<?= sanitize($search) ?>">
        
        <div class="form-group">
            <label for="category">Filtrera på kategori:</label>
            <select name="category" id="category" onchange="this.form.submit()">
                <option value="">Alla kategorier</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" 
                            <?= $category_id == $category['id'] ? 'selected' : '' ?>>
                        <?= sanitize($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
</div>

<?php if (empty($products)): ?>
    <p>Inga produkter hittades som matchar din sökning.</p>
    
    <div class="search-suggestions">
        <h2>Tips för bättre sökresultat:</h2>
        <ul>
            <li>Kontrollera stavningen</li>
            <li>Använd färre sökord</li>
            <li>Prova med mer generella söktermer</li>
            <li>Ta bort kategorifiltret om det är aktivt</li>
        </ul>
    </div>
<?php else: ?>
    <p>Hittade <?= count($products) ?> produkt(er)</p>
    
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <?php if ($product['image_url']): ?>
                    <img src="<?= sanitize($product['image_url']) ?>" 
                         alt="<?= sanitize($product['name']) ?>">
                <?php endif; ?>
                
                <h3><?= sanitize($product['name']) ?></h3>
                <p class="category"><?= sanitize($product['category_name']) ?></p>
                <p class="price"><?= number_format($product['price'], 2) ?> kr</p>
                
                <?php if ($product['stock'] > 0): ?>
                    <p class="stock">I lager: <?= $product['stock'] ?> st</p>
                <?php else: ?>
                    <p class="out-of-stock">Tillfälligt slut i lager</p>
                <?php endif; ?>
                
                <div class="product-actions">
                    <a href="/product.php?id=<?= $product['id'] ?>" class="btn">
                        Visa produkt
                    </a>
                    
                    <?php if (isLoggedIn() && $product['stock'] > 0): ?>
                        <form method="POST" action="/product.php?id=<?= $product['id'] ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn">Köp</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?> 