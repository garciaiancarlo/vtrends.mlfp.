<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "123456"; // Update with your MySQL password if needed
$dbname = "loyalty_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $_SESSION['username'] = $username;
        header("Location: home.html"); // Redirect to the home page
        exit();
    } else {
        echo "<script>alert('Invalid username or password'); window.location.href='index.html';</script>";
    }

    $stmt->close();
}

$conn->close();
?>
