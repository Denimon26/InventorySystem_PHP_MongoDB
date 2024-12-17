<?php
  use MongoDB\Client;
  require 'vendor/autoload.php';
  require_once('includes/load.php');



  $page_title = 'Home Page';

  $uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
  $client = new Client($uri);
  $database = $client->selectDatabase('inventory_system');
  $product = $database->selectCollection('product');
  $categories = $database->selectCollection('categories');

  $all_products = $product->find();
  $all_categories = $categories->find();

  // Get selected category from dropdown
  $selected_category = isset($_POST['category']) ? $_POST['category'] : '';
?>

<?php include_once('layouts/header.php'); ?>
<link rel="stylesheet" href="libs/css/main.css" />
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th-list"></span>
          <span>Available Products</span>
        </strong>
      </div>
      <div class="panel-body">
        
        <!-- Category Selection Form -->
        <form method="POST" action="">
          <div class="form-group">
            <label for="category">Filter by Category:</label>
            <select name="category" id="category" class="form-control" onchange="this.form.submit()">
              <option value="">All Categories</option>
              <?php foreach ($all_categories as $category): ?>
                <option value="<?php echo $category['name']; ?>" <?php if ($category['name'] === $selected_category) echo 'selected'; ?>>
                  <?php echo $category['name']; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </form>

        <!-- Products Table -->
<!-- Products Table -->
<table class="table table-bordered">
  <thead>
    <tr>
      <th> Product Name </th>
      <th> Category </th>
      <th> Price </th>
      <th> Quantity Available </th>
      <th> Quantity to Add </th>
      <th> Action </th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($all_products as $product): ?>
      <?php if ($selected_category === '' || $product['categories'] === $selected_category): ?>
        <tr>
          <td><?php echo remove_junk($product['name']); ?></td>
          <td><?php echo remove_junk($product['categories']); ?></td>
          <td>₱ <?php echo number_format($product['buy_price'], 2); ?></td>
          <td><?php echo remove_junk($product['quantity']); ?></td>
          <td>
            <input type="number" id="qty-<?php echo $product['name']; ?>" class="form-control" min="1" max="<?php echo $product['quantity']; ?>" value="1" />
          </td>
          <td>
            <button class="btn btn-success" onclick="addToCart('<?php echo $product['name']; ?>', '<?php echo $product['categories']; ?>', <?php echo $product['buy_price']; ?>, 'qty-<?php echo $product['name']; ?>')">
              Add to Cart
            </button>
          </td>
        </tr>
      <?php endif; ?>
    <?php endforeach; ?>
  </tbody>
</table>


      </div>
    </div>
  </div>
</div>

<script>
function addToCart(name, category, price, qtyInputId) {
  const quantity = parseInt(document.getElementById(qtyInputId).value) || 1;

  const item = { name, category, price, quantity };

  let cart = JSON.parse(localStorage.getItem('cart')) || [];

  cart.push(item);

  localStorage.setItem('cart', JSON.stringify(cart));

  updateCartCount();

  alert(`${quantity} x ${name} has been added to your cart.`);
}

</script>

<?php include_once('layouts/footer.php'); ?>
