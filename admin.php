  <?php
  
    $page_title = 'Admin Home Page';
    require_once('includes/load.php');
    require 'vendor/autoload.php';

    use MongoDB\Client;
use MongoDB\BSON\ObjectId;
    // Checkin What level user has permission to view this page
    page_require_level(1);
   
    function page_require_level($required_level) {
      $uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
      $client = new Client($uri);
      $database = $client->selectDatabase('inventory_system');
      $admins = $database->selectCollection('admin');
      $admin=  $admins->findOne(['_id' => $_SESSION['user_id']]);

      if (!isset($admin)) {

          redirect('index.php', false);
      }
      if ($admin['user_level'] <= (int)$required_level) {
          return true;
      } else {
          // If the user does not have permission, redirect to the home page
          redirect('home.php', false);
      }
  }

 
  ?>
  <?php
  $uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
  $client = new Client($uri);
  $database = $client->selectDatabase('inventory_system');
  $categories = $database->selectCollection('categories');
  $product = $database->selectCollection('product');
  $users = $database->selectCollection('users');
  
  $c_categorie = $categories->countDocuments();
  $c_product = $product->countDocuments();
  $c_user = $users->countDocuments();
  
  $recent_products_cursor = $product->find([], ['sort' => ['date' => -1], 'limit' => 5]);
  $recent_products = iterator_to_array($recent_products_cursor);
  
  echo '<script>console.log(' . json_encode($recent_products) . ');</script>';
  
  $low_limit = 99;   

  $products_by_quantity = $product->find(['quantity' => ['$lt' => 5]], ['sort' => ['quantity' => 1]])->toArray();

  $quantities = array_column($products_by_quantity, 'quantity');
  sort($quantities);
  $arrlength = count($quantities);
  $fast_moving_products = $product->find(['fast-moving']);
  $slow_moving_products =$product->find(['slow-moving']);

  $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
  ?>
  <?php include_once('layouts/header.php'); ?>
  <link rel="stylesheet" href="libs/css/main.css" />

<div class="row">
  <div class="col-half">
    <?php 
      echo '<script>console.log(' . json_encode($msg) . ');</script>';
    ?>
  </div>
</div>

<div class="row">

<a href="product.php" class="product-link">
    <div class="col-quarter">
       <div class="panel-product panel-box clearfix">
         <div class="panel-icon bg-blue2">
          <i class="glyphicon glyphicon-shopping-cart"></i>
        </div>
        <div class="panel-value">
          <h2 class="panel-count"> <?php  echo $c_product; ?> </h2>
          <p class="panel-text">Products</p>
        </div>
       </div>
    </div>
</a>

  <div class="col-third">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th-list"></span>
          <span>Low Quantity Products</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="get" action="">
          <label for="filter">Filter by:</label>
          <select name="filter" id="filter" class="form-control" onchange="this.form.submit()">
            <option value="all" <?php if($filter == 'all') echo 'selected'; ?>>All</option>
            <option value="critical" <?php if($filter == 'critical') echo 'selected'; ?>>Critical</option>
            <option value="low" <?php if($filter == 'low') echo 'selected'; ?>>Low</option>
          </select>
        </form>
        
        <ul class="list-group">
          <?php
          $quantities = array_column($products_by_quantity, 'quantity');
          array_multisort($quantities, SORT_ASC, $products_by_quantity);

          foreach ($products_by_quantity as $product):
            $quantity = $product['quantity'];
            $is_critical = ($quantity < 20);
            $is_low = ($quantity < $low_limit && $quantity >= 20);

            if ($filter == 'critical' && !$is_critical) continue;
            if ($filter == 'low' && !$is_low) continue;
          ?>
            <li class="list-group-item">
              <span class="badge"><?php echo $quantity; ?></span>
              <?php echo remove_junk($product['name']); ?>
              <?php if ($is_critical): ?>
                <span class="label label-danger pull-right">Critical</span>
              <?php elseif ($is_low): ?>
                <span class="label label-warning pull-right">Low</span>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>

  <div class="col-third">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th-list"></span>
          <span>Fast-Moving Products</span>
        </strong>
      </div>
      <div class="panel-body">
        <ul class="list-group">
          <?php foreach ($fast_moving_products as $product): ?>
            <li class="list-group-item">
              <span class="badge"><?php echo $product['quantity']; ?></span>
              <?php echo remove_junk($product['name']); ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>

  <div class="col-third">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th-list"></span>
          <span>Slow-Moving Products</span>
        </strong>
      </div>
      <div class="panel-body">
        <ul class="list-group">
          <?php foreach ($slow_moving_products as $product): ?>
            <li class="list-group-item">
              <span class="badge"><?php echo $product['quantity']; ?></span>
              <?php echo remove_junk($product['name']); ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</div>

<?php //include_once('layouts/footer.php'); ?>
