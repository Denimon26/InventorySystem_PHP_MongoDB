<?php
  $page_title = 'All Categories';
  require_once('includes/load.php');
  require 'vendor/autoload.php';

  use MongoDB\Client;

  // Check user permissions to view this page
  page_require_level(1);

  // Function to require user level
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

  // MongoDB connection
  $uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
  $client = new Client($uri);
  $database = $client->selectDatabase('inventory_system');
  $categoriesCollection = $database->selectCollection('categories');

  // Fetch all categories
  $all_categories = $categoriesCollection->find();

  // Handling category addition
  if (isset($_POST['add_cat'])) {
    $req_field = array('categorie-name');
    validate_fields($req_field);
    $cat_name = remove_junk($db->escape($_POST['categorie-name']));

    if (empty($errors)) {
      // Insert new category into MongoDB
      $result = $categoriesCollection->insertOne(['name' => $cat_name]);
      if ($result->getInsertedCount() > 0) {
        $session->msg("s", "Successfully Added New Category");
        redirect('categorie.php', false);
      } else {
        $session->msg("d", "Sorry, failed to insert.");
        redirect('categorie.php', false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('categorie.php', false);
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
</div>
<div class="row">
  <div class="col-md-5">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Add New Category</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="categorie.php">
          <div class="form-group">
            <input type="text" class="form-control" name="categorie-name" placeholder="Category Name" required>
          </div>
          <button type="submit" name="add_cat" class="btn btn-primary">Add Category</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-md-7">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>All Categories</span>
        </strong>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Categories</th>
              <th class="text-center" style="width: 100px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($all_categories as $key => $cat): ?>
              <tr>
                <td class="text-center"><?php echo $key + 1; ?></td>
                <td><?php echo remove_junk(ucfirst($cat['name'])); ?></td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="edit_categorie.php?id=<?php echo (string)$cat['_id']; ?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                      <span class="glyphicon glyphicon-edit"></span>
                    </a>
                    <a href="delete_categorie.php?id=<?php echo (string)$cat['_id']; ?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
                      <span class="glyphicon glyphicon-trash"></span>
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
<?php include_once('layouts/footer.php'); ?>
