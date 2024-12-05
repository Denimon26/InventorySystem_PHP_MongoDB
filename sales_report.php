<?php
require_once('includes/load.php');
require 'vendor/autoload.php';

use MongoDB\Client;

// Connect to MongoDB
$uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
$client = new Client($uri);
$database = $client->selectDatabase('inventory_system');
$salesCollection = $database->selectCollection('sales');

// Fetch sales data
$sales = $salesCollection->find();

?>
<?php include_once('layouts/header.php'); ?>
<?php include_once('layouts/admin_menu.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <link rel="stylesheet" href="libs/css/main.css">
</head>
<body>
    <div class="container">
        <h1>Sales Report</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sale Items</th>
                    <th>Services</th>
                    <th>Total Amount</th>
                    <th>Sale Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $count = 1;
                foreach ($sales as $sale):
                    $saleItems = $sale['sale_items'] ?? [];
                    $service = $sale['service'] ?? null;
                    $serviceCost = $sale['cost'] ?? 0;
                ?>
                <tr>
                    <td><?php echo $count++; ?></td>
                    <td>
                        <?php if (!empty($saleItems)): ?>
                            <ul>
                                <?php foreach ($saleItems as $item): ?>
                                    <li>
                                        <?php
                                            echo "Product: " . htmlspecialchars($item['product_name'] ?? 'N/A') . 
                                                 ", Quantity: " . htmlspecialchars($item['quantity'] ?? 0) . 
                                                 ", Total: ₱" . htmlspecialchars($item['total'] ?? 0);
                                        ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            No products sold.
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($service): ?>
                            <?php
                                echo "Service: " . htmlspecialchars($service) . 
                                     ", Cost: ₱" . htmlspecialchars($serviceCost);
                            ?>
                        <?php else: ?>
                            No services sold.
                        <?php endif; ?>
                    </td>
                    <td>₱ <?php echo htmlspecialchars($sale['total_cost'] ?? $serviceCost); ?></td>
                    <td>
                        <?php
                        if (isset($sale['date'])) {
                            $saleDate = $sale['date']->toDateTime();
                            echo $saleDate->format('Y-m-d H:i:s');
                        } else {
                            echo 'Unknown';
                        }
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
