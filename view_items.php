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

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM items WHERE item_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $delete_id);
    $delete_stmt->execute();
    $delete_stmt->close();
    header("Location: view_items.php"); // Redirect to the same page to refresh
    exit();
}

// Initialize search variable
$search_name = '';
if (isset($_GET['search_name'])) {
    $search_name = $_GET['search_name'];
}

// Retrieve items from the database with optional search
$sql = "SELECT * FROM items";
if (!empty($search_name)) {
    $sql .= " WHERE item_name LIKE ? OR category LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_param = "%$search_name%"; // Add wildcards for partial matching
    $stmt->bind_param("ss", $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Items</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            background-color: #464646;
            color: white;
            min-height: 100vh;
            padding-top: 20px;
            position: fixed;
            width: 250px;
        }
        .sidebar h5 {
            color: #f1f1f1;
            text-align: center;
            font-weight: bold;
        }
        .sidebar a {
            color: white;
            display: block;
            padding: 12px 20px;
            text-decoration: none;
            font-size: 1rem;
            background-color: #313131; /* Button background color */
            margin: 10px;
            border-radius: 8px; /* Rounded corners */
            transition: background-color 0.3s, transform 0.2s; /* Smooth transitions */
        }
        .sidebar a:hover {
            background-color: #313131; /* Hover color */
            color: white;
            transform: translateY(-2px); /* Slight lift effect */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Subtle shadow on hover */
        }
        .content {
            margin-left: 270px;
            padding: 20px;
        }
        h2, h3 {
            color: #007bff;
            text-align: center;
        }
        img {
            border-radius: 50%;
            display: block;
            margin: 0 auto;
            max-width: 100px;
        }
        /* Add this CSS for .active */
        .sidebar.active {
            transform: translateX(0);
        }
        /* Adjust sidebar initial state for mobile */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease; /* Smooth transition */
            }
            .sidebar.active {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar"> <!-- Added id="sidebar" here -->
        <center><img src="https://scontent.fmnl4-3.fna.fbcdn.net/v/t39.30808-6/455041990_911393857674308_9135799122053590255_n.jpg?_nc_cat=110&ccb=1-7&_nc_sid=6ee11a&_nc_eui2=AeF9ohVAm_8_rrcG70V5LRPbMNMC5zspmAYw0wLnOymYBtbYmoJX0YIy3rr2JLc2ty8QWxxI6IeUvCLgzTBC4BRH&_nc_ohc=vMloPAIzT2wQ7kNvgFct2HV&_nc_zt=23&_nc_ht=scontent.fmnl4-3.fna&_nc_gid=Am4UdESzd7ysn3eJpuEGbn4&oh=00_AYC2vbIq5CSCQxb1OKx1gdUXNQp6b6QDGcqtXtTQNkya0A&oe=671E4252" 
        alt="Loyalty Card Logo"></center>
        <h5>Vtrends.mlfp</h5>
        <a href="home.html">Home</a>
        <a href="loyalty_card.php">Loyalty Card</a>
        <a href="category.html">Category (Add Item)</a>
        <a href="view_items.php">View Items</a>
        <a href="transaction.php">Transaction Records</a>
        <br>
        <br>
        <br>
        <br>
        <br>
        <a href="index.php">Logout</a>
    </div>

    <!-- Toggle Button for Sidebar -->
    <button class="btn btn-primary d-md-none m-3" onclick="toggleSidebar()">☰ Menu</button>

    <!-- Main Content -->
    <div class="content">
        <h2>Item List</h2>

        <!-- Search Form -->
        <form method="GET" class="mb-3">
            <div class="input-group">
                <input type="text" name="search_name" class="form-control" placeholder="Search by item name or category" value="<?php echo htmlspecialchars($search_name); ?>">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form>

        <form action="order_summary.php" method="POST"> <!-- Form to submit selected items -->
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Checkout</th>
                        <th>Item ID</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td><input type='checkbox' name='selected_items[]' value='" . $row["item_id"] . "'></td>
                                    <td>" . $row["item_id"] . "</td>
                                    <td>" . htmlspecialchars($row["item_name"]) . "</td>
                                    <td>" . htmlspecialchars($row["category"]) . "</td>
                                    <td>₱" . number_format($row["price"], 2) . "</td>
                                    <td>
                                        <a href='edit_item.php?id=" . $row["item_id"] . "' class='btn btn-warning btn-sm'>Edit</a>
                                        <a href='view_items.php?delete_id=" . $row["item_id"] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this item?\");'>Delete</a>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center'>No items found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Checkout Button -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-success">Checkout</button> <!-- Submit button to process selected items -->
            </div>
        </form>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>

<!-- Sidebar Toggle Script -->
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById("sidebar");
        sidebar.classList.toggle("active"); // Toggles the active class on the sidebar
    }
</script>

<?php
// Close connection
$conn->close();
?>
