<?php
$page_title = 'Add Supplier';
require_once('includes/load.php');
require 'vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

page_require_level(1);

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
$uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
$client = new Client($uri);
$database = $client->selectDatabase('inventory_system');
$suppliers = $database->selectCollection('suppliers');

if (isset($_POST['add_supplier'])) {
  $req_fields = array('name', 'products', 'email', 'contact_number');
  validate_fields($req_fields);

  if (empty($errors)) {
    $name = remove_junk($_POST['name']);
    $products = remove_junk($_POST['products']);
    $email = remove_junk($_POST['email']);
    $contact_number = remove_junk($_POST['contact_number']);

    $data = [
      'name' => $name,
      'products' => $products,
      'email' => $email,
      'contact_number' => $contact_number,
    ];

    $result = $suppliers->insertOne($data);
    echo '<script>alert("Supplier added successfully!");</script>';
  } else {
    $session->msg("d", $errors);
    redirect('add_supplier.php', false);
  }
}
?>

<?php include_once('layouts/header.php'); ?>
<?php include_once('layouts/admin_menu.php'); ?>
<link rel="stylesheet" href="libs/css/main.css" />

<div class="row">
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong><span class="glyphicon glyphicon-th"></span> Add New Supplier</strong>
      </div>
      <div class="panel-body">
        <form method="post" action="add_supplier.php">
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" name="name" placeholder="Supplier Name">
          </div>
          <div class="form-group">
            <label for="products">Products</label>
            <input type="text" class="form-control" name="products" placeholder="Products Supplied">
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" name="email" placeholder="Email">
          </div>
          <div class="form-group">
            <label for="contact_number">Contact Number</label>
            <input type="text" class="form-control" name="contact_number" placeholder="Contact Number">
          </div>
          <div class="form-group clearfix">
            <button type="submit" name="add_supplier" class="btn btn-primary">Add Supplier</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>