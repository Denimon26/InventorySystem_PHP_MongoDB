<?php
  $page_title = 'Edit Category';
  require_once('includes/load.php');
  use MongoDB\Client;

  // Check what level user has permission to view this page
  function page_require_level($required_level) {
    $uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
    $client = new Client($uri);
    $database = $client->selectDatabase('inventory_system');
    $admins = $database->selectCollection('admin');
    $admin = $admins->findOne(['_id' => $_SESSION['user_id']]);

    if (!isset($admin) || $admin['user_level'] > (int) $required_level) {
      redirect('index.php', false);
    }
    return true;
  }
  page_require_level(1);
?>

<?php
  $uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
  $client = new Client($uri);
  $database = $client->selectDatabase('inventory_system');
  $cats = $database->selectCollection('categories');

  $cat_id = $_GET['id'];
  $categorie = $cats->findOne(['_id' => new MongoDB\BSON\ObjectId($cat_id)]);
  
  if (!$categorie) {
    $session->msg("d", "Missing category ID.");
    redirect('categorie.php');
  }
?>

<?php
if (isset($_POST['edit_cat'])) {
  $req_field = array('categorie-name');
  validate_fields($req_field);
  $cat_name = remove_junk($_POST['categorie-name']);

  if (empty($errors)) {
    $result = $cats->updateOne(
      ['_id' => new MongoDB\BSON\ObjectId($cat_id)],
      ['$set' => ['name' => $cat_name]]
    );

    if ($result->getModifiedCount() > 0) {
      $session->msg("s", "Successfully updated category.");
      redirect('categorie.php', false);
    } else {
      $session->msg("d", "Failed to update category.");
      redirect('categorie.php', false);
    }
  } else {
    $session->msg("d", $errors);
    redirect('edit_categorie.php?id=' . $cat_id, false);
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
   <div class="col-md-5">
     <div class="panel panel-default">
       <div class="panel-heading">
         <strong>
           <span class="glyphicon glyphicon-th"></span>
           <span>Editing <?php echo remove_junk(ucfirst($categorie['name']));?></span>
        </strong>
       </div>
       <div class="panel-body">
         <form method="post" action="edit_categorie.php?id=<?php echo $cat_id; ?>">
           <div class="form-group">
               <input type="text" class="form-control" name="categorie-name" value="<?php echo remove_junk(ucfirst($categorie['name'])); ?>">
           </div>
           <button type="submit" name="edit_cat" class="btn btn-primary">Update Category</button>
       </form>
       </div>
     </div>
   </div>
</div>

<?php include_once('layouts/footer.php'); ?>
