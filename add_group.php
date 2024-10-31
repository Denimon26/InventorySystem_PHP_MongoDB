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

    redirect('login.php', false);
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
  try {

    $result = $collection->find([
      '$and' => [
        [
          'registered_date' => [
            '$gte' => new UTCDateTime(strtotime('-1 year') * 1000)
          ]
        ],
        [
          'personal_details.date_of_birth' => ['$exists' => true],
          'personal_details.address' => ['$exists' => true],
          'personal_details.tin_no' => ['$exists' => true]
        ],
        [
          'employer_details.salary' => [
            '$gte' => 30000,
            '$lte' => 100000
          ]
        ]
      ]
    ]);
    return iterator_to_array($result);
  } catch (Exception $e) {
    return "Error in AND filter: " . $e->getMessage();
  }

  if (find_by_groupName($_POST['group-name']) === false) {
    $session->msg('d', '<b>Sorry!</b> Entered Group Name already in database!');
    redirect('add_group.php', false);
  } elseif (find_by_groupLevel($_POST['group-level']) === false) {
    $session->msg('d', '<b>Sorry!</b> Entered Group Level already in database!');
    redirect('add_group.php', false);
  }

}
?>
<?//php include_once('layouts/header.php'); ?>
<div class="login-page">
  <div class="text-center">
    <h3>Add new user Group</h3>
  </div>
  <?php echo display_msg($msg); ?>
  <form method="post" action="add_group.php" class="clearfix">
    <div class="form-group">
      <label for="name" class="control-label">Group Name</label>
      <input type="name" class="form-control" name="group-name">
    </div>
    <div class="form-group">
      <label for="level" class="control-label">Group Level</label>
      <input type="number" class="form-control" name="group-level">
    </div>
    <div class="form-group">
      <label for="status">Status</label>
      <select class="form-control" name="status">
        <option value="1">Active</option>
        <option value="0">Deactive</option>
      </select>
    </div>
    <div class="form-group clearfix">
      <button type="submit" name="add" class="btn btn-info">Update</button>
    </div>
  </form>
</div>

<?php //include_once('layouts/footer.php'); ?>