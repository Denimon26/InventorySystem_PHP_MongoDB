<?php
require 'vendor/autoload.php';
require_once('includes/load.php');

use MongoDB\Client;

$page_title = 'Sales Report';

$uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
$client = new Client($uri);
$database = $client->selectDatabase('inventory_system');
$orders = $database->selectCollection('orders');

$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';

$filter = ['status' => 'completed'];

if ($startDate && $endDate) {
    $filter['date_completed'] = [
        '$gte' => new MongoDB\BSON\UTCDateTime(strtotime($startDate) * 1000),
        '$lte' => new MongoDB\BSON\UTCDateTime(strtotime($endDate) * 1000)
    ];
}

$completedOrders = $orders->find($filter);
$completedOrdersArray = iterator_to_array($completedOrders);

$productSales = [];
$grandTotal = 0;

foreach ($completedOrdersArray as $order) {
    foreach ($order['items'] as $item) {
        $productName = $item['product_name'];
        $totalPrice = $item['total_price'];

        if (!isset($productSales[$productName])) {
            $productSales[$productName] = 0;
        }
        $productSales[$productName] += $totalPrice;
        $grandTotal += $totalPrice;
    }
}
?>

<?php include_once('layouts/header.php'); ?>
<?php include_once('layouts/admin_menu.php'); ?>

<link rel="stylesheet" href="libs/css/main.css" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
.container {
  padding-bottom: 100px; 
  padding-top: 20px;   
}
</style>

<div class="container">
  <h2 class="text-center">Sales Report</h2>

  <form method="GET" class="mb-4">
    <div class="row">
      <div class="col-md-4">
        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo $startDate; ?>" required>
      </div>
      <div class="col-md-4">
        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo $endDate; ?>" required>
      </div>
      <div class="col-md-4 d-flex align-items-end">
        <button type="submit" class="btn btn-primary">Filter</button>
      </div>
    </div>
  </form>

  <canvas id="salesChart" width="400" height="200"></canvas>

  <h3 class="mt-4">Completed Orders Table</h3>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Order ID</th>
        <th>Username</th>
        <th>Order Time</th>
        <th>Total Order Price</th>
        <th>Product Name</th>
        <th>Category</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Total Price</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($completedOrdersArray as $order): ?>
        <?php foreach ($order['items'] as $item): ?>
          <tr>
            <td><?php echo (string) $order['_id']; ?></td>
            <td><?php echo htmlspecialchars($order['username']); ?></td>
            <td><?php echo $order['order_time']->toDateTime()->format('Y-m-d H:i:s'); ?></td>
            <td>₱ <?php echo number_format($order['total_order_price'], 2); ?></td>
            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
            <td><?php echo htmlspecialchars($item['category']); ?></td>
            <td>₱ <?php echo number_format($item['price'], 2); ?></td>
            <td><?php echo $item['quantity']; ?></td>
            <td>₱ <?php echo number_format($item['total_price'], 2); ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="8" class="text-end"><strong>Grand Total:</strong></td>
        <td><strong>₱ <?php echo number_format($grandTotal, 2); ?></strong></td>
      </tr>
    </tfoot>
  </table>
</div>

<script>
  const ctx = document.getElementById('salesChart').getContext('2d');
  const salesData = {
    labels: <?php echo json_encode(array_keys($productSales)); ?>,
    datasets: [{
      label: 'Total Sales (₱)',
      data: <?php echo json_encode(array_values($productSales)); ?>,
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
