<?php
$page_title = 'Add Group';
require_once('includes/load.php');
use MongoDB\Client;
require 'vendor/autoload.php';
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
if (isset($_POST['add'])) {




  $uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
  $client = new Client($uri);
  $database = $client->selectDatabase('inventory_system');
  $groups = $database->selectCollection('group');

  $req_fields = array('group-name', 'group-level');
  validate_fields($req_fields);
  if(!isset($_POST['group-name'])||!isset($_POST['group-name'])||(!isset($_POST['group-name'])))
  {
    $session->msg('d', ' Sorry failed to create Group!');
    redirect('add_group.php', false);
  }
  else
  {
    if($_POST['group-name']==""||$_POST['group-name']==""||$_POST['group-name']=="")
    {
      $session->msg('d', ' Sorry failed to create Group!');
      redirect('add_group.php', false);
    }

  }
  if (empty($errors)) {
    $name = remove_junk($_POST['group-name']);
    $level = remove_junk($_POST['group-level']);
    $status = remove_junk($_POST['status']);

  
  } else {
    $session->msg("d", $errors);
    redirect('add_group.php', false);
  }

    $result = $groups->find([
          'group_name' =>$_POST['group-name']
    ]);
    echo '<script>console.log('.json_encode(value: $_POST['group-name']).');</script>';

    if(iterator_to_array($result))
    {
      //echo '<script>console.log("dupes");</script>';
    //  echo '<script>console.log('.json_encode(iterator_to_array($result)).');</script>';
      $session->msg('d', '<b>Sorry!</b> Entered Group Name already in database!');
      redirect('add_group.php', false);
    }
    else
    {
      $data=[
        'group_name'=>$name,
        'group_level'=>$level,
        'group_status'=>$status

      ];
      $result2 = $groups->insertOne($data);
      echo '<script>alert("Success")</script>';


    }



}
?>
<?php //include_once('layouts/header.php'); ?>
<link rel="stylesheet" href="libs/css/main.css" />
<?php echo display_msg($msg); ?>
<div class="login-page">
  <div class="panel-form">
    <div class="panel-heading-form">
      <strong>
        <span class="glyphicon glyphicon-th"></span>
        <span>Add New User Group</span>
      </strong>
    </div>
    <div class="panel-body">
      <div class="col-md-6">
        <form method="post" action="add_group.php">
          <div class="form-group">
            <label for="group-name">Group Name</label>
            <input type="text" class="form-control" name="group-name" placeholder="Group Name" required>
          </div>
          <div class="form-group">
            <label for="group-level">Group Level</label>
            <input type="number" class="form-control" name="group-level" placeholder="Group Level" required min="1">
          </div>
          <div class="form-group">
            <label for="status">Status</label>
            <select class="form-control" name="status" required>
              <option value="1">Active</option>
              <option value="0">Deactive</option>
            </select>
          </div>
          <div class="form-group clearfix">
            <button type="submit" name="add" class="btn btn-primary">Add Group</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</div>

<?php include_once('layouts/footer.php'); ?>