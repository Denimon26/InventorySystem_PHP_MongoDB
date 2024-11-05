<?php
require 'vendor/autoload.php';
use MongoDB\Client;

// Ensure session and user information are set properly
//$user = current_user();
$client = new Client("mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&ssl=true&appName=Cluster0");
$collection = $client->inventory->notifications;

// Fetch unread notifications with error handling
try {
    $unreadNotifications = $collection->find(['is_read' => 0], ['sort' => ['date' => -1]]);
    $notification_count = $collection->countDocuments(['is_read' => 0]);
} catch (Exception $e) {
    $notification_count = 0;
    $unreadNotifications = [];
    error_log("Error fetching notifications: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>
        <?php
        echo !empty($page_title) 
            ? remove_junk($page_title) 
            : (!empty($user) ? ucfirst($user['name']) : "Inventory Management System");
        ?>
    </title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css"/>
    <link rel="stylesheet" href="libs/css/main.css"/>
    <style>
#notif-img{
  width: 30px;
  height: 30px;
  cursor: pointer;
}
#notification-count {
    top: 5px;
    right: 10px;
    color: white;
    border-radius: 20px;
    padding: 2px 8px;
}
.notification-btn {
    background: none;
    border: none;
    cursor: pointer;
}
.notification-btn:hover img {
    filter: brightness(0.3);
}
    </style>
</head>
<body>
<?php if (isset($session) && $session->isUserLoggedIn(true)): ?>
<header id="header">
    <div class="logo pull-left">Inventory System</div>
    <div class="header-content">
        <div class="header-date pull-left">
            <strong><?php echo date("F j, Y, g:i a"); ?></strong>
        </div>
        <div class="header-notif pull-right">
            <button class="notification-btn" id="notification-btn" data-toggle="dropdown" aria-expanded="false">
                <span id="notification-count"><?php echo $notification_count; ?></span>
                <img id="notif-img" src="pictures/bell-icon7.png" alt="Notifications">
            </button>

            <!-- Notification Dropdown -->
            <ul class="dropdown-menu notifications-dropdown">
                <?php if ($notification_count > 0): ?>
                    <?php foreach ($unreadNotifications as $notification): ?>
                        <li>
                            <a href="view_notification.php?id=<?php echo $notification['_id']; ?>">
                                <strong><?php echo htmlspecialchars($notification['message']); ?></strong><br>
                                <small>
                                    <?php echo date("F j, Y, g:i a", 
                                        $notification['date']->toDateTime()->getTimestamp()); ?>
                                </small>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <li class="last"><a href="all_notifications.php">See all notifications</a></li>
                <?php else: ?>
                    <li>No new notifications</li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="pull-right clearfix">
            <ul class="info-menu list-inline list-unstyled">
                <li class="profile">
                    <a href="#" data-toggle="dropdown" class="toggle" aria-expanded="false">
                        <img src="uploads/users/<?php echo htmlspecialchars($user['image']); ?>" 
                             alt="user-image" class="img-circle img-inline">
                             <span>
                        <?php echo isset($user) ? ucfirst(remove_junk($user['name'])) : ""; ?> <i class="caret"></i>
                    </span>
                    </a>
                    <ul class="dropdown-menu">
                    <li><a href="profile.php?id=<?php echo isset($user['id']) ? (int) $user['id'] : 0; ?>">
                            <i class="glyphicon glyphicon-user"></i> Profile
                        </a></li>
                        <li><a href="edit_account.php" title="edit account">
                            <i class="glyphicon glyphicon-cog"></i> Settings
                        </a></li>
                        <li class="last"><a href="logout.php">
                            <i class="glyphicon glyphicon-off"></i> Logout
                        </a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</header>
</div>
<?php endif; ?>

<div class="page">
  <div class="container-fluid">
    <!-- Page content goes here -->
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
</body>
</html>
