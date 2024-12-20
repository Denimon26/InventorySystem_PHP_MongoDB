<?php
$page_title = 'All Product';
require_once('includes/load.php');
require 'vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

// Checkin What level user has permission to view this page
page_require_level(2);

function page_require_level($required_level)
{
  $uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
  $client = new Client($uri);
  $database = $client->selectDatabase('inventory_system');
  $admins = $database->selectCollection('admin');
  $admin = $admins->findOne(['_id' => $_SESSION['user_id']]);

  if (!isset($admin)) {
    redirect('index.php', false);
  }
  if ($admin['user_level'] <= (int) $required_level) {
    return true;
  } else {
    redirect('home.php', false);
  }
}

// Connect to MongoDB and fetch products
$uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
$client = new Client($uri);
$database = $client->selectDatabase('inventory_system');
$products2 = $database->selectCollection('product');
$products = $products2->find();
?>
<?php include_once('layouts/header.php'); ?>
<?php include_once('layouts/admin_menu.php'); ?>
<link rel="stylesheet" href="libs/css/main.css" />
<style>
  /* Responsive Design */
  @media (max-width: 768px) {
    .table {
      font-size: 12px;
    }

    .btn,
    .btn-add-user {
      font-size: 12px;
      padding: 5px 8px;
    }

    .navbar {
      flex-direction: column;
      align-items: flex-start;
    }

    .navbar h1 {
      font-size: 20px;
    }

    .nav-item a {
      right: 0px;
    }

    .nav-links {
      flex-direction: column;
      width: 100%;
    }

    .table thead {
      display: none;
    }

    .table tbody tr {
      display: flex;
      flex-direction: column;
      margin-bottom: 10px;
      border: 1px solid #ddd;
      padding: 10px;
    }

    .table tbody td {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      padding: 5px 0;
    }

    .table tbody td::before {
      content: attr(data-label);
      font-weight: bold;
      margin-bottom: 5px;
    }
  }

  @media (min-width: 769px) and (max-width: 1024px) {
    .table {
      font-size: 13px;
    }
  }

  @media (min-width: 1025px) and (max-width: 1440px) {
    .table {
      font-size: 14px;
    }
  }

  @media (min-width: 1441px) {
    .table {
      font-size: 16px;
    }
  }
</style>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="pull-right">
          <a href="add_product.php" class="btn btn-primary">Add New</a>
        </div>
      </div>
      <div class="panel-body">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Photo</th>
              <th>Product Title</th>
              <th class="text-center" style="width: 10%;">Categories</th>
              <th class="text-center" style="width: 10%;">In-Stock</th>
              <th class="text-center" style="width: 10%;">Price</th>
              <th class="text-center" style="width: 10%;">EOQ</th>
              <th class="text-center" style="width: 10%;">Product Added</th>
              <th class="text-center" style="width: 100px;">Actions</th>
            </tr>
          </thead>
          <tbody>
  <?php foreach ($products as $product): ?>
    <?php 
      $critical_amount = isset($product['critical_amount']) ? (int)$product['critical_amount'] : 0;
      $quantity = isset($product['quantity']) ? (int)$product['quantity'] : 0;

      $row_class = ($critical_amount > 0 && $quantity <= $critical_amount) ? 'critical-row' : '';
    ?>
    <tr class="<?php echo $row_class; ?>">
      <td class="text-center"style="background-color: <?php echo ($critical_amount > 0 && $quantity <= $critical_amount) ? '#ffdddd' : ''; ?>"><?php echo count_id(); ?></td>
      <td style="background-color: <?php echo ($critical_amount > 0 && $quantity <= $critical_amount) ? '#ffdddd' : ''; ?>">
        <div class="text-center">
          <?php if (isset($product['image']) && !empty($product['image'])): ?>
            <img class="img-circle" src="<?php echo $product['image']; ?>" alt="Product Image" style="width: 100px; height: 100px;">
          <?php else: ?>
            <img class="img-circle" src="uploads/products/no_image.png" alt="Default Image" style="width: 100px; height: 100px;">
          <?php endif; ?>
        </div>
      </td>
      <td style="background-color: <?php echo ($critical_amount > 0 && $quantity <= $critical_amount) ? '#ffdddd' : ''; ?>"><?php echo remove_junk($product['name']); ?></td>

      <td class="text-center" style="background-color: <?php echo ($critical_amount > 0 && $quantity <= $critical_amount) ? '#ffdddd' : ''; ?>"><?php echo remove_junk($product['categories']); ?></td>
      <td class="text-center"style="background-color: <?php echo ($critical_amount > 0 && $quantity <= $critical_amount) ? '#ffdddd' : ''; ?>"><?php echo remove_junk($product['quantity']); ?></td>
      <td class="text-center"style="background-color: <?php echo ($critical_amount > 0 && $quantity <= $critical_amount) ? '#ffdddd' : ''; ?>">₱ <?php echo remove_junk($product['buy_price']); ?></td>
      <td class="text-center" style="background-color: <?php echo ($critical_amount > 0 && $quantity <= $critical_amount) ? '#ffdddd' : ''; ?>"><?php echo (int)remove_junk($product['eoq']); ?></td>
      <td class="text-center"style="background-color: <?php echo ($critical_amount > 0 && $quantity <= $critical_amount) ? '#ffdddd' : ''; ?>"><?php echo read_date($product['date']); ?></td>
      <td class="text-center"style="background-color: <?php echo ($critical_amount > 0 && $quantity <= $critical_amount) ? '#ffdddd' : ''; ?>">
        <div class="btn-group">
          <a href="edit_product.php?id=<?php echo $product['_id']; ?>" class="btn btn-xs btn-warning" title="Edit" data-toggle="tooltip">
            <span class="glyphicon glyphicon-edit"></span>
          </a>
          <a href="delete_product.php?id=<?php echo $product['_id']; ?>" class="btn btn-xs btn-danger" title="Delete" data-toggle="tooltip">
            <span class="glyphicon glyphicon-trash"></span>
          </a>
        </div>
      </td>
    </tr>
  <?php endforeach; ?>
</tbody>


        </table>
      </div>
    </div>
  </div>
</div>
<?php //include_once('layouts/footer.php'); 
?>