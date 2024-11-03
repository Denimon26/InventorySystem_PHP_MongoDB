<?php
  $page_title = 'Edit User';
  require_once('includes/load.php');
  require 'vendor/autoload.php';
  use MongoDB\Client;
  
  // Checkin What level user has permission to view this page
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
      // If the user does not have permission, redirect to the home page
      redirect('home.php', false);
    }
  }

?>
<?php
//$e_user = find_by_id('users',(int)$_GET['id']);
//$groups  = find_all('user_groups');
//if(!$e_user){
//  $session->msg("d","Missing user id.");
//  redirect('users.php');
//}
?>

<?php

$uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
$client = new Client($uri);
$database = $client->selectDatabase('inventory_system');
$users = $database->selectCollection('users');
$user = $_GET['id'];
$e_user=$users->findOne(['name'=>$_GET['id']]);
echo "<script>console.log('".$user."');</script>";

//Update User basic info
if (isset($_POST['update'])) {


  $req_fields = array('name', 'username', 'level');
  validate_fields($req_fields);
  if (empty($errors)) {
    $name = ($_GET['id']);
    $level = remove_junk($_POST['level']);
    $username = remove_junk($_POST['username']);

    $data = [
      'name' => $_POST['name'],
      'username' => $username,
      'user_level' => $level

    ];
    $result = $users->updateOne(
      ['name' => $name],
      ['$set' => $data]
    );

    if ($result->getModifiedCount() > 0) {
      //sucess
      $session->msg('s', "Group has been updated! ");
      redirect('users.php' , false);
          } else {
      //failed
      $session->msg('d', ' Sorry failed to updated Group!');

      redirect('users.php' , false);

          }
    } else {
      $session->msg("d", $errors);
      redirect('users.php' , false);
    }
  }

// Update user password
if (isset($_POST['update-pass'])) {
  $req_fields = array('password');
  validate_fields($req_fields);
if (empty($errors)) {
  $password = remove_junk(($_POST['password']));
  

  if (empty($errors)) {
    $name = ($_GET['id']);
    $pass = remove_junk($_POST['password']);  
    $data = [
      'password' => $pass,

    ];
    $result = $users->updateOne(
      ['name' => $name],
      ['$set' => $data]
    );

    if ($result->getModifiedCount() > 0) {
      //sucess
      $session->msg('s', "Password has been updated! ");
      redirect('users.php' , false);
    } else {
      //failed
      $session->msg('d', ' Sorry failed to updated Group!');

      redirect('users.php' , false);
      
    }
  } else {
    $session->msg("d", $errors);
    redirect('users.php' , false);
  }
}
}

?>
<?php //include_once('layouts/header.php'); ?>
<link rel="stylesheet" href="libs/css/main.css" />
<div class="row">
  <div class="col-md-12"> <?php echo display_msg($msg); ?> </div>
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          Update <?php echo remove_junk(ucwords($e_user['name'])); ?> Account
        </strong>
        </div>
      <div class="panel-body">
        <form method="post" action="edit_user.php?id=<?php echo  $e_user['name']; ?>" class="clearfix">
          <div class="form-group">
            <label for="name" class="control-label">Name</label>
            <input type="name" class="form-control" name="name"
              value="<?php echo remove_junk(ucwords($e_user['name'])); ?>">
          </div>
          <div class="form-group">
            <label for="username" class="control-label">Username</label>
            <input type="text" class="form-control" name="username"
              value="<?php echo remove_junk(ucwords($e_user['username'])); ?>">
          </div>
          <div class="form-group">
            <label for="level">User Level</label>
            <select class="form-control" name="level">
            <option value=2>2</option>

            </select>
          </div>
          <div class="form-group">
            <label for="status">Status</label>
            <select class="form-control" name="status">
              <option <?php if ($e_user['status'] === '1')
                echo 'selected="selected"'; ?>value="1">Active</option>
              <option <?php if ($e_user['status'] === '0')
                echo 'selected="selected"'; ?> value="0">Deactive</option>
            </select>
          </div>
          <div class="form-group clearfix">
            <button type="submit" name="update" class="btn btn-primary">Update</button>
          </div>
        </form>
       </div>
     </div>
  </div>
  <!-- Change password form -->
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          Change <?php echo remove_junk(ucwords($e_user['name'])); ?> password
        </strong>
      </div>
      <div class="panel-body">
      <form action="edit_user.php?id=<?php echo $e_user['name']; ?>" method="post" class="clearfix">
          <div class="form-group">
                <label for="password" class="control-label">Password</label>
                <input type="password" class="form-control" name="password" placeholder="Type user new password">
          </div>
          <div class="form-group clearfix">
                  <button type="submit" name="update-pass" class="btn btn-primary pull-right">Change</button>
          </div>
        </form>
      </div>
    </div>
  </div>

 </div>
<?php //include_once('layouts/footer.php'); ?>
