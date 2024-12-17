<?php
require 'vendor/autoload.php';

use MongoDB\Client;

$client = new Client("mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&ssl=true&appName=Cluster0");
$notificationCollection = $client->inventory_system->notification;
$productCollection = $client->inventory_system->product;
$database = $client->selectDatabase('inventory_system');
$users = $database->selectCollection('users');
$admins = $database->selectCollection('admin');

$user = $admins->findOne(['_id' => $_SESSION['user_id']]);
if (!$user) {
    $user = $users->findOne(['_id' => $_SESSION['user_id']]);
}

$userImage = isset($user['image']) ? htmlspecialchars($user['image']) : 'path/to/default/user-image.png';

try {
    $products_query = [
        '$expr' => [
            '$lte' => ['$quantity', '$critical_amount']
        ]
    ];
    $lowStockProducts = $productCollection->find($products_query)->toArray();
    $notification_count = count($lowStockProducts);
} catch (Exception $e) {
    $notification_count = 0;
    $lowStockProducts = [];
    error_log("Error fetching low stock notifications: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo !empty($page_title) ? remove_junk($page_title) : "Inventory Management System"; ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="libs/css/main.css" />

    <style>
        /* Dropdown Menu Styles */
        .info-menu .dropdown-menu {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 5px;
            min-width: 180px;
            padding: 0;
        }

        /* Dropdown Menu Items */
        .info-menu .dropdown-menu li {
            list-style: none;
        }

        .info-menu .dropdown-menu li a {
            display: block;
            color: #333;
            text-decoration: none;
            padding: 10px 15px;
            font-size: 14px;
        }

        .info-menu .dropdown-menu li a:hover {
            background-color: #f5f5f5;
            color: #000;
        }



        /* Highlight Last Item */
        .info-menu .dropdown-menu .last a {
            color: #d9534f;
        }

        .info-menu .dropdown-menu .last a:hover {
            background-color: #f5f5f5;
            color: #b52b27;
        }


        #notif-img {
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

        /* Dropdown Menu Styles */
        .header-notif .dropdown-menu {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 10px;
            width: 250px;
            padding: 0;
            max-height: 300px;
            overflow-y: auto;
        }

        /* Notification Item */
        .header-notif .dropdown-menu li {
            padding: 10px 15px;
            font-size: 14px;
            color: #333;
            border-bottom: 1px solid #f5f5f5;
        }

        /* Notification Item Hover */
        .header-notif .dropdown-menu li:hover {
            background-color: #f5f5f5;
            cursor: pointer;
        }

        /* No Notifications Message */
        .header-notif .dropdown-menu li {
            color: #777;
            font-style: italic;
            text-align: center;
            padding: 15px 0;
        }

        /* Show Dropdown */
        .header-notif .dropdown-menu.show {
            display: block;
        }



        @media (max-width: 600px) {
            .header-content .header-date {
                font-size: 5px;
            }

            .toggle span {
                font-size: 12px;
                /* Add this line to change the size */
            }

            .pull-right {
                font-size: 14px;
                /* Add this line to change the size */
            }

            #header .logo {
                width: 40%;
            }

        }

        @media (max-width: 555px) {
            .header-content .header-date {
                font-size: 0px;
            }

            #header .logo {
                width: 30%;
                font-size: 7px;
            }
        }



        /* Cart Button Styles */
        #cart-img {
            width: 30px;
            height: 30px;
            cursor: pointer;
        }

        #cart-count {
            top: 5px;
            right: 10px;
            color: white;
            border-radius: 20px;
            padding: 2px 8px;
        }

        /* Dropdown Menu Styles for Cart */
        .header-cart .dropdown-menu {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: 0 4px 8px rgb(253, 253, 253);
            margin-top: 10px;
            width: 250px;
            padding: 0;
            max-height: 300px;
            overflow-y: auto;
        }

        .header-cart .dropdown-menu li {
            padding: 10px 15px;
            font-size: 14px;
            color: #333;
            border-bottom: 1px solid #f5f5f5;
        }

        .header-cart .dropdown-menu li:hover {
            background-color: #f5f5f5;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php if (isset($session) && $session->isUserLoggedIn(true)): ?>
        <header id="header">
            <div class="logo pull-left">
                Inventory System
                <?php if ((int) $user['user_level'] != 1): ?>
                    <a href="home.php" class="btn btn-primary btn-sm" style="margin-left: 10px;">Home</a>
                <?php endif; ?>
            </div>

            <div class="header-content">
                <div class="header-date pull-left">
                    <strong><?php echo date("F j, Y, g:i a"); ?></strong>
                </div>

                <!-- Notification Section -->
                <div class="header-notif pull-right">
                    <button class="notification-btn" id="notification-btn" data-toggle="dropdown" aria-expanded="false">
                        <span id="notification-count"><?php echo $notification_count; ?></span>
                        <img id="notif-img" src="pictures/bell-icon7.png" alt="Notifications">
                    </button>
                    <!-- Notification Dropdown -->
                    <ul class="dropdown-menu" aria-labelledby="notification-btn" style="display: none;">
                        <?php if ($notification_count > 0): ?>
                            <?php foreach ($lowStockProducts as $product): ?>
                                <li><?php echo 'Low stock alert: ' . htmlspecialchars($product['name']) . ' has less than ' . $product['critical_amount'] . ' items left.'; ?></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>No new notifications</li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- User Profile Dropdown -->
                <div class="pull-right clearfix">
                    <ul class="info-menu list-inline list-unstyled">
                        <li class="profile">
                            <a href="#" data-toggle="dropdown" class="toggle" aria-expanded="false">
                                <img src="<?php echo $userImage; ?>" alt="user-image" class="img-circle img-inline">
                                <span>
                                    <?php echo isset($user['name']) ? ucfirst(remove_junk($user['name'])) : "Guest"; ?> <i class="caret"></i>
                                </span>
                            </a>

                            <!-- Profile Dropdown Menu -->
                            <ul class="dropdown-menu">
                                <li><a href="profile.php?id=<?php echo isset($user['id']) ? (int) $user['id'] : 0; ?>"><i class="glyphicon glyphicon-user"></i> Profile</a></li>
                                <li><a href="edit_account.php"><i class="glyphicon glyphicon-cog"></i> Settings</a></li>
                                <li class="last"><a href="logout.php"><i class="glyphicon glyphicon-off"></i> Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </header>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#notification-btn').on('click', function () {
                $(this).next('.dropdown-menu').toggle();
            });
        });
    </script>
</body>

</html>
