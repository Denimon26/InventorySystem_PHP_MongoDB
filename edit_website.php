<?php
$page_title = 'Edit Website';
require_once('includes/load.php');
require 'vendor/autoload.php';

use MongoDB\Client;

// MongoDB connection
$uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
$client = new Client($uri);
$database = $client->selectDatabase('inventory_system');
$website = $database->selectCollection('website');
$admin_id = $_SESSION['user_id'];

// Check if user has required level access
function page_require_level($required_level) {
  global $database;
  $admins = $database->selectCollection('admin');
  $admin = $admins->findOne(['_id' => $_SESSION['user_id']]);

  if (!$admin || $admin['user_level'] > (int)$required_level) {
    redirect('home.php', false);
  }
}
page_require_level(3);
?>

<?php
// Update website image in Base64 format and save to MongoDB
if (isset($_POST['submit_image'])) {
    if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] == 0) {
        $maxFileSize = 1048576; // 1MB limit
        $imageFileType = strtolower(pathinfo($_FILES["image_upload"]["name"], PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if ($_FILES['image_upload']['size'] > $maxFileSize) {
            $session->msg('d', 'File size exceeds the maximum limit of 1MB.');
        } elseif (!in_array($imageFileType, $allowedTypes)) {
            $session->msg('d', 'Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.');
        } else {
            $imageData = file_get_contents($_FILES["image_upload"]["tmp_name"]);
            $base64Image = 'data:' . mime_content_type($_FILES["image_upload"]["tmp_name"]) . ';base64,' . base64_encode($imageData);

            try {
                $website->updateOne(
                    ['_id' => 'main_image'],
                    ['$set' => ['image' => $base64Image]],
                    ['upsert' => true]
                );
                $session->msg('s', 'Website image updated successfully.');
            } catch (Exception $e) {
                $session->msg('d', 'Error saving image: ' . $e->getMessage());
            }
        }
    } else {
        $session->msg('d', 'No file selected or upload error occurred.');
    }
    redirect('edit_website.php');
}
?>

<?php include_once('layouts/header.php'); ?>
<?php include_once('layouts/admin_menu.php'); ?>
<div class="row">
    <div class="col-md-12"><?php echo display_msg($msg); ?></div>
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading"><span class="glyphicon glyphicon-camera"></span> Edit Website Image</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <?php
                        $current_image = $website->findOne(['_id' => 'main_image'])['image'] ?? '';
                        if (!empty($current_image)) {
                            echo '<img src="' . htmlspecialchars($current_image) . '" alt="Website Image" style="width: 100px; height: 100px; border-radius: 50%;">';
                        } else {
                            echo '<img src="uploads/default.png" alt="Default Image" style="width: 100px; height: 100px; border-radius: 50%;">';
                        }
                        ?>
                    </div>
                    <div class="col-md-8">
                        <form class="form" action="edit_website.php" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <input type="file" name="image_upload" class="btn btn-default btn-file"/>
                            </div>
                            <div class="form-group">
                                <button type="submit" name="submit_image" class="btn btn-primary">Change</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php //include_once('layouts/footer.php'); ?>
