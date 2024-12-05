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

// Define available services
$services = [
    'Wheel Change',
    'Oil Change',
    'Engine Diagnostics',
    'Brake Inspection'
];

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle Product Sales
    if (!empty($_POST['products'])) {
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

        // Save product sales to the database
        if (!empty($sale_items)) {
            $sales2->insertOne([
                'sale_items' => $sale_items,
                'total_cost' => $total_cost,
                'date' => new MongoDB\BSON\UTCDateTime(),
            ]);
            $msg .= "Product sale successfully recorded. Total Cost: ₱" . $total_cost . "<br>";
        }
    }

    // Handle Service Sales
    if (!empty($_POST['service']) && isset($_POST['price'])) {
        $selected_service = $_POST['service'];
        $service_cost = (float)$_POST['price'];

        if ($selected_service && $service_cost > 0) {
            // Record service sale in the database
            $sales2->insertOne([
                'service' => $selected_service,
                'cost' => $service_cost,
                'date' => new MongoDB\BSON\UTCDateTime(),
            ]);
            $msg .= "Service sale successfully recorded. Service: $selected_service, Cost: ₱" . $service_cost . "<br>";
        } else {
            $msg .= "Please select a valid service and price.<br>";
        }
    }
}
?>
<?php include_once('layouts/header.php'); ?>
<?php include_once('layouts/admin_menu.php'); ?>
<h2>Sales</h2>

<?php if (!empty($msg)): ?>
    <div class="alert alert-<?php echo strpos($msg, 'successfully') !== false ? 'success' : 'danger'; ?>">
        <?php echo $msg; ?>
    </div>
<?php endif; ?>

<!-- Product Sales Form -->
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

    <h2>Service Sales</h2>
    <div class="form-group">
        <label for="service">Select Service</label>
        <select name="service" id="service" class="form-control">
            <option value="">-- Select a Service --</option>
            <?php foreach ($services as $service_name): ?>
                <option value="<?php echo $service_name; ?>"><?php echo $service_name; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="price">Enter Service Price (₱)</label>
        <input type="number" name="price" id="price" class="form-control" min="0" step="0.01" placeholder="Enter price">
    </div>

    <button type="submit" class="btn btn-primary">Process Sale</button>
</form>

<?php //include_once('layouts/footer.php'); ?>
