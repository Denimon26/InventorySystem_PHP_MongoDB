<?php
require 'vendor/autoload.php';

use MongoDB\Client;

// MongoDB connection details
$uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
$client = new Client($uri);

// Connect to the database and collection
$database = $client->selectDatabase('inventory_system');
$websiteImagesCollection = $database->selectCollection('website_image');

// Fetch all images from the collection
$images = $websiteImagesCollection->find();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Gallery</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        .gallery {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }

        .gallery-item {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 300px;
            text-align: center;
        }

        .gallery-item img {
            width: 100%;
            height: auto;
        }

        .gallery-item h3 {
            background-color: #ff5733;
            color: white;
            margin: 0;
            padding: 10px;
            font-size: 1rem;
        }

        .gallery-item p {
            padding: 10px;
            font-size: 0.9rem;
            color: #555;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center;">Service Gallery</h1>
    <div class="gallery">
        <?php
        // Display each image from the database
        foreach ($images as $image) {
            echo '
                <div class="gallery-item">
                    <img src="' . htmlspecialchars($image['image']) . '" alt="' . htmlspecialchars($image['name']) . '">
                    <h3>' . htmlspecialchars($image['name']) . '</h3>
                </div>
            ';
        }
        ?>
    </div>
</body>
</html>
