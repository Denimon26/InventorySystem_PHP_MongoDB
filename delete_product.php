<?php
  require_once('includes/load.php');
  require 'vendor/autoload.php';

  use MongoDB\Client;

  // Checkin What level user has permission to view this page
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
    // If the user does not have permission, redirect to the home page
    redirect('home.php', false);
  }
}
?>
<?php
$client = new MongoDB\Client('mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0');
$db = $client->inventory_system;
$product = $db->product; 
  $prod = $_GET['id'];
  $product->deleteOne(['name'=>$prod]);
    $session->msg("s","Products deleted.");
    redirect('product.php');

?>

