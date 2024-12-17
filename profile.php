<?php
  $page_title = 'My profile';
  require_once('includes/load.php');
  require 'vendor/autoload.php';

  use MongoDB\Client;
  use MongoDB\BSON\ObjectId;
  // Set up MongoDB Client
  $client = new MongoDB\Client('mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0');
  $db = $client->inventory_system;
  $collection = $db->users;
  $collection2 = $db->admin;

  //page_require_level(-1);

  // Get user ID from GET request
  $user_id = $_SESSION['user_id'] ?? null;
  if (empty($user_id)&&!isset($user_id)) {
    redirect('home.php', false);
  } else {
     //Find the user by ObjectId
    $user_p = $collection->findOne(['_id' => $user_id]);

    if(!isset($user_p))
    {
      $user_p = $collection2->findOne(['_id' => $user_id ]);

    }
  }

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
?>
<?php include_once('layouts/header.php'); ?>
<?php include_once('layouts/admin_menu.php'); ?>
<link rel="stylesheet" href="libs/css/main.css" />
<div class="row">
   <div class="col-md-4">
       <div class="panel profile">
         <div class="jumbotron text-center bg-red">
            <img class="img-circle img-size-2" src="uploads/users/<?php echo $user_p['image'] ?? 'default.png'; ?>" alt="">
           <h3><?php echo ucwords($user_p['name'] ?? 'N/A'); ?></h3>
         </div>
        <?php if($user_p['_id'] == $_SESSION['user_id']): ?>
         <ul class="nav nav-pills nav-stacked">
          <li><a href="edit_account.php"> <i class="glyphicon glyphicon-edit"></i> Edit profile</a></li>
         </ul>
       <?php endif; ?>
       </div>
   </div>
</div>
<?php //include_once('layouts/footer.php'); ?>
