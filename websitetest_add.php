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

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_image'])) {
    $imageName = $_POST['image_name'];
    $imageData = null;

    // Handle image upload
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
        $maxFileSize = 1048576; // 1MB
        $imageFileType = strtolower(pathinfo($_FILES["image_file"]["name"], PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if ($_FILES['image_file']['size'] <= $maxFileSize && in_array($imageFileType, $allowedTypes)) {
            $imageContent = file_get_contents($_FILES["image_file"]["tmp_name"]);
            $imageData = 'data:' . mime_content_type($_FILES["image_file"]["tmp_name"]) . ';base64,' . base64_encode($imageContent);

            // Insert into MongoDB
            $websiteImagesCollection->insertOne([
                'name' => $imageName,
                'image' => $imageData,
            ]);

            echo "<p>Image uploaded successfully!</p>";
        } else {
            echo "<p>Invalid file type or size exceeded 1MB!</p>";
        }
    } else {
        echo "<p>Error uploading file!</p>";
    }
}
?>
<?php include_once('layouts/header.php'); ?>
<?php include_once('layouts/admin_menu.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Image</title>
    <style>

body {
    font-family: Arial, sans-serif;
    background-color: #f9f9f9;
    margin: 0;
    padding: 0;
}

/* Header Styling */
h1 {
    text-align: center;
    margin: 30px 0;
    font-size: 2rem;
    color: #333;
}

/* Form Container */
form {
    display: flex;
    flex-direction: column;
    width: 300px;
    margin: 0 auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Input and Label Styles */
label {
    margin-bottom: 5px;
    font-weight: bold;
    font-size: 1rem;
    color: #555;
}

input[type="text"],
input[type="file"] {
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 1rem;
    background-color: #f9f9f9;
}

/* Button Styles */
.add {
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
    transition: background-color 0.3s ease;
}

.add:hover {
    background-color: #45a049;
}

/* Error and Success Messages */
p {
    font-size: 1rem;
    color: #e74c3c;
    text-align: center;
}

/* Success Message Styling */
p.success {
    color: #2ecc71;
}

/* Responsive Design for Mobile */
@media (max-width: 768px) {
    form {
        width: 90%;
    }

    h1 {
        font-size: 1.5rem;
    }
}

    </style>
</head>
<body>
    <h1 style="text-align:center;">Add Image</h1>
    <form method="POST" enctype="multipart/form-data">
        <label for="image_name">Image Name:</label>
        <input type="text" id="image_name" name="image_name" required>

        <label for="image_file">Choose Image:</label>
        <input type="file" id="image_file" name="image_file" accept="image/*" required>

        <button class="add" type="submit" name="add_image">Add Image</button>
    </form>
</body>
</html>
