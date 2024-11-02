<?php
  require_once('includes/load.php');
  require 'vendor/autoload.php';


use MongoDB\Client;
  // Checkin What level user has permission to view this page
   page_require_level(1);

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


  $uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
  $client = new Client($uri);
  $database = $client->selectDatabase('inventory_system');
  $groups = $database->selectCollection('group');
  $group_id = $_GET['id'];
  $group = $groups->deleteOne(['group_name' => $group_id]);
  if($group){
      $session->msg("s","Group has been deleted.");
      redirect('group.php');
  } else {
      $session->msg("d","Group deletion failed Or Missing Prm.");
      redirect('group.php');
  }
?>
