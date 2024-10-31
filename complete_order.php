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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $totalAmount = $_POST['total'];
    $itemIds = $_POST['items'];

    // Insert transaction into the database
    $stmt = $conn->prepare("INSERT INTO transactions (item_ids, total_amount, transaction_date) VALUES (?, ?, NOW())");
    $stmt->bind_param("sd", $itemIds, $totalAmount);

    if ($stmt->execute()) {
        // Redirect to the transaction page
        header("Location: transaction.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Close connection
$conn->close();
?>
