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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['selected_items'])) {
    $selected_ids = $_POST['selected_items'];

    // Fetch selected items from the database
    $placeholders = implode(',', array_fill(0, count($selected_ids), '?'));
    $sql = "SELECT * FROM items WHERE item_id IN ($placeholders)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('i', count($selected_ids)), ...$selected_ids); // Bind parameters
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    header("Location: view_items.php"); // Redirect if no items were selected
    exit();
}

$totalAmount = 0; // Initialize total amount
$itemsData = [];

// Prepare to store item details for insertion later
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $itemsData[] = $row; // Collect items for later use
        $totalAmount += $row["price"]; // Add to total amount
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .content {
            padding: 20px;
        }
        h2 {
            color: #007bff;
            text-align: center;
        }
        #order-summary {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
    <script>
        function applyDiscount(discount) {
            let totalElement = document.getElementById('totalAmount');
            let originalTotal = parseFloat(totalElement.dataset.original);
            let discountedTotal = originalTotal * (1 - discount);

            totalElement.innerText = `₱${discountedTotal.toFixed(2)}`;

            // Update the hidden input for discounted total
            document.getElementById('discountedTotal').value = discountedTotal.toFixed(2);
        }
    </script>
</head>
<body>

<div class="content">
    <h2>Order Summary</h2>
    <div id="order-summary">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Item ID</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (empty($itemsData)) {
                    echo "<tr><td colspan='4' class='text-center'>No items found</td></tr>";
                } else {
                    foreach ($itemsData as $item) {
                        echo "<tr>
                                <td>" . htmlspecialchars($item["item_id"]) . "</td>
                                <td>" . htmlspecialchars($item["item_name"]) . "</td>
                                <td>" . htmlspecialchars($item["category"]) . "</td>
                                <td>₱" . number_format($item["price"], 2) . "</td>
                              </tr>";
                    }
                }
                ?>
            </tbody>
        </table>
        <h4 class="text-right" id="totalAmount" data-original="<?php echo $totalAmount; ?>">Total: ₱<?php echo number_format($totalAmount, 2); ?></h4>

        <div class="text-center mt-4">
            <button class="btn btn-warning" onclick="applyDiscount(0.10)">Apply 10% Discount</button>
            <button class="btn btn-danger" onclick="applyDiscount(0.20)">Apply 20% Discount</button>
        </div>

        <div class="text-center mt-4">
            <form action="complete_order.php" method="POST">
                <input type="hidden" name="total" id="discountedTotal" value="<?php echo $totalAmount; ?>">
                <input type="hidden" name="items" value="<?php echo implode(',', array_column($itemsData, 'item_id')); ?>">
                <button type="submit" class="btn btn-success">Complete Order</button>
            </form>
        </div>
    </div>
</div>

<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Close connection
$conn->close();
?>
