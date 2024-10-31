<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "123456"; // Update your MySQL password if needed
$dbname = "loyalty_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add Customer
if (isset($_POST['add_customer'])) {
    $customer_name = $_POST['customer_name'];
    $customer_email = $_POST['customer_email'];
    $points = 0; // Initialize with zero points
    $dateofpoints = $_POST['dateofpoints']; // Get the date of points from the form

    $stmt = $conn->prepare("INSERT INTO loyalty_cards (customer_name, customer_email, points, dateofpoints) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $customer_name, $customer_email, $points, $dateofpoints);
    
    if ($stmt->execute()) {
        $message = "New customer added successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Update Points
if (isset($_POST['update_points'])) {
    $customer_id = $_POST['customer_id'];
    $new_points = $_POST['points'];
    $dateofpoints = $_POST['dateofpoints']; // Get the date of points from the form

    $stmt = $conn->prepare("UPDATE loyalty_cards SET points = ?, dateofpoints = ? WHERE id = ?");
    $stmt->bind_param("isi", $new_points, $dateofpoints, $customer_id);
    
    if ($stmt->execute()) {
        $message = "Points updated successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Delete Customer
if (isset($_GET['delete_id'])) {
    $customer_id = $_GET['delete_id'];
    
    $stmt = $conn->prepare("DELETE FROM loyalty_cards WHERE id = ?");
    $stmt->bind_param("i", $customer_id);
    
    if ($stmt->execute()) {
        $message = "Customer deleted successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Initialize search variable
$search_name = '';
if (isset($_GET['search_name'])) {
    $search_name = $_GET['search_name'];
}

// Retrieve All Customers with optional search
$sql = "SELECT * FROM loyalty_cards";
if (!empty($search_name)) {
    $sql .= " WHERE customer_name LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_param = "%$search_name%"; // Add wildcards for partial matching
    $stmt->bind_param("s", $search_param);
} else {
    $stmt = $conn->prepare($sql);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vtrends.mlfp</title>
    <!-- Include Bootstrap CSS -->
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
            background-color: #313131;
            margin: 10px;
            border-radius: 8px;
            transition: background-color 0.3s, transform 0.2s;
        }
        .sidebar a:hover {
            background-color: #313131;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .content {
            margin-left: 270px;
            padding: 20px;
        }
        .card-header {
            background-color: #464646;
            color: white;
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
        @media (max-width: 768px) {
            .sidebar {
                position: absolute;
                width: 100%;
                left: -100%;
                transition: left 0.3s ease;
            }
            .sidebar.active {
                left: 0;
            }
            .content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
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

<!-- Toggle Button for Sidebar (only visible on smaller screens) -->
<button class="btn btn-primary d-md-none m-3" onclick="toggleSidebar()">☰ Menu</button>

<!-- Main Content -->
<div class="content">
    <center><img src="https://scontent.fmnl4-3.fna.fbcdn.net/v/t39.30808-6/455041990_911393857674308_9135799122053590255_n.jpg?_nc_cat=110&ccb=1-7&_nc_sid=6ee11a&_nc_eui2=AeF9ohVAm_8_rrcG70V5LRPbMNMC5zspmAYw0wLnOymYBtbYmoJX0YIy3rr2JLc2ty8QWxxI6IeUvCLgzTBC4BRH&_nc_ohc=vMloPAIzT2wQ7kNvgFct2HV&_nc_zt=23&_nc_ht=scontent.fmnl4-3.fna&_nc_gid=Am4UdESzd7ysn3eJpuEGbn4&oh=00_AYC2vbIq5CSCQxb1OKx1gdUXNQp6b6QDGcqtXtTQNkya0A&oe=671E4252" 
    alt="Loyalty Card Logo"></center>
    <h2>Loyalty Card System</h2>
    <h3>Vtrends.mlfp</h3>

    <!-- Success/Error Message -->
    <?php if (isset($message)) { ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php } ?>

    <!-- Search Form -->
    <div class="mb-4">
        <form method="GET" action="">
            <div class="input-group">
                <input type="text" class="form-control" name="search_name" placeholder="Search by Customer Name" value="<?php echo htmlspecialchars($search_name); ?>">
                <button type="submit" class="btn btn-secondary">Search</button>
            </div>
        </form>
    </div>

    <!-- Add Customer Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h4>Add New Customer</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="customer_name" class="form-label">Customer Name</label>
                    <input type="text" class="form-control" name="customer_name" required>
                </div>
                <div class="mb-3">
                    <label for="customer_email" class="form-label">Customer Email</label>
                    <input type="email" class="form-control" name="customer_email" required>
                </div>
                <div class="mb-3">
                    <label for="dateofpoints" class="form-label">Date of Points</label>
                    <input type="date" class="form-control" name="dateofpoints" required>
                </div>
                <button type="submit" name="add_customer" class="btn btn-primary">Add Customer</button>
            </form>
        </div>
    </div>

    <!-- Customer List Table -->
    <div class="card">
        <div class="card-header">
            <h4>Customer List</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Points</th>
                            <th>Date of Points</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['customer_name']}</td>
                                    <td>{$row['customer_email']}</td>
                                    <td>{$row['points']}</td>
                                    <td>{$row['dateofpoints']}</td>
                                    <td>
                                        <form method='POST' action='' class='d-inline'>
                                            <input type='hidden' name='customer_id' value='{$row['id']}'>
                                            <input type='number' name='points' placeholder='Edit points' class='form-control d-inline' style='width: 100px;'>
                                            <input type='date' name='dateofpoints' class='form-control d-inline' style='width: 120px;'>
                                            <button type='submit' name='update_points' class='btn btn-warning btn-sm'>Edit</button>
                                        </form>
                                        <a href='?delete_id={$row['id']}' class='btn btn-danger btn-sm'>Delete</a>
                                    </td>
                                  </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No customers found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Sidebar Toggle Script -->
<script>
function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("active");
}
</script>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
