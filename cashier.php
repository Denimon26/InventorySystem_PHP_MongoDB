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

//define('PRE_ORDER_TIME_LIMIT', 86,400);
define('PRE_ORDER_TIME_LIMIT', 60);
?>

<?php include_once('layouts/header.php'); ?>
<?php include_once('layouts/admin_menu.php'); ?>

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
    <div id="pending-orders-container">
      <?php displayOrders($pendingOrders, 'pending'); ?>
    </div>
  </div>

  <div id="prepared-section" class="order-section" style="display: none;">
    <h3>Prepared Orders</h3>
    <div id="prepared-orders-container">
      <?php displayOrders($preparedOrders, 'prepared'); ?>
    </div>
  </div>

  <div id="history-section" class="order-section" style="display: none;">
    <h3>Transaction History</h3>
    <div class="mb-3">
      <input type="text" id="usernameInput" placeholder="Enter Username" class="form-control" required />
      <button id="fetchTransactionsBtn" class="btn btn-primary mt-2">Fetch Transactions</button>
    </div>

    <h4 id="transactionHistoryTitle" style="display: none;"></h4>
    <div id="completedOrdersContainer"></div>
  </div>
</div>

<script>
  // Show and hide different sections
  function showSection(sectionId) {
    const sections = document.querySelectorAll('.order-section');
    sections.forEach(section => section.style.display = 'none');
    document.getElementById(sectionId).style.display = 'block';
  }

  // Auto-fetch orders every 10 seconds for pending and prepared orders
  setInterval(function() {
    fetchOrders('pending');
    fetchOrders('prepared');
  }, 10000); // 10 seconds

  // Function to fetch orders and update the DOM
  function fetchOrders(status) {
    fetch(`fetch_orders.php?status=${status}`)
      .then(response => response.json())
      .then(data => {
        if (status === 'pending') {
          updateOrdersContainer('pending-orders-container', data);
        } else if (status === 'prepared') {
          updateOrdersContainer('prepared-orders-container', data);
        }
      })
      .catch(error => console.error('Error fetching orders:', error));
  }

  // Function to update the order container with new data
  function updateOrdersContainer(containerId, orders) {
    const container = document.getElementById(containerId);
    container.innerHTML = ''; // Clear existing content

    orders.forEach(order => {
      const orderHTML = `
        <div class="order-ticket mb-4 p-3 border">
          <h5><strong>Order ID:</strong> ${order._id}</h5>
          <p><strong>Username:</strong> ${order.username}</p>
          <p><strong>Number:</strong> ${order.number || 'N/A'}</p>
          <p><strong>Email:</strong> ${order.email || 'N/A'}</p>
          <p><strong>Order Time:</strong> ${order.order_time}</p>
          <p><strong>Total Order Price:</strong> ₱ ${parseFloat(order.total_order_price).toFixed(2)}</p>
          <p><strong>Status:</strong> ${order.status}</p>
        </div>
      `;
      container.innerHTML += orderHTML;
    });
  }

  // Fetch transaction history for a specific username
  document.getElementById('fetchTransactionsBtn').addEventListener('click', function () {
    const username = document.getElementById('usernameInput').value;
    const container = document.getElementById('completedOrdersContainer');
    const title = document.getElementById('transactionHistoryTitle');

    if (!username) {
      alert("Please enter a username.");
      return;
    }

    fetch('fetch_transactions.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `username=${encodeURIComponent(username)}`
    })
      .then(response => response.json())
      .then(data => {
        container.innerHTML = '';
        title.style.display = 'block';
        title.textContent = `Transaction History for: ${username}`;

        if (data.length === 0) {
          container.innerHTML = '<p>No transactions found for this user.</p>';
          return;
        }

        data.forEach(order => {
          const orderHTML = `
            <div class="order-ticket mb-4 p-3 border">
              <h5><strong>Order ID:</strong> ${order._id}</h5>
              <p><strong>Username:</strong> ${order.username}</p>
              <p><strong>Order Time:</strong> ${order.order_time}</p>
              <p><strong>Total Order Price:</strong> ₱ ${parseFloat(order.total_order_price).toFixed(2)}</p>
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
                  ${order.items.map(item => `
                    <tr>
                      <td>${item.product_name}</td>
                      <td>${item.category}</td>
                      <td>₱ ${parseFloat(item.price).toFixed(2)}</td>
                      <td>${item.quantity}</td>
                      <td>₱ ${parseFloat(item.total_price).toFixed(2)}</td>
                    </tr>
                  `).join('')}
                </tbody>
              </table>
            </div>`;
          container.innerHTML += orderHTML;
        });
      })
      .catch(error => {
        console.error('Error:', error);
        container.innerHTML = '<p>Failed to fetch transactions. Please try again.</p>';
      });
  });
</script>

<?php
// Function to display orders
function displayOrders($ordersCursor, $status)
{
    global $orders; // Access the orders collection globally

    foreach ($ordersCursor as $order):
        $orderId = (string) $order['_id']; // Convert ObjectId to string
        $orderTime = $order['order_time']->toDateTime(); // Convert MongoDB DateTime to PHP DateTime
        $currentTime = new DateTime(); // Current time
        $timeDifference = $currentTime->getTimestamp() - $orderTime->getTimestamp(); // Calculate time difference
        $isExpired = $timeDifference > PRE_ORDER_TIME_LIMIT; // Check if order is expired

        // Handle expired orders
        if ($isExpired) {
            $orders->deleteOne(['_id' => $order['_id']]); // Delete expired order
            continue; // Skip displaying expired orders
        }
        ?>
        <div class="order-ticket mb-4 p-3 border">
            <h5><strong>Order ID:</strong> <?php echo $orderId; ?></h5>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
            <p><strong>Number:</strong> <?php echo htmlspecialchars($order['number'] ?? 'N/A'); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email'] ?? 'N/A'); ?></p>
            <p><strong>Order Time:</strong> <?php echo $orderTime->format('Y-m-d H:i:s'); ?></p>
            <p><strong>Total Order Price:</strong> ₱ <?php echo number_format($order['total_order_price'], 2); ?></p>
            <p><strong>Status:</strong> <?php echo $isExpired ? '<span class="text-danger">Expired</span>' : '<span class="text-success">Active</span>'; ?></p>

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
                        <button type="submit" class="btn btn-success" <?php echo $isExpired ? 'disabled' : ''; ?>>Ready</button>
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
                        <button type="submit" class="btn btn-secondary" <?php echo $isExpired ? 'disabled' : ''; ?>>Unprepare</button>
                    </form>

                    <form method="POST" action="update_order.php">
                        <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
                        <input type="hidden" name="action" value="cancel">
                        <button type="submit" class="btn btn-danger">Cancel</button>
                    </form>

                    <form method="POST" action="update_order.php">
                        <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
                        <input type="hidden" name="action" value="paid">
                        <button type="submit" class="btn btn-success" <?php echo $isExpired ? 'disabled' : ''; ?>>Paid</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <?php
    endforeach;
}
?>
