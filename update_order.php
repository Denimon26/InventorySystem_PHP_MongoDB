<?php
require 'vendor/autoload.php';
use MongoDB\Client;

$uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
$client = new Client($uri);
$database = $client->selectDatabase('inventory_system');
$orders = $database->selectCollection('orders');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $orderId = $_POST['order_id'];
  $action = $_POST['action'];

  try {
    switch ($action) {
      case 'ready':
        $orders->updateOne(['_id' => new MongoDB\BSON\ObjectId($orderId)], ['$set' => ['status' => 'prepared']]);
        break;
      case 'cancel':
        $orders->deleteOne(['_id' => new MongoDB\BSON\ObjectId($orderId)]);
        break;
      case 'unprepare':
        $orders->updateOne(['_id' => new MongoDB\BSON\ObjectId($orderId)], ['$set' => ['status' => 'pending']]);
        break;
      case 'paid':
        $orders->updateOne(['_id' => new MongoDB\BSON\ObjectId($orderId)], ['$set' => ['status' => 'completed']]);
        break;
    }

    header('Location: cashier.php?status=success');
    exit();

  } catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
  }
}
?>
