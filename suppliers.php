<?php
$page_title = 'All Suppliers';
require_once('includes/load.php');
require 'vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

$client = new MongoDB\Client('mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0');
$db = $client->inventory_system;
$collection = $db->suppliers;
?>

<?php
page_require_level(1);

$all_suppliers = $collection->find();

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
  if ($admin['user_level'] <= (int)$required_level) {
    return true;
  } else {
    redirect('home.php', false);
  }
}
?>

<?php include_once('layouts/header.php'); ?>
<?php include_once('layouts/admin_menu.php'); ?>
<link rel="stylesheet" href="libs/css/main.css" />
<link rel="stylesheet" href="./supplier.css">

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Suppliers</span>
        </strong>
        <a href="add_supplier.php" class="btn btn-primary">Add New Supplier</a>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Name</th>
              <th>Products</th>
              <th>Email</th>
              <th>Contact Number</th>
              <th class="text-center" style="width: 150px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($all_suppliers as $supplier): ?>
              <tr>
                <td class="text-center"><?php echo count_id(); ?></td>
                <td><?php echo remove_junk(ucwords($supplier['name'])); ?></td>
                <td><?php echo remove_junk(ucwords($supplier['products'])); ?></td>
                <td><?php echo remove_junk($supplier['email']); ?></td>
                <td><?php echo remove_junk($supplier['contact_number']); ?></td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="contact_supplier.php?id=<?php echo $supplier['_id']; ?>" class="btn btn-xs btn-success" data-toggle="tooltip" title="Contact">
                      <i class="glyphicon glyphicon-envelope"></i>
                    </a>
                    <a href="edit_supplier.php?id=<?php echo $supplier['_id']; ?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                      <i class="glyphicon glyphicon-pencil"></i>
                    </a>
                    <a href="delete_supplier.php?id=<?php echo $supplier['_id']; ?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
                      <i class="glyphicon glyphicon-remove"></i>
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

<?php include_once('layouts/footer.php'); ?>