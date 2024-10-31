<?php
  $page_title = 'Home Page';
  require_once('includes/load.php');
  if (!$session->isUserLoggedIn(true)) { redirect('index.php', false);}
  $all_products = find_all('products');
  $all_categories = find_all('categories');
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
                <td><?php echo remove_junk($product['categorie']); ?></td> 
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
