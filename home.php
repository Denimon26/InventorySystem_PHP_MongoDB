<?php
  use MongoDB\Client;
  require 'vendor/autoload.php';
  require_once('includes/load.php');

  if (property_exists($session, 'message') && str_contains($session->message, 'admin')) {
    echo '<script>console.log('.json_encode($session).')</script>';
  }

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
        <table class="table table-bordered">
          <thead>
            <tr>
              <th> Product Name </th>
              <th> Category </th>
              <th> Price </th>
              <th> Quantity </th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($all_products as $product): ?>
              <?php if ($selected_category === '' || $product['categories'] === $selected_category): ?>
                <tr>
                  <td><?php echo remove_junk($product['name']); ?></td>
                  <td><?php echo remove_junk($product['categories']); ?></td>
                  <td><?php echo number_format($product['buy_price'], 2); ?> $</td>
                  <td><?php echo $product['quantity']; ?></td>
                </tr>
              <?php endif; ?>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>
