<?php
$page_title = 'All Product';
require_once('includes/load.php');
require 'vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

// Checkin What level user has permission to view this page
page_require_level(2);

function page_require_level($required_level) {
    $uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
    $client = new Client($uri);
    $database = $client->selectDatabase('inventory_system');
    $admins = $database->selectCollection('admin');
    $admin = $admins->findOne(['_id' => $_SESSION['user_id']]);

    if (!isset($admin)) {
        redirect('index.php', false);
    }
    if ($admin['user_level'] <= (int)$required_level) {
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
              <tr>
                <td class="text-center"><?php echo count_id(); ?></td>
                <td>
                  <div class="text-center">
                  <?php if (isset($product['image']) && !empty($product['image'])): ?>
  <img class="img-circle" src="<?php echo $product['image']; ?>" alt="Product Image" style="width: 100px; height: 100px;">
<?php else: ?>
  <img class="img-circle" src="uploads/products/no_image.png" alt="Default Image" style="width: 100px; height: 100px;">
<?php endif; ?>

                  </div>
                </td>
                <td><?php echo remove_junk($product['name']); ?></td>
                <td class="text-center"><?php echo remove_junk($product['categories']); ?></td>
                <td class="text-center"><?php echo remove_junk($product['quantity']); ?></td>
                <td class="text-center">₱ <?php echo remove_junk($product['buy_price']); ?></td>
                <td class="text-center"><?php echo remove_junk($product['eoq']); ?></td>
                <td class="text-center"><?php echo read_date($product['date']); ?></td>
                <td class="text-center">
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
<?php //include_once('layouts/footer.php'); ?>
