<?php $user = current_user(); ?>
<!DOCTYPE html>
  <html lang="en">
    <head>
    <meta charset="UTF-8">
    <title><?php if (!empty($page_title))
           echo remove_junk($page_title);
            elseif(!empty($user))
           echo ucfirst($user['name']);
            else echo "Inventory Management System";?>
    </title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
    <link rel="stylesheet" href="libs/css/main.css" />

    <style>
      #notif-img{
  width: 24px;
  height: 24px;
  cursor: pointer;
}
#notification-count {
    top: 5px;
    right: 10px;
    background: red;
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
  <?php  if ($session->isUserLoggedIn(true)): ?>

    <header id="header">
      <div class="logo pull-left"> Inventory System</div>
      <div class="header-content">
      <div class="header-date pull-left">
        <strong><?php echo date("F j, Y, g:i a");?></strong>
      </div>
      <div class="pull-right clearfix">
        <ul class="info-menu list-inline list-unstyled">
          <li class="profile">
            <a href="#" data-toggle="dropdown" class="toggle" aria-expanded="false">
              <img src="uploads/users/<?php echo $user['image'];?>" alt="user-image" class="img-circle img-inline">
              <span><?php echo remove_junk(ucfirst($user['name'])); ?> <i class="caret"></i></span>
            </a>
            <ul class="dropdown-menu">
              <li>
                  <a href="profile.php?id=<?php echo (int)$user['id'];?>">
                      <i class="glyphicon glyphicon-user"></i>
                      Profile
                  </a>
              </li>
             <li>
                 <a href="edit_account.php" title="edit account">
                     <i class="glyphicon glyphicon-cog"></i>
                     Settings
                 </a>
             </li>
             <li class="last">
                 <a href="logout.php">
                     <i class="glyphicon glyphicon-off"></i>
                     Logout
                 </a>
             </li>
           </ul>
          </li>
        </ul>
      </div>
      <div class="header-notif pull-right">
      <button class="notification-btn" id="notification-btn" data-toggle="dropdown" aria-expanded="false">
        <?php
        // Fetch notifications
        $user_id = $user['id'];
        $query = "SELECT * FROM notifications WHERE is_read = 0 ORDER BY date DESC";
        $result = $db->query($query);
        $notification_count = $result->num_rows;
        ?>
        <span id="notification-count"><?php echo $notification_count; ?></span>
        <img id="notif-img" src="pictures/bell-icon5.png" alt="Notifications">
      </button>

      <!-- Notification Dropdown -->
      <ul class="dropdown-menu notifications-dropdown">
        <?php if ($notification_count > 0): ?>
          <?php while ($notification = $result->fetch_assoc()): ?>
            <li>
              <a href="view_notification.php?id=<?php echo $notification['id']; ?>">
                <strong><?php echo $notification['message']; ?></strong>
                <br>
                <small><?php echo date("F j, Y, g:i a", strtotime($notification['date'])); ?></small>
              </a>
            </li>
          <?php endwhile; ?>
          <li class="last">
            <a href="all_notifications.php">See all notifications</a>
          </li>
        <?php else: ?>
          <li>No new notifications</li>
        <?php endif; ?>
      </ul>
    </div>
     </div>
    </header>
    <div class="sidebar">
      <?php if($user['user_level'] === '1'): ?>
        <!-- admin menu -->
      <?php include_once('admin_menu.php');?>

      <?php elseif($user['user_level'] === '2'): ?>
        <!-- Special user -->
      <?php include_once('special_menu.php');?>

      <?php elseif($user['user_level'] === '3'): ?>
        <!-- User menu -->
      <?php include_once('user_menu.php');?>

      <?php endif;?>

   </div>
<?php endif;?>

<div class="page">
  <div class="container-fluid">
