<?php
  use MongoDB\Client;
  require 'vendor/autoload.php';
  require_once('includes/load.php');
  //require_once('includes/sql.php');
  if (property_exists($session, 'message') && str_contains($session->message, 'admin')) {
    echo '<script>console.log('.json_encode($session).')</script>';
    //redirect('index.php', false);
  }
  $page_title = 'Home Page';

  
  $uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
$client = new Client($uri);
$database = $client->selectDatabase('inventory_system');
$product=$database->selectCollection('product');
$categories=$database->selectCollection('categories');
  $all_products = $product->find();
  $all_categories =  $categories->find();

?>
<?php include_once('layouts/header.php'); ?>
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
              <tr>
                <td><?php echo remove_junk($product['name']); ?></td>
                <td><?php echo remove_junk($product['categories']); ?></td> 
                <td><?php echo number_format($product['buy_price'], 2); ?> $</td>
                <td><?php echo $product['quantity']; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>
