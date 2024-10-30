<?php
  $page_title = 'All Product';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
 /* 
  // Read the JSON input from the POST request
  $data = json_decode(file_get_contents("php://input"), true);

  if (isset($data['review']) && isset($data['sentiment'])) {
      $review = $conn->real_escape_string($data['review']);
      $sentiment = $conn->real_escape_string($data['sentiment']);

      // Insert into the database
      $sql = "INSERT INTO reviews (review, sentiment) VALUES ('$review', '$sentiment')";

      if ($conn->query($sql) === TRUE) {
          echo json_encode(["message" => "Data stored successfully"]);
      } else {
          echo json_encode(["error" => "Error storing data: " . $conn->error]);
      }
  } else {
      echo json_encode(["error" => "Invalid data"]);
  }

  // Fetch reviews and sentiments from the database for display
  $reviews = join_reviews_table(); // Fetch product information if needed
  $result = $conn->query("SELECT * FROM reviews");

?>

<?php include_once('layouts/header.php'); ?>

<div class="panel-body">
  <table class="table table-bordered">
    <thead>
      <tr>
        <th class="text-center" style="width: 50px;">#</th>
        <th>Reviews</th>
        <th>Sentiment</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td class="text-center"><?php echo count_id(); ?></td>
            <td class="text-center"><?php echo remove_junk($row['review']); ?></td>
            <td class="text-center"><?php echo remove_junk($row['sentiment']); ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="3" class="text-center">No reviews found</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include_once('layouts/footer.php'); ?>


  $page_title = 'All Product';
  require_once('includes/load.php');
  
  // Ensure $conn is initialized (this should be in load.php, but double-check)
  if (!isset($conn)) {
      $conn = new mysqli('localhost', 'root', '', 'inventory_system'); // Adjust credentials
      if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
      }
  }
  
  // Check if user has permission to view this page
  page_require_level(2);
  
  // Read the JSON input from the POST request
  $data = json_decode(file_get_contents("php://input"), true);

  // Check if review and sentiment data are present and valid
  if (isset($data['review']) && isset($data['sentiment'])) {
      $review = $conn->real_escape_string($data['review']);
      $sentiment = $conn->real_escape_string($data['sentiment']);

      // Insert into the database
      $sql = "INSERT INTO reviews (review, sentiment) VALUES ('$review', '$sentiment')";

      if ($conn->query($sql) === TRUE) {
          echo json_encode(["message" => "Data stored successfully"]);
      } else {
          echo json_encode(["error" => "Error storing data: " . $conn->error]);
      }
  } else {
      echo json_encode(["error" => "Invalid data"]);
  }

  // Fetch reviews and sentiments from the database for display
  $result = $conn->query("SELECT * FROM reviews");

?>

<?php include_once('layouts/header.php'); ?>

<div class="panel-body">
  <table class="table table-bordered">
    <thead>
      <tr>
        <th class="text-center" style="width: 50px;">#</th>
        <th>Reviews</th>
        <th>Sentiment</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td class="text-center"><?php echo count_id(); ?></td>
            <td class="text-center"><?php echo remove_junk($row['review']); ?></td>
            <td class="text-center"><?php echo remove_junk($row['sentiment']); ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="3" class="text-center">No reviews found</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include_once('layouts/footer.php'); ?>
*/

/*
// Receive the JSON data from JavaScript
$data = json_decode(file_get_contents('php://input'), true);
$review = $data['review'];
$sentiment = $data['data.sentiment'];

// Connect to the database
$servername = "localhost";
$dbname = "inventory_system";
$usernameDB = "root";
$passwordDB = "";

// Create a connection
$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert the review and its sentiment into the database
$stmt = $conn->prepare("INSERT INTO reviews (review, sentiment) VALUES (?, ?)");
$stmt->bind_param("sss", $review, $sentiment);

if ($stmt->execute()) {
    echo json_encode(["message" => "Review and sentiment stored successfully"]);
} else {
    echo json_encode(["message" => "Error: " . $stmt->error]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
*/


/*
// Database connection
$host = 'localhost'; // Or your database host
$dbname = 'inventory_system'; // Your database name
$username = 'root'; // Your database username
$password = ''; // Your database password

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the POST data from the request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if 'review' and 'sentiment' are set and not null
    if (isset($data['review']) && isset($data['sentiment'])) {
        $review = $data['review'];
        $sentiment = $data['sentiment'];

        // SQL Insert query to store the review and sentiment
        $stmt = $conn->prepare("INSERT INTO reviews (review_text, sentiment) VALUES (:review, :sentiment)");
        $stmt->bindParam(':review', $review);
        $stmt->bindParam(':sentiment', $sentiment);

        // Execute the query
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Data inserted successfully']);
        } else {
            echo json_encode(['message' => 'Failed to insert data']);
        }
    } else {
        // If data['review'] or data['sentiment'] is not set, return an error
        echo json_encode(['error' => 'Review or sentiment not provided']);
    }

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

$conn = null;
*/




// Get the POST data
$review = $_POST['review'];
$sentiment = $_POST['sentiment'];

// SQL query to insert the review and sentiment
$sql = "INSERT INTO reviews_table (review, sentiment) VALUES ('$review', '$sentiment')";

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>