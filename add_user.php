<?php
  $page_title = 'Add User';
  require_once('includes/load.php');
  require 'vendor/autoload.php';

  use MongoDB\Client;
  use MongoDB\BSON\ObjectId;
  // Checkin What level user has permission to view this page
  page_require_level(1);
  $uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
  $client = new Client($uri);
  $database = $client->selectDatabase('inventory_system');
  $group = $database->selectCollection('group');
  $users = $database->selectCollection('users');
  $groups = iterator_to_array($group->find());
  

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
<?php
  if(isset($_POST['add_user'])){

   $req_fields = array('full-name','username','password','level' );
   validate_fields($req_fields);

   if(empty($errors)){
           $name   = remove_junk(($_POST['full-name']));
       $username   = remove_junk(($_POST['username']));
       $password   = remove_junk(($_POST['password']));
       $group_name   = remove_junk(($_POST['group_name']));
       $user_level = (int)($_POST['level']);
       $password = sha1($password);
       
       $data=[
        'name'=>$name,
        'username'=>$username,
        'password'=>$password,
        'group_level'=>$user_level,
        'group_name'=>$group_name,
        'group_status'=>"1",
        'last-login'=>null,
        'status'=>1

      ];
      $result2 = $users->insertOne($data);
      echo '<script>alert("Success")</script>';

   } else {
     $session->msg("d", $errors);
      redirect('add_user.php',false);
   }
 }
?>
<?php //include_once('layouts/header.php'); ?>
<link rel="stylesheet" href="libs/css/main.css" />
  <?php echo display_msg($msg); ?>
  <div class="row">
    <div class="panel-form">
      <div class="panel-heading-form">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Add New User</span>
       </strong>
      </div>
      <div class="panel-body">
        <div class="col-md-6">
          <form method="post" action="add_user.php">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="full-name" placeholder="Full Name">
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" name="username" placeholder="Username">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name ="password"  placeholder="Password">
            </div>
            <div class="form-group">
              <label for="group_name">User Group</label>
                <select class="form-control" name="group_name">
                  <?php foreach ($groups as $group ):                    ?>
                   <option value="<?php echo ucwords($group['group_name']);?>"><?php echo ucwords($group['group_name']);?></option>
                <?php endforeach;?>
                </select>
            </div>
            <div class="form-group">
              <label for="level">User Level</label>
                <select class="form-control" name="level">
                  <?php foreach ($groups as $group ):
                    ?>
                  
                   <option value="<?php echo  ucwords($group['group_level']);?>"><?php echo  ucwords($group['group_level']);?></option>
                <?php endforeach;?>
                </select>
            </div>
            
            <div class="form-group clearfix">
              <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
            </div>
        </form>
        </div>

      </div>

    </div>
  </div>

<?php //include_once('layouts/footer.php'); ?>
