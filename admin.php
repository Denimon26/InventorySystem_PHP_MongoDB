<?php
$page_title = 'Admin Home Page';
require_once('includes/load.php');
require 'vendor/autoload.php';

use MongoDB\Client;

// Check user permission level
function page_require_level($required_level) {
    $uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
    $client = new Client($uri);
    $database = $client->selectDatabase('inventory_system');
    $admins = $database->selectCollection('admin');

    // Check if user is logged in and has required permission level
    $admin = $admins->findOne(['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])]);
    if (!isset($admin) || $admin['user_level'] > (int)$required_level) {
        header('Location: index.php');
        exit;
    }
}

// Require admin level 3 or higher
page_require_level(3);

// MongoDB connection
$uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
$client = new Client($uri);
$database = $client->selectDatabase('inventory_system');

// Collections
$categories = $database->selectCollection('categories');
$product = $database->selectCollection('product');
$users = $database->selectCollection('users');
$salesCollection = $database->selectCollection('sales');

// Count totals
$c_categorie = $categories->countDocuments();
$c_product = $product->countDocuments();
$c_user = $users->countDocuments();

// Fetch recent products
$recent_products_cursor = $product->find([], ['sort' => ['date' => -1], 'limit' => 5]);
$recent_products = iterator_to_array($recent_products_cursor);

// Fetch low quantity products
$low_limit = 99;
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

$products_query = [];
if ($filter === 'critical') {
    $products_query = ['quantity' => ['$lt' => 20]];
} elseif ($filter === 'low') {
    $products_query = ['quantity' => ['$gte' => 20, '$lt' => $low_limit]];
} else {
    $products_query = ['quantity' => ['$lt' => $low_limit]];
}
$products_by_quantity = $product->find($products_query, ['sort' => ['quantity' => 1]])->toArray();

// Fetch fast and slow-moving products
$fast_moving_sales_threshold = 50;
$slow_moving_sales_threshold = 10;

$fast_moving_query = ['sales' => ['$gte' => $fast_moving_sales_threshold]];
$slow_moving_query = ['sales' => ['$lt' => $slow_moving_sales_threshold]];

$fast_moving_products = iterator_to_array($product->find($fast_moving_query, ['sort' => ['sales' => -1]]));
$slow_moving_products = iterator_to_array($product->find($slow_moving_query, ['sort' => ['sales' => 1]]));
?>

<?php include_once('layouts/header.php'); ?>
<?php include_once('layouts/admin_menu.php'); ?>
<link rel="stylesheet" href="libs/css/main.css" />

<div class="row">
    <!-- Dashboard panels -->
    <a href="product.php" class="product-link">
        <div class="col-quarter">
            <div class="panel-product panel-box clearfix">
                <div class="panel-icon bg-blue2">
                    <i class="glyphicon glyphicon-shopping-cart"></i>
                </div>
                <div class="panel-value">
                    <h2 class="panel-count"><?php echo $c_product; ?></h2>
                    <p class="panel-text">Products</p>
                </div>
            </div>
        </div>
    </a>
</div>

<div class="row">
    <!-- Low Quantity Products -->
    <div class="col-third">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th-list"></span>
                    <span>Low Quantity Products</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="get" action="">
                    <label for="filter">Filter by:</label>
                    <select name="filter" id="filter" class="form-control" onchange="this.form.submit()">
                        <option value="all" <?php if ($filter == 'all') echo 'selected'; ?>>All</option>
                        <option value="critical" <?php if ($filter == 'critical') echo 'selected'; ?>>Critical</option>
                        <option value="low" <?php if ($filter == 'low') echo 'selected'; ?>>Low</option>
                    </select>
                </form>
                <ul class="list-group">
                    <?php if (empty($products_by_quantity)) : ?>
                        <li class="list-group-item">No products found</li>
                    <?php else : ?>
                        <?php foreach ($products_by_quantity as $prod) : ?>
                            <li class="list-group-item">
                                <span class="badge"><?php echo $prod['quantity']; ?></span>
                                <?php echo htmlspecialchars($prod['name']); ?>
                                <?php if ($prod['quantity'] < 20) : ?>
                                    <span class="label label-danger pull-right">Critical</span>
                                <?php elseif ($prod['quantity'] >= 20 && $prod['quantity'] < $low_limit) : ?>
                                    <span class="label label-warning pull-right">Low</span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Fast-Moving Products -->
    <div class="col-third">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th-list"></span>
                    <span>Fast-Moving Products</span>
                </strong>
            </div>
            <div class="panel-body">
                <ul class="list-group">
                    <?php if (empty($fast_moving_products)) : ?>
                        <li class="list-group-item">No fast-moving products found</li>
                    <?php else : ?>
                        <?php foreach ($fast_moving_products as $product) : ?>
                            <li class="list-group-item">
                                <span class="badge">Sales: <?php echo $product['sales']; ?></span>
                                <strong><?php echo htmlspecialchars($product['name']); ?></strong> 
                                <br>
                                Stock: <?php echo $product['quantity']; ?>
                                <span class="label label-success pull-right">Fast-Moving</span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Slow-Moving Products -->
    <div class="col-third">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th-list"></span>
                    <span>Slow-Moving Products</span>
                </strong>
            </div>
            <div class="panel-body">
                <ul class="list-group">
                    <?php if (empty($slow_moving_products)) : ?>
                        <li class="list-group-item">No slow-moving products found</li>
                    <?php else : ?>
                        <?php foreach ($slow_moving_products as $product) : ?>
                            <li class="list-group-item">
                                <span class="badge">Sales: <?php echo $product['sales']; ?></span>
                                <strong><?php echo htmlspecialchars($product['name']); ?></strong> 
                                <br>
                                Stock: <?php echo $product['quantity']; ?>
                                <span class="label label-danger pull-right">Slow-Moving</span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php // include_once('layouts/footer.php'); ?>
