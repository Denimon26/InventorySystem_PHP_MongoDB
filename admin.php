  <?php
    $page_title = 'Admin Home Page';
    require_once('includes/load.php');
    // Checkin What level user has permission to view this page
    page_require_level(2);
  ?>
  <?php
  $c_categorie     = count_by_id('categories');
  $c_product       = count_by_id('products');
  $c_user          = count_by_id('users');
  $recent_products = find_recent_product_added('5');
  $low_limit = 99;   
  $products_by_quantity = find_products_by_quantity($low_limit);

  $quantities = array_column($products_by_quantity, 'quantity');
  sort($quantities);
  $arrlength = count($quantities);
  $fast_moving_products = find_products_by_type('fast-moving');
  $slow_moving_products = find_products_by_type('slow-moving');

  $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
  ?>
  <?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<div class="row">

<a href="product.php" style="color:black;">
    <div class="col-md-3">
       <div class="panel panel-box clearfix">
         <div class="panel-icon pull-left bg-blue2">
          <i class="glyphicon glyphicon-shopping-cart"></i>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top"> <?php  echo $c_product['total']; ?> </h2>
          <p class="text-muted">Products</p>
        </div>
       </div>
    </div>
	</a>

  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th-list"></span>
          <span>Low Quantity Products</span>
        </strong>
      </div>
      <div class="panel-body">
        <!-- Dropdown for filtering -->
        <form method="get" action="">
          <label for="filter">Filter by:</label>
          <select name="filter" id="filter" onchange="this.form.submit()">
            <option value="all" <?php if($filter == 'all') echo 'selected'; ?>>All</option>
            <option value="critical" <?php if($filter == 'critical') echo 'selected'; ?>>Critical</option>
            <option value="low" <?php if($filter == 'low') echo 'selected'; ?>>Low</option>
          </select>
        </form>
        
        <ul class="list-group">
          <?php
          // Sort the products based on quantity
          $quantities = array_column($products_by_quantity, 'quantity');
          array_multisort($quantities, SORT_ASC, $products_by_quantity);

          // Loop through the sorted products and filter based on the selection
          foreach ($products_by_quantity as $product):
            $quantity = $product['quantity'];
            $is_critical = ($quantity < 20);
            $is_low = ($quantity < $low_limit && $quantity >= 20);

            if ($filter == 'critical' && !$is_critical) continue;
            if ($filter == 'low' && !$is_low) continue;
          ?>
            <li class="list-group-item">
              <span class="badge"><?php echo $quantity; // Display full quantity ?></span>
              <?php echo remove_junk($product['name']); // Display product name ?>
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

    <!-- Fast-Moving Products Panel -->
    <div class="col-md-4">
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

  <!-- Slow-Moving Products Panel -->
  <div class="col-md-4">
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

<?php include_once('layouts/footer.php'); ?>
