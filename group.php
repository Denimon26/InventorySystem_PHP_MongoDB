<?php
$page_title = 'All Group';
require_once('includes/load.php');
require 'vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;
// Checkin What level user has permission to view this page
page_require_level(1);

$uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
$client = new Client($uri);
$database = $client->selectDatabase('inventory_system');
$groups = $database->selectCollection('group');
$all_groups = $groups->find();

function page_require_level($required_level)
{
  $uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
  $client = new Client($uri);
  $database = $client->selectDatabase('inventory_system');
  $admins = $database->selectCollection('admin');
  $admin =  $admins->findOne(['_id' => $_SESSION['user_id']]);

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
</div>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Groups</span>
        </strong>
        <a href="add_group.php" class="btn btn-primary">Add New Group</a>
      </div>
      <div class="panel-body">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Group Name</th>
              <th class="text-center" style="width: 20%;">Group Level</th>
              <th class="text-center" style="width: 15%;">Status</th>
              <th class="text-center" style="width: 100px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($all_groups as $a_group): ?>
              <tr>
                <td class="text-center"><?php echo count_id(); ?></td>
                <td><?php echo remove_junk(ucwords($a_group['group_name'])) ?></td>
                <td class="text-center">
                  <?php echo remove_junk(ucwords($a_group['group_level'])) ?>
                </td>
                <td class="text-center">
                  <?php if ($a_group['group_status'] === '1'): ?>
                    <span class="label label-success"><?php echo "Active"; ?></span>
                  <?php else: ?>
                    <span class="label label-danger"><?php echo "Deactive"; ?></span>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="edit_group.php?id=<?php echo $a_group['group_name']; ?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">Edit
                      <i class="glyphicon glyphicon-pencil"></i>
                    </a>
                    <a href="delete_group.php?id=<?php echo $a_group['group_name']; ?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">Remove
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
<?php //include_once('layouts/footer.php'); 
?>