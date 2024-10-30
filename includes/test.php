<?php
require '../vendor/autoload.php';
   // connect to mongodb
   $client = new MongoDB\Client('mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0');
   echo "Connection to database successfully";
	
   // select a database
   $database = $client->selectDatabase('inventory_system');
   $collection = $database->selectCollection('product');
	
   $document = array( 
      "name" => "Demo Product", 
      "quantity" => "48", 
      "buy_price" => "100",
      "eoq" => "500",
      "categorie_id" => "1",
      "media_id" => "0",
      "date" => "2021-04-04 16:45:51",
      "restock_threshhold" => "10",
      "product_type" => "slow-moving",
      "last_restock_date" => "2024-10-16"
   );
	
   $collection->insertOne($document);
   echo "Document inserted successfully";
?>


