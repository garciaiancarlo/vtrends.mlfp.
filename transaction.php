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

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Retrieve transactions from the database
$sql = "SELECT * FROM transactions";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Records</title>
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
            transition: transform 0.3s ease;
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
        h2 {
            color: #007bff;
            text-align: center;
        }
        img {
            border-radius: 50%;
            display: block;
            margin: 0 auto;
            max-width: 100px;
        }
        /* Responsive Sidebar */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: absolute;
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar"> <!-- Added id="sidebar" -->
        <center><img src="https://scontent.fmnl4-3.fna.fbcdn.net/v/t39.30808-6/455041990_911393857674308_9135799122053590255_n.jpg?_nc_cat=110&ccb=1-7&_nc_sid=6ee11a&_nc_eui2=AeF9ohVAm_8_rrcG70V5LRPbMNMC5zspmAYw0wLnOymYBtbYmoJX0YIy3rr2JLc2ty8QWxxI6IeUvCLgzTBC4BRH&_nc_ohc=vMloPAIzT2wQ7kNvgFct2HV&_nc_zt=23&_nc_ht=scontent.fmnl4-3.fna&_nc_gid=Am4UdESzd7ysn3eJpuEGbn4&oh=00_AYC2vbIq5CSCQxb1OKx1gdUXNQp6b6QDGcqtXtTQNkya0A&oe=671E4252" alt="Loyalty Card Logo"></center>
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

    <!-- Content -->
    <div class="content">
        <h2>Transaction Records</h2>
        <div class="container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Item IDs</th>
                        <th>Total Amount</th>
                        <th>Transaction Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row["transaction_id"]) . "</td>
                                    <td>" . htmlspecialchars($row["item_ids"]) . "</td>
                                    <td>₱" . number_format($row["total_amount"], 2) . "</td>
                                    <td>" . $row["transaction_date"] . "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center'>No transactions found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <!-- Sidebar Toggle Script -->
    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("active");
        }
    </script>
</body>
</html>

<?php
// Close connection
$conn->close();
?>
