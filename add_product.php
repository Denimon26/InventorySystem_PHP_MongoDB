<?php
require 'vendor/autoload.php';

// Set up MongoDB Client
$client = new MongoDB\Client('mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0');
$db = $client->inventory_system;
$productCollection = $db->product;
$categoriesCollection = $db->categories;  // Categories collection

// Fetch all categories for the dropdown
$all_categories = $categoriesCollection->find();

if (isset($_POST['add_product'])) {
    $req_fields = array('product-title', 'product-categorie', 'product-quantity', 'buying-price');
    
    // Validate fields (simplified version for MongoDB use)
    foreach ($req_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = $field . " is required.";
        }
    }

    if (empty($errors)) {
        $p_name = $_POST['product-title'];
        $p_cat = $_POST['product-categorie'];
        $p_qty = (int)$_POST['product-quantity'];
        $p_buy = (float)$_POST['buying-price'];

        // EOQ Calculation (example values for costs)
        $demand_rate = 1000;
        $ordering_cost = $p_buy;
        $holding_cost_per_unit = $p_qty; // Placeholder for holding cost
        $eoq = sqrt((2 * $demand_rate * $ordering_cost) / $holding_cost_per_unit); // EOQ formula

        // Prepare document to insert into MongoDB
        $product = [
            'name' => $p_name,
            'quantity' => $p_qty,
            'buy_price' => $p_buy,
            'eoq' => $eoq,
            'categories' => $p_cat,
            'media_id' => '0',
            'date' => new MongoDB\BSON\UTCDateTime(),
        ];

        // Insert product into MongoDB collection
        $result = $productCollection->insertOne($product);

        if ($result->getInsertedCount() > 0) {
            echo "<script>alert('Product added successfully');</script>";
        } else {
            echo "<script>alert('Sorry, failed to add product!');</script>";
        }
    } else {
        echo "<script>alert('".implode(", ", $errors)."');</script>";
    }
}
?>

<?php include_once('layouts/admin_menu.php'); ?>
<link rel="stylesheet" href="libs/css/main.css" />
<div class="row">
  <div class="col-md-12"></div>
</div>

<div class="row">
  <div class="col-md-8">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Add New Product</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="col-md-12">
          <form method="post" action="add_product.php" class="clearfix">
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="glyphicon glyphicon-th-large"></i>
                </span>
                <input type="text" class="form-control" name="product-title" placeholder="Product Title">
              </div>
            </div>
            <div class="form-group">
              <div class="row">
                <div class="col-md-6">
                  <select class="form-control" name="product-categorie">
                    <option value="">Select Product Category</option>
                    <?php foreach ($all_categories as $category): ?>
                      <option value="<?php echo $category['name']; ?>"><?php echo $category['name']; ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-md-4">
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="glyphicon glyphicon-shopping-cart"></i>
                    </span>
                    <input type="number" class="form-control" name="product-quantity" placeholder="Product Quantity">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="glyphicon glyphicon-ruble"></i>
                    </span>
                    <input type="number" class="form-control" name="buying-price" placeholder="Price">
                    <span class="input-group-addon"></span>
                  </div>
                </div>
              </div>
            </div>
            <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
