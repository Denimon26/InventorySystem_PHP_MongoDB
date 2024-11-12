<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <title>ONIN - Navbar</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: white;
            padding: 15px;
            border-bottom: 1px solid #ccc;
        }

        .navbar h1 {
            color: red;
            font-weight: 900;
            font-size: 28px;
        }

        .nav-links {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 20px;
        }

        .nav-item a {
            text-decoration: none;
            color: black;
            font-weight: bold;
            transition: color 0.3s;
        }

        .nav-item a:hover {
            color: gray;
        }

        /* Table Styling */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            padding: 0;
            font-size: 14px;
        }

        .table thead {
            background-color: #333;
            color: #ffc107;
        }

        .table th,
        .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-links {
                flex-direction: column;
                width: 100%;
            }

            .table {
                font-size: 12px;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .table {
                font-size: 13px;
            }
        }

        @media (min-width: 1025px) and (max-width: 1440px) {
            .table {
                font-size: 14px;
            }
        }

        @media (min-width: 1441px) {
            .table {
                font-size: 16px;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar using Bootstrap classes -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
        <div class="container-fluid">
            <h1 class="navbar-brand text-danger m-0">LIONSTECH CYCLESHOP</h1>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Reviews</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div id="content">
        <?php
        $page_title = 'All Product';
        require_once('includes/load.php');
        require 'vendor/autoload.php';

        use MongoDB\Client;
        use MongoDB\BSON\ObjectId;

        // Checkin What level user has permission to view this page
        function page_require_level($required_level)
        {
            $uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
            $client = new Client($uri);
            $database = $client->selectDatabase('inventory_system');
        }

        // Connect to MongoDB and fetch products
        $uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
        $client = new Client($uri);
        $database = $client->selectDatabase('inventory_system');
        $products2 = $database->selectCollection('product');
        $products = $products2->find();
        ?>

        <!-- <link rel="stylesheet" href="libs/css/main.css" /> -->

        <div class="row">
            <div class="col-md-12">
                <?php echo display_msg($msg); ?>
            </div>
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;">#</th>
                                    <th>Photo</th>
                                    <th>Product Name</th>
                                    <th class="text-center" style="width: 10%;">In-Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td class="text-center"><?php echo count_id(); ?></td>
                                        <td>
                                            <div class="text-center">
                                                <?php if (isset($product['image']) && !empty($product['image'])): ?>
                                                    <img class="img-circle" src="<?php echo $product['image']; ?>" alt="Product Image" style="width: 100px; height: 100px;">
                                                <?php else: ?>
                                                    <img class="img-circle" src="uploads/products/no_image.png" alt="Default Image" style="width: 100px; height: 100px;">
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td><?php echo remove_junk($product['name']); ?></td>
                                        <td class="text-center"><?php echo remove_junk($product['quantity']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php
        ?>
    </div>

    <!-- Add Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>