<?php
$page_title = 'Edit Account';
require_once('includes/load.php');
require 'vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

$client = new MongoDB\Client('mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0');
$db = $client->inventory_system;
$users = $db->users;
$admins = $db->admin;

$user_id = $_SESSION['user_id'] ?? null;

if (empty($user_id)) {
  redirect('home.php', false);
}

$user = $users->findOne(['_id' => new ObjectId($user_id)]);
if (!$user) {
  $user = $admins->findOne(['_id' => new ObjectId($user_id)]);
}

if (!$user) {
  redirect('home.php', false);
}

function page_require_level($required_level)
{
  $uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
  $client = new Client($uri);
  $database = $client->selectDatabase('inventory_system');
  $admins = $database->selectCollection('admin');

  $admin = $admins->findOne(['_id' => new ObjectId($_SESSION['user_id'])]);

  if (!isset($admin)) {
    redirect('index.php', false);
  }

  if ($admin['user_level'] <= (int) $required_level) {
    return true;
  } else {
    if ($admin['user_level'] != 1)
      redirect('home.php', false);
    else
      redirect('admin.php', false);
  }
}

//page_require_level(3);
?>


<?php
// Update user photo in Base64 format and save to MongoDB
if (isset($_POST['submit'])) {
  if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0) {
    // Set max file size (in bytes) - e.g., 1MB
    $maxFileSize = 1048576;
    $imageFileType = strtolower(pathinfo($_FILES["file_upload"]["name"], PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    // Check if file size is within the allowed limit
    if ($_FILES['file_upload']['size'] > $maxFileSize) {
      $session->msg('d', 'File size exceeds the maximum limit of 1MB.');
      redirect('edit_account.php');
      exit();
    }

    // Check if the file is an image and is of an allowed type
    $check = getimagesize($_FILES["file_upload"]["tmp_name"]);
    if ($check !== false && in_array($imageFileType, $allowedTypes)) {
      // Convert the image to Base64
      $imageData = file_get_contents($_FILES["file_upload"]["tmp_name"]);
      $base64Image = 'data:' . $check['mime'] . ';base64,' . base64_encode($imageData);

      try {
        // Update the Base64 image string in MongoDB
        $users->updateOne(
          ['_id' => $user_id],
          ['$set' => ['image' => $base64Image]]
        );
        $session->msg('s', 'Photo has been successfully uploaded.');
      } catch (Exception $e) {
        $session->msg('d', 'Error updating the image in the database: ' . $e->getMessage());
      }
    } else {
      $session->msg('d', 'File is not a valid image or is not of an allowed type.');
    }
  } else {
    $session->msg('d', 'No file selected or there was an upload error.');
  }
  redirect('edit_account.php');
}
?>

<?php
// Update user information
if (isset($_POST['update'])) {
  $req_fields = array('name', 'username');
  validate_fields($req_fields);
  if (empty($errors)) {
    $name = remove_junk($_POST['name']);
    $username = remove_junk($_POST['username']);
    $number = $_POST['number'];
    $email = $_POST['email'];
    $data = [
      'name' => $name,
      'username' => $username,
      'number' => $number,
      'email' => $email,
    ];
    $result = $users->updateOne(
      ['_id' => $user_id],
      ['$set' => $data]
    );
    if ($result->getModifiedCount() > 0) {
      $session->msg('s', "Account updated.");
    } else {
      $session->msg('d', 'Failed to update account.');
    }
    redirect('edit_account.php');
  } else {
    $session->msg("d", $errors);
    redirect('edit_account.php');
  }
}
?>

<?php include_once('layouts/header.php'); ?>
<?php include_once('layouts/admin_menu.php'); ?>
<link rel="stylesheet" href="libs/css/main.css" />
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <span class="glyphicon glyphicon-camera"></span> Change My Photo
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-4">
            <?php
            $image = $user['image'];

            if (!empty($image)) {
              echo '<img src="' . htmlspecialchars($image) . '" alt="User Image" style="width: 100px; height: 100px; border-radius: 50%;">';
            } else {
              echo '<img src="uploads/users/default.png" alt="Default Image" style="width: 100px; height: 100px; border-radius: 50%;">';
            }
            ?>
          </div>

          <div class="col-md-8">
            <form class="form" action="edit_account.php" method="POST" enctype="multipart/form-data">
              <div class="form-group">
                <input type="file" name="file_upload" class="btn btn-default btn-file" />
              </div>
              <div class="form-group">
                <button type="submit" name="submit" class="btn btn-primary">Change</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <span class="glyphicon glyphicon-edit"></span> Edit My Account
      </div>
      <div class="panel-body">
        <form method="post" action="edit_account.php" class="clearfix">
          <div class="form-group">
            <label for="name" class="control-label">Name</label>
            <input type="text" class="form-control" name="name"
              value="<?php echo remove_junk(ucwords($user['name'])); ?>">
          </div>
          <div class="form-group">
            <label for="username" class="control-label">Username</label>
            <input type="text" class="form-control" name="username"
              value="<?php echo remove_junk(ucwords($user['username'])); ?>">
          </div>
          <div class="form-group">
            <label for="number" class="control-label">Contact Number</label>
            <input type="text" class="form-control" name="number"
              value="<?php echo isset($user['number']) && !empty($user['number']) ? remove_junk(ucwords($user['number'])) : 'No Number'; ?>">
          </div>

          <div class="form-group">
            <label for="email" class="control-label">Email</label>
            <input type="text" class="form-control" name="email"
              value="<?php echo isset($user['email']) && !empty($user['email']) ? remove_junk(ucwords($user['email'])) : 'No Email'; ?>">
          </div>

          <div class="form-account clearfix">
            <a href="change_password.php" class="btn btn-primary pull-right">Change Password</a>
            <button type="submit" name="update" class="btn btn-primary">Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php //include_once('layouts/footer.php'); ?>