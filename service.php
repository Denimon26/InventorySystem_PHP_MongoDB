<?php
$page_title = 'Manage Services';
require_once('includes/load.php');
require 'vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

// Connect to MongoDB
$uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
$client = new Client($uri);
$database = $client->selectDatabase('inventory_system');
$serviceCollection = $database->selectCollection('service');

$msg = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_service'])) {
        // Add new service
        $serviceName = trim($_POST['service_name'] ?? '');

        if (!empty($serviceName)) {
            $serviceCollection->insertOne([
                'name' => $serviceName,
                'date_added' => new MongoDB\BSON\UTCDateTime(),
            ]);
            $msg = "Service '{$serviceName}' added successfully!";
        } else {
            $msg = 'Service name cannot be empty.';
        }
    } elseif (isset($_POST['edit_service'])) {
        // Edit existing service
        $serviceId = $_POST['service_id'];
        $newServiceName = trim($_POST['new_service_name'] ?? '');

        if (!empty($newServiceName)) {
            $serviceCollection->updateOne(
                ['_id' => new ObjectId($serviceId)],
                ['$set' => ['name' => $newServiceName]]
            );
            $msg = "Service updated successfully!";
        } else {
            $msg = 'New service name cannot be empty.';
        }
    } elseif (isset($_POST['delete_service'])) {
        // Delete service
        $serviceId = $_POST['service_id'];
        $serviceCollection->deleteOne(['_id' => new ObjectId($serviceId)]);
        $msg = "Service deleted successfully!";
    }
}

// Fetch all services
$services = $serviceCollection->find();
?>
<?php include_once('layouts/header.php'); ?>
<?php include_once('layouts/admin_menu.php'); ?>

<h2>Manage Services</h2>

<div class="col-md-12">
    <?php if (strpos($msg, 'successfully') !== false): ?>
        <div class="alert alert-success" role="alert" style="background-color: green; font-weight: bold;">
            <?php echo $msg; ?>
        </div>
    <?php elseif (!empty($msg)): ?>
        <div class="alert alert-danger" role="alert" style="background-color: red; font-weight: bold;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>
</div>


<!-- Add New Service Form -->
<form method="POST" action="service.php">
    <div class="form-group">
        <label for="service_name">Service Name</label>
        <input type="text" name="service_name" id="service_name" class="form-control" placeholder="Enter service name" required>
    </div>
    <button type="submit" name="add_service" class="btn btn-primary">Add Service</button>
</form>

<hr>

<!-- Display Services -->
<h3>Existing Services</h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Service Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($services as $service): ?>
            <tr>
                <td><?php echo htmlspecialchars($service['name']); ?></td>
                <td>
                    <!-- Edit Form -->
                    <form method="POST" action="service.php" style="display:inline-block;">
                        <input type="hidden" name="service_id" value="<?php echo $service['_id']; ?>">
                        <input type="text" name="new_service_name" placeholder="New Name" class="form-control" style="display:inline-block; width:auto;">
                        <button type="submit" name="edit_service" class="btn btn-warning btn-sm">Edit</button>
                    </form>
                    <!-- Delete Form -->
                    <form method="POST" action="service.php" style="display:inline-block;">
                        <input type="hidden" name="service_id" value="<?php echo $service['_id']; ?>">
                        <button type="submit" name="delete_service" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php //include_once('layouts/footer.php'); ?>
