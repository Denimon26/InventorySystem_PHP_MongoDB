<?php
require_once('includes/load.php');
require 'vendor/autoload.php';

use MongoDB\Client;

// Check if the user has permission to delete
page_require_level(2);

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
    redirect('home.php', false);
  }
}

// Initialize MongoDB client and select the collection
$client = new Client('mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0');
$db = $client->inventory_system;
$productCollection = $db->product;

// Get the product ID from the URL
if (isset($_GET['id'])) {
  $productId = $_GET['id'];

  // Attempt to delete the product by ID
  $result = $productCollection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($productId)]);

  // Check if deletion was successful
  if ($result->getDeletedCount() > 0) {
    $session->msg("s", "Product deleted successfully.");
  } else {
    $session->msg("d", "Failed to delete the product.");
  }
  redirect('product.php');
} else {
  $session->msg("d", "No product ID provided.");
  redirect('product.php');
}
?>
