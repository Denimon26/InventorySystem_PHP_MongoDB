<?php
require 'vendor/autoload.php';
require_once('includes/load.php');

use MongoDB\Client;

$page_title = 'Orders Page';

$uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
$client = new Client($uri);
$database = $client->selectDatabase('inventory_system');
$orders = $database->selectCollection('orders');
$users = $database->selectCollection('users');

$user = $users->findOne(['_id' => $_SESSION['user_id']]);
$username = isset($user['name']) ? ucfirst(remove_junk($user['name'])) : "Guest";

$pendingOrders = $orders->find(['status' => 'pending', 'username' => $username]);
$preparedOrders = $orders->find(['status' => 'prepared', 'username' => $username]);
$completedOrders = $orders->find(['status' => 'completed', 'username' => $username]);
?>

<?php include_once('layouts/header.php'); ?>

<link rel="stylesheet" href="libs/css/main.css" />
<div class="container">
  <h2 class="text-center">Your Orders</h2>

  <div class="text-center mb-4">
    <button class="btn btn-primary" onclick="showSection('pending-section')">Pending Orders</button>
    <button class="btn btn-warning" onclick="showSection('prepared-section')">Prepared/Ready for Pickup</button>
    <button class="btn btn-success" onclick="showSection('history-section')">Transaction History</button>
  </div>

  <div id="pending-section" class="order-section">
    <h3>Pending Orders</h3>
    <?php displayOrders($pendingOrders); ?>
  </div>

  <div id="prepared-section" class="order-section" style="display: none;">
    <h3>Prepared/Ready for Pickup</h3>
    <?php displayOrders($preparedOrders); ?>
  </div>

  <div id="history-section" class="order-section" style="display: none;">
    <h3>Transaction History</h3>
    <?php displayOrders($completedOrders); ?>
  </div>

</div>

<script>
  function showSection(sectionId) {
    const sections = document.querySelectorAll('.order-section');
    sections.forEach(section => section.style.display = 'none');
    document.getElementById(sectionId).style.display = 'block';
  }
</script>

<?php include_once('layouts/footer.php'); ?>

<?php
function displayOrders($orders) {
  foreach ($orders as $order):
?>
    <div class="order-ticket mb-4 p-3 border">
      <h5><strong>Order ID:</strong> <?php echo $order['_id']; ?></h5>
      <p><strong>Order Time:</strong> <?php echo $order['order_time']->toDateTime()->format('Y-m-d H:i:s'); ?></p>
      <p><strong>Total Order Price:</strong> ₱ <?php echo number_format($order['total_order_price'], 2); ?></p>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Product Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total Price</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($order['items'] as $item): ?>
            <tr>
              <td><?php echo htmlspecialchars($item['product_name']); ?></td>
              <td><?php echo htmlspecialchars($item['category']); ?></td>
              <td>₱ <?php echo number_format($item['price'], 2); ?></td>
              <td><?php echo $item['quantity']; ?></td>
              <td>₱ <?php echo number_format($item['total_price'], 2); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
<?php
  endforeach;
}
?>
