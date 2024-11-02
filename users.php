<?php
  $page_title = 'All User';
  require_once('includes/load.php');
  require 'vendor/autoload.php';
  

  use MongoDB\Client;
  use MongoDB\BSON\ObjectId;
// Set up MongoDB Client
$client = new MongoDB\Client('mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0');
$db = $client->inventory_system;
$collection = $db->users; 
?>
<?php
// Checkin What level user has permission to view this page
 page_require_level(1);
//pull out all user form database
 $all_users = $collection->find();


 function page_require_level($required_level) {
  $uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
  $client = new Client($uri);
  $database = $client->selectDatabase('inventory_system');
  $admins = $database->selectCollection('admin');
  $admin=  $admins->findOne(['_id' => $_SESSION['user_id']]);

  if (!isset($admin)) {

      redirect('index.php', false);
  }
  if ($admin['user_level'] <= (int)$required_level) {
      return true;
  } else {
      // If the user does not have permission, redirect to the home page
      redirect('home.php', false);
  }
}
?>
<?php include_once('layouts/header.php'); ?>
<link rel="stylesheet" href="libs/css/main.css" />
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
          <span>Users</span>
       </strong>
         <a href="add_user.php" class="btn btn-add-user">Add New User</a>
      </div>
     <div class="panel-body">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th class="text-center" style="width: 50px;">#</th>
            <th>Name </th>
            <th>Username</th>
            <th class="text-center" style="width: 15%;">User Role</th>
            <th class="text-center" style="width: 10%;">Status</th>
            <th style="width: 20%;">Last Login</th>
            <th class="text-center" style="width: 100px;">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($all_users as $a_user): ?>
          <tr>
           <td class="text-center"><?php echo count_id();?></td>
           <td><?php echo remove_junk(ucwords($a_user['name']))?></td>
           <td><?php echo remove_junk(ucwords($a_user['username']))?></td>
           <td class="text-center"><?php echo remove_junk(ucwords($a_user['group_name']))?></td>
           <td class="text-center">
           <?php if($a_user['group_status'] === '1'): ?>
            <span class="label label-success"><?php echo "Active"; ?></span>
          <?php else: ?>
            <span class="label label-danger"><?php echo "Deactive"; ?></span>
          <?php endif;?>
           </td>
           <td><?php echo isset($a_user['last_login'])? read_date($a_user['last_login']):""?></td>
           <td class="text-center">
  <div class="btn-group">
     <a href="edit_user.php?id=<?php echo $a_user['_id']; ?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">Edit
       <i class="glyphicon glyphicon-pencil"></i>
     </a>
     <a href="delete_user.php?id=<?php echo $a_user['_id']; ?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">Remove
       <i class="glyphicon glyphicon-remove"></i>
     </a>
  </div>
</td>

          </tr>
        <?php endforeach;?>
       </tbody>
     </table>
     </div>
    </div>
  </div>
</div>
  <?php //include_once('layouts/footer.php'); ?>
