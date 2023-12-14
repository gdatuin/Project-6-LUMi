<?php
session_start();
include_once 'connect.php';

$sortOption = filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_STRING);
$searchTerm = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING);
$typeFilter = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);

$sort = 'product_name';
$order = 'ASC';


$categories = [];
try {
    $statement = $db->prepare("SELECT * FROM categories");
    $statement->execute();
    $categories = $statement->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}


$sortOptions = [
    'product_name_asc' => ['field' => 'product_name', 'order' => 'ASC'],
    'product_name_desc' => ['field' => 'product_name', 'order' => 'DESC'],
    'price_asc' => ['field' => 'price', 'order' => 'ASC'],
    'price_desc' => ['field' => 'price', 'order' => 'DESC'],
    'rating_asc' => ['field' => 'average_rating', 'order' => 'ASC'],
    'rating_desc' => ['field' => 'average_rating', 'order' => 'DESC'],
];

if (array_key_exists($sortOption, $sortOptions)) {
    $sort = $sortOptions[$sortOption]['field'];
    $order = $sortOptions[$sortOption]['order'];
}


$countquery = "SELECT COUNT(*) FROM products p LEFT JOIN categories c ON p.category_id = c.category_id";
$paramsCount = [];


if ($searchTerm) {
    $countquery .= " WHERE (p.product_name LIKE :searchTerm OR c.category_name LIKE :searchTerm)";
    $paramsCount[':searchTerm'] = '%' . $searchTerm . '%';
}

if ($typeFilter && $typeFilter != 'all') {
    $countquery .= ($searchTerm ? " AND" : " WHERE") . " c.category_name = :typeFilter";
    $paramsCount[':typeFilter'] = $typeFilter;
}


$countStatement = $db->prepare($countquery);
foreach ($paramsCount as $key => $value) {
    $countStatement->bindValue($key, $value);
}
$countStatement->execute();
$totalProducts = $countStatement->fetchColumn();


$productsPerPage = 8;
$totalPages = ceil($totalProducts / $productsPerPage);
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
$page = max($page, 1);
$page = min($page, $totalPages);
$offset = ($page - 1) * $productsPerPage;


if ($offset < 0) {
    $offset = 0;
}


$query = "SELECT p.product_id, p.product_name, p.description, p.price, p.inventory_count, p.image, c.category_name, COALESCE(AVG(r.rating), 0) AS average_rating FROM products p LEFT JOIN reviews r ON p.product_id = r.product_id LEFT JOIN categories c ON p.category_id = c.category_id";
$params = [];


if ($searchTerm) {
    $query .= " WHERE (p.product_name LIKE :searchTerm OR c.category_name LIKE :searchTerm)";
    $params[':searchTerm'] = '%' . $searchTerm . '%';
}

if ($typeFilter && $typeFilter != 'all') {
    $query .= $searchTerm ? " AND" : " WHERE";
    $query .= " c.category_name = :typeFilter";
    $params[':typeFilter'] = $typeFilter;
}


$query .= " GROUP BY p.product_id ORDER BY $sort $order LIMIT :limit OFFSET :offset";

$statement = $db->prepare($query);
foreach ($params as $key => $value) {
    $statement->bindValue($key, $value);
}
$statement->bindValue(':limit', $productsPerPage, PDO::PARAM_INT);
$statement->bindValue(':offset', $offset, PDO::PARAM_INT);

try {
    $statement->execute();
    $products = $statement->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $products = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Products - LUMi</title>
    <link rel="stylesheet" href="styles.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bruno+Ace&family=Fugaz+One&family=Russo+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Days+One&display=swap" rel="stylesheet">
    <script src="https://static.elfsight.com/platform/platform.js" data-use-service-core defer></script>
    <meta name="viewport" content="width=device-width">
    <link rel="shortcut icon" href="#">
</head>

<body id="products">
    <div class="elfsight-app-4114d580-7b3f-4432-b30a-d4699aac173d"></div> <?php include 'header.php'; ?> <main id="productsContent">
        <div class="productsHeader">
            <h1>PRODUCTS</h1>
        </div>
        <div class="search-form-container">
            <form action="" method="get">
                <label for="search">Search:</label>
                <input type="text" name="search" id="search" value="<?= htmlspecialchars($searchTerm) ?>">
                <input type="submit" value="Search">
            </form>
        </div> <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'content_manager', 'sales_manager'])): ?> <a href="add-product.php" class="add-product-button">Add Product</a> <?php endif; ?> <div class="sorting-options">
            <form action="" method="get">
                <input type="hidden" name="type" value="<?= htmlspecialchars($typeFilter) ?>">
                <input type="hidden" name="search" value="<?= htmlspecialchars($searchTerm) ?>">
                <label for="sorting">Sort by:</label>
                <select name="sort" id="sorting" onchange="this.form.submit()">
                    <option value="product_name_asc" <?php if ($sortOption == 'product_name_asc') echo 'selected'; ?>>Name (A-Z)</option>
                    <option value="product_name_desc" <?php if ($sortOption == 'product_name_desc') echo 'selected'; ?>>Name (Z-A)</option>
                    <option value="price_asc" <?php if ($sortOption == 'price_asc') echo 'selected'; ?>>Price (Low to High)</option>
                    <option value="price_desc" <?php if ($sortOption == 'price_desc') echo 'selected'; ?>>Price (High to Low)</option>
                    <option value="rating_asc" <?php if ($sortOption == 'rating_asc') echo 'selected'; ?>>Rating (Low to High)</option>
                    <option value="rating_desc" <?php if ($sortOption == 'rating_desc') echo 'selected'; ?>>Rating (High to Low)</option>
                </select>
            </form>
            <nav class="product-type-nav">
                <a href="?type=all<?= $searchTerm ? '&search=' . urlencode($searchTerm) : '' ?>&sort=<?= urlencode($sortOption) ?>">View All</a> <?php foreach ($categories as $category): ?> <a href="?type=<?= urlencode($category['category_name']) ?><?= $searchTerm ? '&search=' . urlencode($searchTerm) : '' ?>&sort=<?= urlencode($sortOption) ?>"><?= htmlspecialchars($category['category_name']) ?></a> <?php endforeach; ?>
            </nav>
        </div>
        <div class="listOfProducts"> <?php if (empty($products)): ?> <div class="no-results-found">
                <p>No results found.</p>
            </div> <?php else: ?> <?php foreach ($products as $product): ?> <div class="productItem">
                <div class="productImage">
                    <a href="product.php?id=<?= htmlspecialchars($product['product_id']) ?>">
                        <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
                    </a>
                </div>
                <div class="productDetails">
                    <p>
                        <a href="product.php?id=<?= htmlspecialchars($product['product_id']) ?>"><?= htmlspecialchars($product['product_name']) ?></a>
                        <br>$<?= htmlspecialchars(number_format($product['price'], 2)) ?>
                    </p>
                </div>
            </div> <?php endforeach; ?> <?php endif; ?> </div>
        <div class="pages"> <?php for ($i = 1; $i <= $totalPages; $i++): ?> <a href="?page=<?= $i ?>&<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a> <?php endfor; ?> </div>
    </main> <?php include 'footer.php'; ?>
</body>

</html>