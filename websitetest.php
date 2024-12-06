<?php
require 'vendor/autoload.php';

use MongoDB\Client;

// MongoDB connection details
$uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
$client = new Client($uri);

// Connect to the database and collection
$database = $client->selectDatabase('inventory_system');
$productsCollection = $database->selectCollection('product');

// Handle updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
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

    // Update product in MongoDB
    $updateFields = ['name' => $newName];
    if ($newImage) {
        $updateFields['image'] = $newImage;
    }

    $productsCollection->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($productId)],
        ['$set' => $updateFields]
    );

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch products
$products = $productsCollection->find();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
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
    </style>
</head>
<body>
    <h1 style="text-align:center;">Product Management</h1>
    <div class="product-container">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <?php if (isset($product['image']) && !empty($product['image'])): ?>
                    <img class="product-image" src="<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image">
                <?php else: ?>
                    <img class="product-image" src="uploads/products/no_image.png" alt="Default Image">
                <?php endif; ?>
                <form class="product-form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" value="<?php echo $product['_id']; ?>">
                    <input type="text" name="product_name" value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" required>
                    <input type="file" name="product_image">
                    <button type="submit" name="update_product">Update</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
