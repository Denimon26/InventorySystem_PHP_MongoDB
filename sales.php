<?php
$page_title = 'Sales';
require_once('includes/load.php');
require 'vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

// Connect to MongoDB
$uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
$client = new Client($uri);
$database = $client->selectDatabase('inventory_system');
$products2 = $database->selectCollection('product');
$sales2 = $database->selectCollection('sales');

// Fetch all products
$products = $products2->find();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_items = $_POST['products'];
    $total_cost = 0;
    $sale_items = [];

    foreach ($order_items as $product_id => $quantity) {
        if ($quantity > 0) {
            $product = $products2->findOne(['_id' => new ObjectId($product_id)]);
            $item_total = $product['buy_price'] * $quantity;
            $total_cost += $item_total;

            $sale_items[] = [
                'product_id' => $product_id,
                'product_name' => $product['name'],
                'quantity' => $quantity,
                'price' => $product['buy_price'],
                'total' => $item_total,
            ];

            // Update product stock
            $products2->updateOne(
                ['_id' => new ObjectId($product_id)],
                ['$inc' => ['quantity' => -$quantity]]
            );
        }
    }

    // Save sale to the database
    $sales2->insertOne([
        'sale_items' => $sale_items,
        'total_cost' => $total_cost,
        'date' => new MongoDB\BSON\UTCDateTime(),
    ]);

    $msg = "Sale successfully recorded. Total Cost: ₱" . $total_cost;
}
?>
<?php include_once('layouts/header.php'); ?>
<?php include_once('layouts/admin_menu.php'); ?>
<h2>Sales</h2>
<form method="POST" action="sales.php">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>In-Stock</th>
                <th>Order Quantity</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo $product['name']; ?></td>
                    <td>₱ <?php echo $product['buy_price']; ?></td>
                    <td><?php echo $product['quantity']; ?></td>
                    <td>
                        <input type="number" name="products[<?php echo $product['_id']; ?>]" class="form-control" min="0" max="<?php echo $product['quantity']; ?>">
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <button type="submit" class="btn btn-primary">Process Sale</button>
</form>
<?php include_once('layouts/footer.php'); ?>
