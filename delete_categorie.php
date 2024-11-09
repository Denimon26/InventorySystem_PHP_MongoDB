<?php
  require_once('includes/load.php');
  require 'vendor/autoload.php';

  use MongoDB\Client;

  // Check the user's permission level to view this page
  page_require_level(1);

  // MongoDB Client and Database setup
  $uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
  $client = new Client($uri);
  $database = $client->selectDatabase('inventory_system');
  $categoriesCollection = $database->selectCollection('categories');

  // Fetch category by ID from MongoDB
  $category_id = $_GET['id'];
  $category = $categoriesCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($category_id)]);

  if (!$category) {
    $session->msg("d", "Missing Category id.");
    redirect('categorie.php');
  }

  // Delete the category from MongoDB
  $delete_result = $categoriesCollection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($category_id)]);

  if ($delete_result->getDeletedCount() > 0) {
      $session->msg("s", "Category deleted.");
      redirect('categorie.php');
  } else {
      $session->msg("d", "Category deletion failed.");
      redirect('categorie.php');
  }

  // Function to require a user level check
  function page_require_level($required_level) {
    $uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
    $client = new Client($uri);
    $database = $client->selectDatabase('inventory_system');
    $adminsCollection = $database->selectCollection('admin');
    $admin = $adminsCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])]);

    if (!$admin) {
      redirect('index.php', false);
    }

    if ($admin['user_level'] <= (int)$required_level) {
      return true;
    } else {
      redirect('home.php', false);
    }
  }
?>
