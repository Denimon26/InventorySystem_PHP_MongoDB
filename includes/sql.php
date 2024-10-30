<?php
require_once('includes/load.php');

/*--------------------------------------------------------------*/
/* Function for finding all database table rows by collection name */
/*--------------------------------------------------------------*/
function find_all($collection) {
    global $db;
    return $db->$collection->find()->toArray(); // MongoDB query to fetch all documents
}

/*--------------------------------------------------------------*/
/* Function for finding data from collection by id */
/*--------------------------------------------------------------*/
function find_by_id($collection, $id) {
    global $db;
    $id = new MongoDB\BSON\ObjectId($id); // MongoDB uses ObjectId for unique document IDs
    return $db->$collection->findOne(['_id' => $id]);
}

/*--------------------------------------------------------------*/
/* Function for deleting data from collection by id */
/*--------------------------------------------------------------*/
function delete_by_id($collection, $id) {
    global $db;
    $id = new MongoDB\BSON\ObjectId($id);
    $result = $db->$collection->deleteOne(['_id' => $id]);
    return ($result->getDeletedCount() === 1) ? true : false;
}

/*--------------------------------------------------------------*/
/* Function for counting documents by collection name */
/*--------------------------------------------------------------*/
function count_by_id($collection) {
    global $db;
    return $db->$collection->countDocuments(); // MongoDB count query
}

/*--------------------------------------------------------------*/
/* Function to authenticate login from the login form */
/*--------------------------------------------------------------*/
function authenticate($username = '', $password = '') {
    global $db;
    $user = $db->users->findOne(['username' => $username]);
    if ($user && password_verify($password, $user['password'])) {
        return $user['_id'];
    }
    return false;
}

/*--------------------------------------------------------------*/
/* Function for getting the current user by session id */
/*--------------------------------------------------------------*/
function current_user() {
    global $db;
    if (isset($_SESSION['_id'])) {
        $user_id = new MongoDB\BSON\ObjectId($_SESSION['_id']);
        return find_by_id('users', $user_id);
    }
    return null;
}

/*--------------------------------------------------------------*/
/* Function for page requirement */
/*--------------------------------------------------------------*/
function page_require_level($required_level) {
    // Check if user is logged in and has permission to access the page
    if (!$current_user = current_user()) {
        // If the user is not logged in, redirect to login page
        redirect('login.php', false);
    }
    // Check the user's level against the required level for the page
    if ($current_user['user_level'] <= (int)$required_level) {
        return true;
    } else {
        // If the user does not have permission, redirect to the home page
        redirect('home.php', false);
    }
}

/*--------------------------------------------------------------*/
/* Function for finding recent products added */
/*--------------------------------------------------------------*/
function find_recent_product_added($limit) {
    global $db;
    return $db->products->find([], ['sort' => ['date' => -1], 'limit' => (int)$limit])->toArray();
}

/*--------------------------------------------------------------*/
/* Function for finding low-quantity products */
/*--------------------------------------------------------------*/
function find_products_by_quantity($low_limit) {
    global $db;
    return $db->products->find(['quantity' => ['$lt' => (int)$low_limit]], ['sort' => ['quantity' => 1]])->toArray();
}
?>
