<?php
require 'vendor/autoload.php';
require_once('includes/load.php');

use MongoDB\Client;

$page_title = 'Admin Home Page';
$uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
$client = new Client($uri);
$database = $client->selectDatabase('inventory_system');
$orders = $database->selectCollection('orders');
$product = $database->selectCollection('product');

// Get total product count
$c_product = $product->countDocuments();

// Get last 5 completed transactions
$lastTransactionsCursor = $orders->find(
    ['status' => 'completed'],
    ['sort' => ['date_completed' => -1], 'limit' => 5]
);
$lastTransactions = iterator_to_array($lastTransactionsCursor);

// Define the date range for sales data aggregation (last 30 days)
$today = new DateTime('today');
$startOfDay = new MongoDB\BSON\UTCDateTime(strtotime('-30 days') * 1000); // 30 days ago
$endOfDay = new MongoDB\BSON\UTCDateTime(time() * 1000); // current time

// Define thresholds for fast and slow-moving products
$threshold_fast_moving = 50;  // Example threshold for fast-moving products
$threshold_slow_moving = 10;  // Example threshold for slow-moving products

// Aggregated sales data to classify products as fast/slow moving
$salesAggregation = $orders->aggregate([
    ['$match' => [
        'status' => 'completed',  // Only completed orders
    ]],
    ['$unwind' => '$items'],  // Flatten the items array in the order
    ['$group' => [
        '_id' => '$items.product_name',  // Group by product name
        'total_quantity' => ['$sum' => '$items.quantity'],  // Sum the quantity ordered
    ]],
]);

// Classify products based on their total ordered quantity
$fastMovingProducts = [];
$slowMovingProducts = [];

foreach ($salesAggregation as $sale) {
    if ($sale['total_quantity'] >= $threshold_fast_moving) {
        $fastMovingProducts[] = $sale['_id'];  // Fast-moving products
    } elseif ($sale['total_quantity'] <= $threshold_slow_moving) {
        $slowMovingProducts[] = $sale['_id'];  // Slow-moving products
    }
}

// Fetch the fast-moving and slow-moving products from the product collection
$fastMovingCursor = $product->find(['name' => ['$in' => $fastMovingProducts]]);
$slowMovingCursor = $product->find(['name' => ['$in' => $slowMovingProducts]]);
$fastMoving = iterator_to_array($fastMovingCursor);
$slowMoving = iterator_to_array($slowMovingCursor);


// Query for critical-level products (quantity < 20)
$criticalLevelProductsCursor = $product->find(['quantity' => ['$lt' => 20]]);
$criticalLevelProducts = iterator_to_array($criticalLevelProductsCursor);

// Get sales data for the last 6 days
$salesData = [];
$dates = [];
for ($i = 5; $i >= 0; $i--) {
    $date = clone $today;
    $date->modify("-$i days");
    $dates[] = $date->format('Y-m-d');

    $startOfDay = new MongoDB\BSON\UTCDateTime($date->getTimestamp() * 1000);
    $endOfDay = new MongoDB\BSON\UTCDateTime(($date->getTimestamp() + 86400) * 1000); // next day

    $salesForDay = $orders->aggregate([
        ['$match' => [
            'status' => 'completed',
            'date_completed' => ['$gte' => $startOfDay, '$lt' => $endOfDay]
        ]],
        ['$group' => ['_id' => null, 'total' => ['$sum' => '$total_order_price']]]
    ])->toArray();

    $salesData[] = $salesForDay ? $salesForDay[0]['total'] : 0;  // Default to 0 if no data
}
?>

<?php include_once('layouts/header.php'); ?>
<?php include_once('layouts/admin_menu.php'); ?>
<link rel="stylesheet" href="libs/css/main.css" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="row">
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

    <!-- Critical Level Products -->
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-warning-sign"></span>
                    <span>Critical Level Products</span>
                </strong>
            </div>
            <div class="panel-body">
                <ul class="list-group">
                    <?php
                    if (empty($criticalLevelProducts)) {
                        echo "<li class='list-group-item'>No critical level products found</li>";
                    } else {
                        foreach ($criticalLevelProducts as $prod) {
                            echo "<li class='list-group-item'>" . htmlspecialchars($prod['name']) . " - Quantity: " . $prod['quantity'] . "</li>";
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Fast Moving Products -->
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-arrow-up"></span>
                    <span>Fast Moving Products</span>
                </strong>
            </div>
            <div class="panel-body">
                <ul class="list-group">
                    <?php
                    if (empty($fastMoving)) {
                        echo "<li class='list-group-item'>No fast-moving products found</li>";
                    } else {
                        foreach ($fastMoving as $prod) {
                            echo "<li class='list-group-item'>" . htmlspecialchars($prod['name']) . "</li>";
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Slow Moving Products -->
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-arrow-down"></span>
                    <span>Slow Moving Products</span>
                </strong>
            </div>
            <div class="panel-body">
                <ul class="list-group">
                    <?php
                    if (empty($slowMoving)) {
                        echo "<li class='list-group-item'>No slow-moving products found</li>";
                    } else {
                        foreach ($slowMoving as $prod) {
                            echo "<li class='list-group-item'>" . htmlspecialchars($prod['name']) . "</li>";
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Last 5 Transactions -->
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-list-alt"></span>
                    <span>Last 5 Transactions</span>
                </strong>
            </div>
            <div class="panel-body">
                <ul class="list-group">
                    <?php foreach ($lastTransactions as $transaction): ?>
                        <li class="list-group-item">
                            <strong><?php echo htmlspecialchars($transaction['username']); ?></strong>
                            - <?php echo $transaction['date_completed']->toDateTime()->format('Y-m-d H:i:s'); ?>
                            <span class="badge">₱ <?php echo number_format($transaction['total_order_price'], 2); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-signal"></span>
                    <span>Sales Today vs Last 5 Days</span>
                </strong>
            </div>
            <div class="panel-body">
                <canvas id="salesChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

</div>

<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesData = {
        labels: <?php echo json_encode($dates); ?>,
        datasets: [{
            label: 'Total Sales (₱)',
            data: <?php echo json_encode($salesData); ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    };

    new Chart(ctx, {
        type: 'bar',
        data: salesData,
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<?php //include_once('layouts/footer.php'); ?>
