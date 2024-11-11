<?php
$page_title = 'Sales Report';
require_once('includes/load.php');
require 'vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\UTCDateTime;
use MongoDB\BSON\ObjectId;

// Check permission level
page_require_level(1);

function page_require_level($required_level) {
    $uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
    $client = new Client($uri);
    $database = $client->selectDatabase('inventory_system');
    $admins = $database->selectCollection('admin');
    $admin = $admins->findOne(['_id' => $_SESSION['user_id']]);

    if (!isset($admin)) {
        redirect('index.php', false);
    }
    if ($admin['user_level'] <= (int)$required_level) {
        return true;
    } else {
        redirect('home.php', false);
    }
}

// MongoDB connection
$uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
$client = new Client($uri);
$database = $client->selectDatabase('inventory_system');
$sales_collection = $database->selectCollection('sales');
$product_collection = $database->selectCollection('product');

// Helper function to get start and end dates
function getDateRange($period) {
    $now = new DateTime();
    if ($period === 'weekly') {
        $start = (clone $now)->modify('-7 days');
    } elseif ($period === 'monthly') {
        $start = (clone $now)->modify('-30 days');
    } else {
        $start = (clone $now)->modify('-7 days');
    }
    return [
        'start' => new UTCDateTime($start->getTimestamp() * 1000),
        'end' => new UTCDateTime($now->getTimestamp() * 1000)
    ];
}

// Fetch weekly and monthly sales data
$weekly_sales_cursor = $sales_collection->aggregate([
    [
        '$match' => [
            'sale_date' => [
                '$gte' => getDateRange('weekly')['start'],
                '$lte' => getDateRange('weekly')['end']
            ]
        ]
    ],
    [
        '$group' => [
            '_id' => '$product_id',
            'total_sold' => ['$sum' => '$quantity_sold'],
            'total_amount' => ['$sum' => '$total_price']
        ]
    ]
]);

$monthly_sales_cursor = $sales_collection->aggregate([
    [
        '$match' => [
            'sale_date' => [
                '$gte' => getDateRange('monthly')['start'],
                '$lte' => getDateRange('monthly')['end']
            ]
        ]
    ],
    [
        '$group' => [
            '_id' => '$product_id',
            'total_sold' => ['$sum' => '$quantity_sold'],
            'total_amount' => ['$sum' => '$total_price']
        ]
    ]
]);

// Convert cursors to arrays
$weekly_sales = iterator_to_array($weekly_sales_cursor);
$monthly_sales = iterator_to_array($monthly_sales_cursor);

// Fetch product names for display
function getProductNames($product_ids, $product_collection) {
    $product_names = [];
    $products = $product_collection->find(['_id' => ['$in' => array_map(function($id) { return new ObjectId($id); }, $product_ids)]]);
    foreach ($products as $product) {
        $product_names[(string)$product['_id']] = $product['name'];
    }
    return $product_names;
}

// Gather unique product IDs for both weekly and monthly sales
$product_ids = array_unique(array_merge(
    array_map(fn($sale) => (string)$sale['_id'], $weekly_sales),
    array_map(fn($sale) => (string)$sale['_id'], $monthly_sales)
));
$product_names = getProductNames($product_ids, $product_collection);
?>

<?php include_once('layouts/header.php'); ?>
<?php include_once('layouts/admin_menu.php'); ?>

<link rel="stylesheet" href="libs/css/main.css" />

<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>

    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Weekly Sales Report</span>
                </strong>
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Total Sold</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($weekly_sales as $sale): ?>
                            <tr>
                                <td><?php echo isset($product_names[(string)$sale['_id']]) ? $product_names[(string)$sale['_id']] : 'Unknown Product'; ?></td>
                                <td><?php echo $sale['total_sold']; ?></td>
                                <td>₱ <?php echo number_format($sale['total_amount'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Monthly Sales Report</span>
                </strong>
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Total Sold</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($monthly_sales as $sale): ?>
                            <tr>
                                <td><?php echo isset($product_names[(string)$sale['_id']]) ? $product_names[(string)$sale['_id']] : 'Unknown Product'; ?></td>
                                <td><?php echo $sale['total_sold']; ?></td>
                                <td>₱ <?php echo number_format($sale['total_amount'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php //include_once('layouts/footer.php'); ?>
