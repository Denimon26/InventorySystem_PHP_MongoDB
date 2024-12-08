<?php
require 'vendor/autoload.php';
require_once('includes/load.php');

use MongoDB\Client;

// MongoDB connection details
$uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
$client = new Client($uri);

// Connect to the database and collection
$database = $client->selectDatabase('inventory_system');
$websiteImagesCollection = $database->selectCollection('website_image');

// Handle updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_product'])) {
        $productId = $_POST['product_id'];
        $newName = $_POST['product_name'];
        $newImage = null;

        // Handle image upload if provided
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            $maxFileSize = 1048576; // 1MB
            $imageFileType = strtolower(pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

            if ($_FILES['product_image']['size'] <= $maxFileSize && in_array($imageFileType, $allowedTypes)) {
                $imageData = file_get_contents($_FILES["product_image"]["tmp_name"]);
                $newImage = 'data:' . mime_content_type($_FILES["product_image"]["tmp_name"]) . ';base64,' . base64_encode($imageData);
            }
        }

        // Update website_image in MongoDB
        $updateFields = ['name' => $newName];
        if ($newImage) {
            $updateFields['image'] = $newImage;
        }

        $websiteImagesCollection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($productId)],
            ['$set' => $updateFields]
        );

        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    // Handle deletion
    if (isset($_POST['delete_product'])) {
        $productId = $_POST['product_id'];

        $websiteImagesCollection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($productId)]);

        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Fetch website images
$websiteImages = $websiteImagesCollection->find();
?>
<?php include_once('layouts/header.php'); ?>
<?php include_once('layouts/admin_menu.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Image Management</title>
    <style>
        .product-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .product-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            width: 250px;
        }

        .product-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
        }

        .product-form {
            margin-top: 10px;
        }
        /* General Styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

h1 {
    padding-top: 10px;
    margin: 20px 0;
    font-size: 2rem;
    color: #333;
}

/* Product Container */
.product-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
    padding: 20px;
}

/* Product Card */
.product-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    background-color: #fff;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    width: 250px;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
}

/* Product Image */
.product-image {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 15px;
}

/* Product Form */
.product-form {
    margin-top: 10px;
}

/* Input Styles */
.product-form input[type="text"],
.product-form input[type="file"] {
    width: 100%;
    padding: 10px;
    margin: 5px 0;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 1rem;
}

/* Button Styles */
.product-form .update-button {
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
    width: 100%;
    transition: background-color 0.3s ease;
}

.product-form .update-button:hover {
    background-color: #45a049;
}

/* Error Styling for Inputs */
.product-form input:invalid { 
}

/* Delete Button Styles */
.delete-button {
    padding: 10px 20px;
    background-color: #e74c3c; /* Red color */
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
    width: 100%;
    transition: background-color 0.3s ease;
}

.delete-button:hover {
    background-color: #c0392b; /* Darker red on hover */
}

/* Responsive Design */
@media (max-width: 768px) {
    .product-container {
        flex-direction: column;
        align-items: center;
    }

    .product-card {
        width: 90%;
    }
}

    </style>
</head>
<body>
    <h1 style="text-align:center;">Website Image Management</h1>
    <div class="product-container">
        <?php foreach ($websiteImages as $image): ?>
            <div class="product-card">
                <?php if (isset($image['image']) && !empty($image['image'])): ?>
                    <img class="product-image" src="<?php echo htmlspecialchars($image['image']); ?>" alt="Image">
                <?php else: ?>
                    <img class="product-image" src="uploads/products/no_image.png" alt="Default Image">
                <?php endif; ?>
                <form class="product-form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" value="<?php echo $image['_id']; ?>">
                    <input type="text" name="product_name" value="<?php echo htmlspecialchars($image['name'] ?? ''); ?>" required>
                    <input type="file" name="product_image">
                    <button type="submit" name="update_product" class="update-button">Update</button>
                    <button type="submit" name="delete_product" class="delete-button" onclick="return confirm('Are you sure you want to delete this product?');">Delete</button>

                </form>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
