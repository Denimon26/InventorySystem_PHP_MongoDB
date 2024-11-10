<?php
require_once('includes/load.php');
require 'vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

$uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
$client = new Client($uri);
$database = $client->selectDatabase('inventory_system');
$products2 = $database->selectCollection('product');
$sales = $database->selectCollection('sales');

// Check if product ID is passed in the URL
if (isset($_GET['id'])) {
    $productId = new ObjectId($_GET['id']);
    $product = $products2->findOne(['_id' => $productId]);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $quantity_sold = (int)$_POST['quantity_sold'];
        $admin_id = $_SESSION['user_id'];
        $total_price = $product['buy_price'] * $quantity_sold;

        if ($quantity_sold > 0 && $quantity_sold <= $product['quantity']) {
            // Update product quantity
            $new_quantity = $product['quantity'] - $quantity_sold;
            $products2->updateOne(['_id' => $productId], ['$set' => ['quantity' => $new_quantity]]);

            // Record the sale in the sales collection
            $sales->insertOne([
                'product_id' => $productId,
                'quantity_sold' => $quantity_sold,
                'sale_date' => new MongoDB\BSON\UTCDateTime(),
                'sold_by' => new ObjectId($admin_id),
                'total_price' => $total_price
            ]);

            // Redirect to sales_report.php
            header("Location: sales_report.php");
            exit;
        } else {
            echo "Invalid quantity.";
        }
    }
} else {
    echo "Product not found.";
    exit;
}
?>
<link rel="stylesheet" href="libs/css/main.css" />
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Sell Product: <?php echo $product['name']; ?></span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="POST" class="clearfix">
                    <p>Current Stock: <?php echo $product['quantity']; ?></p>
                    <div class="form-group">
                        <label for="quantity_sold">Quantity to Sell:</label>
                        <input type="number" name="quantity_sold" class="form-control" min="1" max="<?php echo $product['quantity']; ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Record Sale</button>
                </form>
            </div>
        </div>
    </div>
</div>
