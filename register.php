<?php
ob_start();
require_once('includes/load.php');
require 'vendor/autoload.php';

use MongoDB\Client;

if ($session->isUserLoggedIn(true)) {
  redirect('home.php', false);
}
?>
<link rel="stylesheet" href="libs/css/main.css" />
<?php echo display_msg($msg); ?>

<div class="register-page">
  <div class="panel-form">
    <div class="panel-heading-form">
      <strong>
        <span class="glyphicon glyphicon-user"></span>
        <span>Register</span>
      </strong>
    </div>
    <div class="panel-body">
      <div class="col-md-6">
        <form method="post" action="register_v2.php" enctype="multipart/form-data">
          <div class="form-group">
            <label for="name" class="control-label">Name</label>
            <input type="text" class="form-control" name="name" placeholder="Full Name" required>
          </div>
          <div class="form-group">
            <label for="username" class="control-label">Username</label>
            <input type="text" class="form-control" name="username" placeholder="Username" required>
          </div>
          <div class="form-group">
            <label for="number" class="control-label">Contact Number</label>
            <input type="text" class="form-control" name="number" placeholder="Contact Number" required>
          </div>
          <div class="form-group">
            <label for="email" class="control-label">Email</label>
            <input type="text" class="form-control" name="email" placeholder="Email" required>
          </div>
          
          <div class="form-group">
            <label for="password" class="control-label">Password</label>
            <input type="password" class="form-control" name="password" placeholder="Password" required>
          </div>
          <div class="form-group">
            <label for="confirm_password" class="control-label">Confirm Password</label>
            <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required>
          </div>
          <div class="form-group">
            <label for="image" class="control-label">Profile Image</label>
            <input type="file" class="form-control" name="image" required>
          </div>
          <div class="form-group clearfix">
            <button type="submit" class="btn btn-success">Register</button>
          </div>
        </form>
        <p>Already have an account? <a href="index.php">Login here</a></p>
      </div>
    </div>
  </div>
</div>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $username = $_POST['username'];
  $password = $_POST['password'];
  $number = $_POST['number'];
  $email = $_POST['email'];
  $confirm_password = $_POST['confirm_password'];
  $image = $_FILES['image'];

  if ($password !== $confirm_password) {
    $session->msg('d', 'Passwords do not match.');
    redirect('register.php', false);
  }

  $hashed_password = sha1($password);

  try {
    $uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
    $client = new Client($uri);
    $database = $client->selectDatabase('inventory_system');
    $users = $database->selectCollection('users');
  } catch (Exception $e) {
    $session->msg('d', 'Failed to connect to the database.');
    redirect('register.php', false);
  }

  $image_name = $image['name'];
  $image_tmp = $image['tmp_name'];
  $image_folder = 'uploads/users/' . basename($image_name);

  if (move_uploaded_file($image_tmp, $image_folder)) {
    $user_data = [
      'name' => $name,
      'username' => $username,
      'password' => $hashed_password,
      'number' => $number,
      'email' => $email,
      'image' => $image_name,
      'user_level' => '2',
      'status' => 1,
      'group_name' => '',
      'group_level' => '',
      'group_status' => ''
    ];

    $insertResult = $users->insertOne($user_data);

    if ($insertResult->getInsertedCount() === 1) {
      $session->msg('s', 'Account created successfully.');
      redirect('login.php', false);
    } else {
      $session->msg('d', 'Registration failed.');
      redirect('register.php', false);
    }
  } else {
    $session->msg('d', 'Failed to upload image.');
    redirect('register.php', false);
  }
}
?>
