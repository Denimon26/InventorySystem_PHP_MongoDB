<?php
 /* $page_title = 'Add Product';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
  $all_categories = find_all('categories');
  $all_photo = find_all('media');
?>
<?php

if (isset($_POST['add_product'])) {
  $req_fields = array('product-title','product-categorie','product-quantity','buying-price','critical-level');
  validate_fields($req_fields);

  if (empty($errors)) {
    $p_name = remove_junk($db->escape($_POST['product-title']));
    $p_cat = remove_junk($db->escape($_POST['product-categorie']));
    $p_qty = remove_junk($db->escape($_POST['product-quantity']));
    $p_buy = remove_junk($db->escape($_POST['buying-price']));
    $p_critical = remove_junk($db->escape($_POST['critical-level'])); // Critical level input

    // Calculate EOQ
    $demand_rate = (int)$p_qty;
    $ordering_cost = (float)$p_buy;
    $holding_cost_per_unit = 2000; // Placeholder for holding cost
    $eoq = calculate_eoq($demand_rate, $ordering_cost, $holding_cost_per_unit);
    error_log("EOQ Calculated: " . $eoq);

    // Insert product into the database
    if (is_null($_POST['product-photo']) || $_POST['product-photo'] === "") {
      $media_id = '0';
    } else {
      $media_id = remove_junk($db->escape($_POST['product-photo']));
    }
    $date = make_date();
    $query = "INSERT INTO products (";
    $query .= " name, quantity, buy_price, eoq, categorie_id, media_id, date, critical_level";
    $query .= ") VALUES (";
    $query .= " '{$p_name}', '{$p_qty}', '{$p_buy}', '{$eoq}', '{$p_cat}', '{$media_id}', '{$date}', '{$p_critical}'";
    $query .= ")";
    $query .= " ON DUPLICATE KEY UPDATE name='{$p_name}'";

    if ($db->query($query)) {
      $session->msg('s', "Product added");

      // If the quantity is below the critical level, we pass the data to JavaScript
      if ($p_qty <= $p_critical) {
        echo "<script>
        alert('Warning: The quantity of $p_name is below the critical level! Current Quantity: $p_qty');
      </script>";
      }
    } else {
      $session->msg('d', 'Sorry failed to add product!');
      redirect('add_product.php', false);
    }
  } else {
    $session->msg("d", $errors);
    redirect('add_product.php', false);
  }
}
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
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
                    <?php  foreach ($all_categories as $cat): ?>
                      <option value="<?php echo (int)$cat['id'] ?>">
                        <?php echo $cat['name'] ?></option>
                    <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <select class="form-control" name="product-photo">
                      <option value="">Select Product Photo</option>
                    <?php  foreach ($all_photo as $photo): ?>
                      <option value="<?php echo (int)$photo['id'] ?>">
                        <?php echo $photo['file_name'] ?></option>
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
                     <input type="number" class="form-control" name="buying-price" placeholder="Buying Price">
                     <span class="input-group-addon">.00</span>
                  </div>
                 </div>
               </div>
              </div>
              <button type="submit" name="add_product" class="btn btn-danger">Add product</button>
          </form>
         </div>
        </div>
      </div>
    </div>
  </div>

<?php include_once('layouts/footer.php'); ?>*/

require '../vendor/autoload.php';

// Set up MongoDB Client
$client = new MongoDB\Client('mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0');
$db = $client->inventory_system;
$collection = $db->products; 

// Check if the form is submitted
if (isset($_POST['add_product'])) {
    $req_fields = array('product-title', 'product-categorie', 'product-quantity', 'buying-price', 'critical-level');
    
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
        $p_critical = (int)$_POST['critical-level']; // Critical level input

        // EOQ Calculation (example values for costs)
        $demand_rate = $p_qty;
        $ordering_cost = $p_buy;
        $holding_cost_per_unit = 2000; // Placeholder for holding cost
        $eoq = sqrt((2 * $demand_rate * $ordering_cost) / $holding_cost_per_unit); // EOQ formula

        // Prepare document to insert into MongoDB
        $product = [
            'name' => $p_name,
            'quantity' => $p_qty,
            'buy_price' => $p_buy,
            'eoq' => $eoq,
            'categorie_id' => $p_cat,
            'date' => new MongoDB\BSON\UTCDateTime(),
            'critical_level' => $p_critical,
        ];

        // Insert product into MongoDB collection
        $result = $collection->insertOne($product);

        if ($result->getInsertedCount() > 0) {
            echo "<script>alert('Product added successfully');</script>";

            // If the quantity is below the critical level, display a warning
            if ($p_qty <= $p_critical) {
                echo "<script>alert('Warning: The quantity of $p_name is below the critical level! Current Quantity: $p_qty');</script>";
            }
        } else {
            echo "<script>alert('Sorry, failed to add product!');</script>";
        }
    } else {
        echo "<script>alert('".implode(", ", $errors)."');</script>";
    }
}
?>

<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
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
                     <input type="number" class="form-control" name="buying-price" placeholder="Buying Price">
                     <span class="input-group-addon">.00</span>
                  </div>
                 </div>
               </div>
              </div>
              <div class="form-group">
                 <div class="col-md-4">
                    <div class="input-group">
                      <input type="number" class="form-control" name="critical-level" placeholder="Critical Level">
                    </div>
                 </div>
              </div>
              <button type="submit" name="add_product" class="btn btn-danger">Add product</button>
          </form>
         </div>
        </div>
      </div>
    </div>
  </div>

<?php include_once('layouts/footer.php'); ?>
?>

