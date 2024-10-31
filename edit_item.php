<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "123456"; // Update your MySQL password if needed
$dbname = "loyalty_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the item ID is provided
if (isset($_GET['id'])) {
    $item_id = $_GET['id'];

    // Fetch the item details
    $stmt = $conn->prepare("SELECT * FROM items WHERE item_id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $stmt->close();

    // Check if the item exists
    if (!$item) {
        echo "Item not found!";
        exit;
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $item_name = $_POST['item_name'];
    $category = $_POST['category'];
    $price = $_POST['price'];

    // Validate form data
    if (empty($item_name) || empty($category) || empty($price)) {
        echo "All fields are required!";
        exit;
    }

    // Prepare and bind
    $stmt = $conn->prepare("UPDATE items SET item_name = ?, category = ?, price = ? WHERE item_id = ?");
    $stmt->bind_param("ssdi", $item_name, $category, $price, $item_id); // ssdi means: string, string, double, integer

    // Execute the statement
    if ($stmt->execute()) {
        echo "Item updated successfully!<br>";
        echo "<a href='view_items.php'>View Item List</a>";
    } else {
        echo "Error: " . htmlspecialchars($stmt->error);
    }

    // Close statement
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Item</h2>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="item_name" class="form-label">Item Name</label>
            <input type="text" class="form-control" id="item_name" name="item_name" value="<?php echo htmlspecialchars($item['item_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <input type="text" class="form-control" id="category" name="category" value="<?php echo htmlspecialchars($item['category']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Price</label>
            <input type="number" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($item['price']); ?>" step="0.01" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Item</button>
        <a href="view_items.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Close connection
$conn->close();
?>
