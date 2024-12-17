<?php
require 'vendor/autoload.php';
require_once('includes/load.php');

use MongoDB\Client;

$page_title = 'Cart Page';

$uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
$client = new Client($uri);
$database = $client->selectDatabase('inventory_system');
$orders = $database->selectCollection('orders');
$users = $database->selectCollection('users');

$user = $users->findOne(['_id' => $_SESSION['user_id']]);

$username = isset($user['name']) ? ucfirst(remove_junk($user['name'])) : "Guest";


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $cart = json_decode($_POST['cart'], true);
    $order_time = new MongoDB\BSON\UTCDateTime((new DateTime())->getTimestamp() * 1000);
    $order_id = new MongoDB\BSON\ObjectId(); 

    if (!empty($cart)) {
        $total_order_price = 0;

        $items = [];
        foreach ($cart as $item) {
            $item_total_price = $item['price'] * $item['quantity'];
            $total_order_price += $item_total_price;

            $items[] = [
                'product_name' => $item['name'],
                'category' => $item['category'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'total_price' => $item_total_price
            ];
        }

        $orders->insertOne([
            '_id' => $order_id,
            'username' => $username,
            'items' => $items,
            'total_order_price' => $total_order_price,
            'status' => 'pending',
            'order_time' => $order_time
        ]);

        echo "<script>
                localStorage.removeItem('cart');
                alert('Order placed successfully!');
                window.location.href = 'cart.php';
              </script>";
    }
}

?>

<?php include_once('layouts/header.php'); ?>

<link rel="stylesheet" href="libs/css/main.css" />
<div class="row">
  <div class="col-md-12">
    <h2 class="text-center">Your Shopping Cart</h2>
    <form method="POST" id="order-form">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Product Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total Price</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="cart-items">
        </tbody>
        <tfoot>
  <tr>
    <td colspan="4" class="text-right"><strong>Total Price:</strong></td>
    <td id="total-price"><strong>₱ 0.00</strong></td>
    <td></td>
  </tr>
</tfoot>

      </table>
      <div class="text-right">
        <button type="button" class="btn btn-danger" onclick="clearCart()">Clear Cart</button>
        <button type="submit" name="place_order" class="btn btn-success" onclick="prepareOrder()">Place Order</button>
        <input type="hidden" name="cart" id="cart-data">
      </div>
    </form>
  </div>
</div>

<script>
  function displayCart() {
  let cart = JSON.parse(localStorage.getItem('cart')) || [];
  let cartItemsContainer = document.getElementById('cart-items');
  let totalPriceContainer = document.getElementById('total-price');
  let totalPrice = 0;

  cartItemsContainer.innerHTML = '';

  if (cart.length === 0) {
    cartItemsContainer.innerHTML = '<tr><td colspan="6" class="text-center">Your cart is empty.</td></tr>';
    totalPriceContainer.innerHTML = '<strong>₱ 0.00</strong>';
    return;
  }

  cart.forEach((item, index) => {
    let itemTotal = item.price * item.quantity;
    totalPrice += itemTotal;

    let row = `
      <tr>
        <td>${item.name}</td>
        <td>${item.category}</td>
        <td>₱ ${item.price.toFixed(2)}</td>
        <td>${item.quantity}</td>
        <td>₱ ${itemTotal.toFixed(2)}</td>
        <td>
          <button class="btn btn-danger btn-sm" onclick="removeFromCart(${index})">Remove</button>
        </td>
      </tr>
    `;
    cartItemsContainer.innerHTML += row;
  });

  totalPriceContainer.innerHTML = `<strong>₱ ${totalPrice.toFixed(2)}</strong>`;
}


  function removeFromCart(index) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cart));
    displayCart();
    updateCartCount();
  }

  function clearCart() {
    if (confirm('Are you sure you want to clear the cart?')) {
      localStorage.removeItem('cart');
      displayCart();
      updateCartCount();
    }
  }

  function prepareOrder() {
    let cart = localStorage.getItem('cart') || '[]';
    document.getElementById('cart-data').value = cart;
  }

  document.addEventListener('DOMContentLoaded', displayCart);
</script>

<?php include_once('layouts/footer.php'); ?>
