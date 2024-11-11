<?php
$page_title = 'Edit Product';
require_once('includes/load.php');
use MongoDB\Client;

// Check user permission
page_require_level(2);

function page_require_level($required_level) {
    $uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
    $client = new Client($uri);
    $database = $client->selectDatabase('inventory_system');
    $admins = $database->selectCollection('admin');
    $admin = $admins->findOne(['_id' => $_SESSION['user_id']]);

    if (!isset($admin) || $admin['user_level'] > (int) $required_level) {
        redirect('index.php', false);
    }
}

$uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
$client = new Client($uri);
$database = $client->selectDatabase('inventory_system');

$prod = $database->selectCollection('product');
$cats = $database->selectCollection('categories');

$prodid = $_GET['id'] ?? null;
$product = $prod->findOne(['_id' => new MongoDB\BSON\ObjectId($prodid)]);
$all_categories = $cats->find();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $req_fields = ['product-title', 'product-categorie', 'product-quantity', 'buying-price'];
    validate_fields($req_fields);

    if (empty($errors)) {
        $p_name  = remove_junk($_POST['product-title']);
        $p_cat   = $_POST['product-categorie'];
        $p_qty   = (int)remove_junk($_POST['product-quantity']);
        $p_buy   = (float)remove_junk($_POST['buying-price']);

        // Handle image upload if provided
        if (isset($_FILES['product-image']) && $_FILES['product-image']['error'] == 0) {
            $maxFileSize = 1048576; // 1MB in bytes
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            $imageFileType = strtolower(pathinfo($_FILES["product-image"]["name"], PATHINFO_EXTENSION));
            
            if ($_FILES['product-image']['size'] > $maxFileSize) {
                $errors[] = 'Image file size exceeds the maximum limit of 1MB.';
            }
            
            $check = getimagesize($_FILES["product-image"]["tmp_name"]);
            if ($check !== false && in_array($imageFileType, $allowedTypes)) {
                // Convert image to Base64
                $imageData = file_get_contents($_FILES["product-image"]["tmp_name"]);
                $base64Image = 'data:' . $check['mime'] . ';base64,' . base64_encode($imageData);
            } else {
                $errors[] = 'Invalid image file type. Only JPG, JPEG, PNG, and GIF are allowed.';
            }
        } else {
            $base64Image = $product['image'] ?? null; // Keep existing image if no new image is uploaded
        }

        // Update product data
        $data = [
            'name' => $p_name,
            'categories' => $p_cat,
            'buy_price' => $p_buy,
            'quantity' => $p_qty,
            'date' => new MongoDB\BSON\UTCDateTime(),
            'image' => $base64Image,  // Store new Base64 image or retain old image
        ];

        $result = $prod->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($prodid)],
            ['$set' => $data]
        );

        if ($result->getModifiedCount() > 0) {
            $session->msg('s', "Product has been updated!");
            redirect('product.php', false);
        } else {
            $session->msg('d', 'Failed to update product!');
            redirect("edit_product.php?id={$prodid}", false);
        }
    } else {
        $session->msg("d", $errors);
        redirect("edit_product.php?id={$prodid}", false);
    }
}
?>

<?php include_once('layouts/header.php'); ?>
<?php include_once('layouts/admin_menu.php'); ?>
<link rel="stylesheet" href="libs/css/main.css" />

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong><span class="glyphicon glyphicon-th"></span> <span>Edit Product</span></strong>
        </div>
        <div class="panel-body">
            <div class="col-md-7">
                <form method="post" action="edit_product.php?id=<?php echo $prodid; ?>" enctype="multipart/form-data">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-th-large"></i></span>
                            <input type="text" class="form-control" name="product-title" value="<?php echo remove_junk($product['name']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <select class="form-control" name="product-categorie">
                                    <option value="">Select a category</option>
                                    <?php foreach ($all_categories as $cat): ?>
                                        <option value="<?php echo $cat['name']; ?>" <?php if($product['categories'] === $cat['name']): echo 'selected'; endif; ?>>
                                            <?php echo remove_junk($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="product-image">Update Product Image</label>
                        <input type="file" name="product-image" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="qty">Quantity</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-shopping-cart"></i></span>
                            <input type="number" class="form-control" name="product-quantity" value="<?php echo remove_junk($product['quantity']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="qty">Price</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i> ₱ </i></span>
                            <input type="number" class="form-control" name="buying-price" value="<?php echo remove_junk($product['buy_price']); ?>">
                        </div>
                    </div>
                    <button type="submit" name="product" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>
