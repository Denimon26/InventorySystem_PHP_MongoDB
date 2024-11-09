<?php
require 'vendor/autoload.php';
use MongoDB\Client;

$client = new Client("mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&ssl=true&appName=Cluster0");
$collection = $client->inventory->notifications;

// Mark the notification as read when the user clicks on it
if (isset($_GET['id'])) {
    try {
        $notificationId = $_GET['id'];
        
        // Update the notification to mark it as read
        $collection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($notificationId)],
            ['$set' => ['is_read' => 1]]
        );
        
        // You can redirect to the page where the user can view the notification details, if needed.
        // For now, we'll just show a confirmation message.
        echo "Notification marked as read.";
        
    } catch (Exception $e) {
        error_log("Error marking notification as read: " . $e->getMessage());
    }
}
?>
