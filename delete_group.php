<?php
  require_once('includes/load.php');
  require 'vendor/autoload.php';


use MongoDB\Client;
  // Checkin What level user has permission to view this page
   page_require_level(1);
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
