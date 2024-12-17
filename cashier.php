<?php
require 'vendor/autoload.php';
require_once('includes/load.php');

use MongoDB\Client;

$page_title = 'Cashier Orders Management';

$uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
$client = new Client($uri);
$database = $client->selectDatabase('inventory_system');
$orders = $database->selectCollection('orders');

$pendingOrders = $orders->find(['status' => 'pending']);
$preparedOrders = $orders->find(['status' => 'prepared']);

$usernameFilter = isset($_POST['username']) ? $_POST['username'] : null;
$completedOrders = $usernameFilter ? $orders->find(['status' => 'completed', 'username' => $usernameFilter]) : [];
?>

<?php include_once('layouts/header.php'); ?>

<link rel="stylesheet" href="libs/css/main.css" />
<div class="container">
  <h2 class="text-center">Cashier Orders Management</h2>

  <div class="text-center mb-4">
    <button class="btn btn-primary" onclick="showSection('pending-section')">Pending Orders</button>
    <button class="btn btn-warning" onclick="showSection('prepared-section')">Prepared Orders</button>
    <button class="btn btn-success" onclick="showSection('history-section')">Transaction History</button>
  </div>

  <div id="pending-section" class="order-section">
    <h3>Pending Orders</h3>
    <?php displayOrders($pendingOrders, 'pending'); ?>
  </div>

  <div id="prepared-section" class="order-section" style="display: none;">
    <h3>Prepared Orders</h3>
    <?php displayOrders($preparedOrders, 'prepared'); ?>
  </div>

  <div id="history-section" class="order-section" style="display: none;">
    <h3>Transaction History</h3>
    <form method="POST" class="mb-3">
      <input type="text" name="username" placeholder="Enter Username" class="form-control" required />
      <button type="submit" class="btn btn-primary mt-2">Fetch Transactions</button>
    </form>

    <?php if ($usernameFilter): ?>
      <h4>Transaction History for: <?php echo htmlspecialchars($usernameFilter); ?></h4>
      <?php displayOrders($completedOrders, 'completed'); ?>
    <?php endif; ?>
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
function displayOrders($orders, $status) {
  foreach ($orders as $order):
    $orderId = (string)$order['_id']; 
?>
    <div class="order-ticket mb-4 p-3 border">
      <h5><strong>Order ID:</strong> <?php echo $orderId; ?></h5>
      <p><strong>Username:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
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

      <div class="mt-3">
        <?php if ($status === 'pending'): ?>
          <form method="POST" action="update_order.php">
            <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
            <input type="hidden" name="action" value="ready">
            <button type="submit" class="btn btn-success">Ready</button>
          </form>

          <form method="POST" action="update_order.php">
            <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
            <input type="hidden" name="action" value="cancel">
            <button type="submit" class="btn btn-danger">Cancel</button>
          </form>

        <?php elseif ($status === 'prepared'): ?>
          <form method="POST" action="update_order.php">
            <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
            <input type="hidden" name="action" value="unprepare">
            <button type="submit" class="btn btn-secondary">Unprepare</button>
          </form>

          <form method="POST" action="update_order.php">
            <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
            <input type="hidden" name="action" value="cancel">
            <button type="submit" class="btn btn-danger">Cancel</button>
          </form>

          <form method="POST" action="update_order.php">
            <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
            <input type="hidden" name="action" value="paid">
            <button type="submit" class="btn btn-success">Paid</button>
          </form>
        <?php endif; ?>
      </div>
    </div>
<?php
  endforeach;
}
?>

