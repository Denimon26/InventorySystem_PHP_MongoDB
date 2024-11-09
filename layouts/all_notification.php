<?php
require 'vendor/autoload.php';
use MongoDB\Client;

// MongoDB Client and Collections
$client = new Client("mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&ssl=true&appName=Cluster0");
$notificationCollection = $client->inventory_system->notification;

// Fetch all notifications, ordered by date descending
try {
    $allNotifications = $notificationCollection->find([], ['sort' => ['date' => -1]]);
} catch (Exception $e) {
    $allNotifications = [];
    error_log("Error fetching all notifications: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Notifications</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .notification-list { margin-top: 20px; }
        .notification-item { padding: 15px; border-bottom: 1px solid #ddd; }
        .notification-item.read { background-color: #f9f9f9; }
        .notification-item.unread { font-weight: bold; }
        .mark-as-read { cursor: pointer; color: #007bff; }
    </style>
</head>
<body>
<div class="container">
    <h2>All Notifications</h2>
    <div class="notification-list">
        <?php if (iterator_count($allNotifications) > 0): ?>
            <?php foreach ($allNotifications as $notification): ?>
                <div class="notification-item <?php echo $notification['is_read'] ? 'read' : 'unread'; ?>">
                    <p><?php echo htmlspecialchars($notification['message']); ?></p>
                    <small><?php echo date("F j, Y, g:i a", $notification['date']->toDateTime()->getTimestamp()); ?></small>
                    <?php if (!$notification['is_read']): ?>
                        <span class="mark-as-read" data-id="<?php echo $notification['_id']; ?>">Mark as read</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No notifications found.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    // Mark a notification as read
    $(document).on('click', '.mark-as-read', function () {
        const notificationId = $(this).data('id');
        
        $.ajax({
            url: 'mark_notification_read.php',
            method: 'POST',
            data: { id: notificationId },
            success: function (response) {
                if (response.success) {
                    location.reload(); // Reload to update the notification list
                } else {
                    alert('Failed to mark as read.');
                }
            }
        });
    });
</script>
</body>
</html>
