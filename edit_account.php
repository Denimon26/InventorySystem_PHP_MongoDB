<?php
  $page_title = 'Edit Account';
  require_once('includes/load.php');
  require 'vendor/autoload.php';
  use MongoDB\Client;

  // MongoDB connection
  $uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
  $client = new Client($uri);
  $database = $client->selectDatabase('inventory_system');
  $users = $database->selectCollection('users');
  $user_id = $_SESSION['user_id'];
  $user = $users->findOne(['_id' => $user_id]);

  // Check if user has required level access
  function page_require_level($required_level) {
    global $database;
    $admins = $database->selectCollection('admin');
    $admin = $admins->findOne(['_id' => $_SESSION['user_id']]);
  
    if (!$admin || $admin['user_level'] > (int)$required_level) {
      redirect('home.php', false);
    }
  }
  page_require_level(3);
?>

<?php
// Update user photo
if(isset($_POST['submit'])) {
  $photo = new Media();
  $photo->upload($_FILES['file_upload']);
  if ($photo->process_user($user_id)) {
    $session->msg('s', 'Photo has been uploaded.');
  } else {
    $session->msg('d', join($photo->errors));
  }
  redirect('edit_account.php');
}
?>

<?php
// Update user information
if(isset($_POST['update'])){
  $req_fields = array('name', 'username');
  validate_fields($req_fields);
  if(empty($errors)) {
    $name = remove_junk($_POST['name']);
    $username = remove_junk($_POST['username']);
    $data = [
      'name' => $name,
      'username' => $username
    ];
    $result = $users->updateOne(
      ['_id' => $user_id],
      ['$set' => $data]
    );
    if($result->getModifiedCount() > 0) {
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
          <img class="img-circle img-size-2" src="uploads/users/<?php echo $user['image'];?>" alt="" style="width: 100px; height: 100px;">

          </div>
          <div class="col-md-8">
            <form class="form" action="edit_account.php" method="POST" enctype="multipart/form-data">
              <div class="form-group">
                <input type="file" name="file_upload" class="btn btn-default btn-file"/>
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
            <input type="text" class="form-control" name="name" value="<?php echo remove_junk(ucwords($user['name'])); ?>">
          </div>
          <div class="form-group">
            <label for="username" class="control-label">Username</label>
            <input type="text" class="form-control" name="username" value="<?php echo remove_junk(ucwords($user['username'])); ?>">
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
<?php include_once('layouts/footer.php'); ?>
