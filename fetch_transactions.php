<?php
require 'vendor/autoload.php';

use MongoDB\Client;

$uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
$client = new Client($uri);
$database = $client->selectDatabase('inventory_system');
$orders = $database->selectCollection('orders');

header('Content-Type: application/json');
$username = isset($_POST['username']) ? $_POST['username'] : null;

if ($username) {
    $completedOrders = $orders->find(['status' => 'completed', 'username' => $username]);
    $response = [];

    foreach ($completedOrders as $order) {
        $response[] = [
            '_id' => (string)$order['_id'],
            'username' => htmlspecialchars($order['username']),
            'order_time' => $order['order_time']->toDateTime()->format('Y-m-d H:i:s'),
            'total_order_price' => $order['total_order_price'],
            'items' => $order['items']
        ];
    }

    echo json_encode($response);
} else {
    echo json_encode([]);
}
?>
