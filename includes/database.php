<?php
require_once(LIB_PATH_INC.DS."config.php");
require 'vendor/autoload.php'; // Make sure to include Composer's autoload

use MongoDB\Client;

class MongoDB_DB {

    private $client;
    private $db;
    private $collection;

    function __construct() {
        $this->db_connect();
    }

    /*--------------------------------------------------------------*/
    /* Function for Open MongoDB connection
    /*--------------------------------------------------------------*/
    public function db_connect() {
        try {
            // Create MongoDB client connection
            $this->client = new Client('mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0');
            // Select the database (replace 'your_database_name' with your actual database)
            $this->db = $this->client->selectDatabase(DB_NAME); 
        } catch (Exception $e) {
            die("Failed to connect to MongoDB: " . $e->getMessage());
        }
    }

    /*--------------------------------------------------------------*/
    /* Function for Close MongoDB connection
    /*--------------------------------------------------------------*/
    public function db_disconnect() {
        // MongoDB connections are typically managed by the client, no need to manually close.
        $this->client = null;
    }

    /*--------------------------------------------------------------*/
    /* Function to select the collection for queries
    /*--------------------------------------------------------------*/
    public function select_collection($collection_name) {
        $this->collection = $this->db->selectCollection($collection_name);
    }

    /*--------------------------------------------------------------*/
    /* Function for MongoDB query (Find documents)
    /*--------------------------------------------------------------*/
    public function query($filter = [], $options = []) {
        if (is_null($this->collection)) {
            die("No collection selected.");
        }
        return $this->collection->find($filter, $options);
    }

    /*--------------------------------------------------------------*/
    /* Function for MongoDB Insert (Insert document)
    /*--------------------------------------------------------------*/
    public function insert($document) {
        if (is_null($this->collection)) {
            die("No collection selected.");
        }
        $result = $this->collection->insertOne($document);
        return $result->getInsertedId(); // Return the ID of the inserted document
    }

    /*--------------------------------------------------------------*/
    /* Function for MongoDB Update (Update document)
    /*--------------------------------------------------------------*/
    public function update($filter, $new_data) {
        if (is_null($this->collection)) {
            die("No collection selected.");
        }
        $result = $this->collection->updateOne($filter, ['$set' => $new_data]);
        return $result->getModifiedCount(); // Return the number of modified documents
    }

    /*--------------------------------------------------------------*/
    /* Function for MongoDB Delete (Delete document)
    /*--------------------------------------------------------------*/
    public function delete($filter) {
        if (is_null($this->collection)) {
            die("No collection selected.");
        }
        $result = $this->collection->deleteOne($filter);
        return $result->getDeletedCount(); // Return the number of deleted documents
    }

    /*--------------------------------------------------------------*/
    /* Helper Functions for Query Results
    /*--------------------------------------------------------------*/
    public function fetch_all($cursor) {
        $results = [];
        foreach ($cursor as $doc) {
            $results[] = $doc;
        }
        return $results;
    }
    
    public function fetch_one($cursor) {
        return $cursor->toArray()[0] ?? null;
    }

}

$db = new MongoDB_DB();
$db->select_collection('products'); // You must select a collection before querying
?>

