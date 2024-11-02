<?php
$page_title = 'Edit Group';
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


$uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
$client = new Client($uri);
$database = $client->selectDatabase('inventory_system');
$groups = $database->selectCollection('group');
$group_id = $_GET['id'];
$e_group = $groups->findOne(['group_name' => $group_id]);

if (!$e_group) {
  redirect('group.php');
}

if (isset($_POST['update'])) {

  $req_fields = array('group-name', 'group-level');
  validate_fields($req_fields);
  if (empty($errors)) {
    $name = ($_GET['id']);
    $level = remove_junk($_POST['group-level']);
    $status = remove_junk($_POST['status']);

    $data = [
      'group_name' => $name,
      'group_level' => $level,
      'group_status' => $status

    ];
    echo "<script>console.log('".$name."');</script>";
    $result = $groups->updateOne(
      ['group_name' => $name],
      ['$set' => $data]
    );
    echo "<script>console.log('".json_encode($result)."');</script>";

    if ($result->getModifiedCount() > 0) {
      //sucess
      $session->msg('s', "Group has been updated! ");
      echo "<script>console.log('done');</script>";
      redirect('group.php?id=' . (int) $e_group['id'], false);
    } else {
      //failed
      $session->msg('d', ' Sorry failed to updated Group!');
      echo "<script>console.log('f');</script>";

      redirect('edit_group.php?id=' . (int) $e_group['id'], false);
      
    }
  } else {
    $session->msg("d", $errors);
    redirect('edit_group.php?id=' . (int) $e_group['id'], false);
  }
}
?>
<?php //include_once('layouts/header.php'); ?>
<div class="login-page">
  <div class="text-center">
    <h3>Edit Group</h3>
  </div>
  <?php echo display_msg($msg); ?>
  <form method="post" action="edit_group.php?id=<?php echo $_GET['id'] ?>">
    <div class="form-group">
      <label for="name" class="control-label">Group Name</label>
      <input type="name" class="form-control" name="group-name"
        value="<?php echo remove_junk(ucwords($e_group['group_name'])); ?>">
    </div>
    <div class="form-group">
      <label for="level" class="control-label">Group Level</label>
      <input type="number" class="form-control" name="group-level" value="<?php echo (int) $e_group['group_level']; ?>">
    </div>
    <div class="form-group">
      <label for="status">Status</label>
      <select class="form-control" name="status">
        <option <?php if ($e_group['group_status'] === '1')
          echo 'selected="selected"'; ?> value="1"> Active </option>
        <option <?php if ($e_group['group_status'] === '0')
          echo 'selected="selected"'; ?> value="0">Deactive</option>
      </select>
    </div>
    <div class="form-group clearfix">
      <button type="submit" name="update" class="btn btn-info">Update</button>
    </div>
  </form>
</div>

<?php //include_once('layouts/footer.php'); ?>