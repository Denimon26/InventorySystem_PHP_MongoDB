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
$serviceCollection = $database->selectCollection('service');

// Fetch all products
$products = $products2->find();

// Fetch all services from the collection
$services = $serviceCollection->find();

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle Product Sales
    if (!empty($_POST['products'])) {
        $order_items = array_filter($_POST['products'], fn($qty) => $qty > 0); // Only valid quantities
        $total_cost = 0;
        $sale_items = [];

        foreach ($order_items as $product_id => $quantity) {
            $product = $products2->findOne(['_id' => new ObjectId($product_id)]);
            if (!$product || $product['quantity'] < $quantity) {
                $msg .= "Invalid quantity for product: " . ($product['name'] ?? 'Unknown') . "<br>";
                continue;
            }

            $item_total = $product['buy_price'] * $quantity;
            $total_cost += $item_total;

            $sale_items[] = [
                'product_id' => (string) $product['_id'],
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
        $selected_service_id = $_POST['service'];
        $service_cost = (float) $_POST['price'];
        $note = !empty($_POST['note']) ? htmlspecialchars($_POST['note']) : null;

        if ($service_cost > 0) {
            $selected_service = $serviceCollection->findOne(['_id' => new ObjectId($selected_service_id)]);
            if ($selected_service) {
                // Record service sale in the database
                $sales2->insertOne([
                    'service_id' => (string) $selected_service['_id'],
                    'service_name' => $selected_service['name'],
                    'cost' => $service_cost,
                    'note' => $note,
                    'date' => new MongoDB\BSON\UTCDateTime(),
                ]);
                $msg .= "Service sale successfully recorded. Service: " . $selected_service['name'] . ", Cost: ₱" . $service_cost . "<br>";
            } else {
                $msg .= "Selected service not found.<br>";
            }
        } else {
            $msg .= "Please enter a valid service cost.<br>";
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
    <h3>Product Sales</h3>
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
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td>₱ <?php echo htmlspecialchars($product['buy_price']); ?></td>
                    <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                    <td>
                        <input type="number" name="products[<?php echo $product['_id']; ?>]" class="form-control" min="0" max="<?php echo htmlspecialchars($product['quantity']); ?>">
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Service Sales</h3>
    <div class="form-group">
        <label for="service">Select Service</label>
        <select name="service" id="service" class="form-control">
            <option value="">-- Select a Service --</option>
            <?php foreach ($services as $service): ?>
                <option value="<?php echo (string) $service['_id']; ?>"><?php echo htmlspecialchars($service['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="price">Enter Service Price (₱)</label>
        <input type="number" name="price" id="price" class="form-control" min="0" step="0.01" placeholder="Enter price">
    </div>
    <div class="form-group">
        <label for="note">Add a Note (Optional)</label>
        <textarea name="note" id="note" class="form-control" rows="3" placeholder="Add any comment or note about the service..."></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Process Sale</button>
</form>

<?php //include_once('layouts/footer.php'); ?>
