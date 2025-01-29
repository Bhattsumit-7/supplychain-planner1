<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "supply_chain";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// File upload and data processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ordersFile = $_FILES['orders']['tmp_name'];
    $inventoryFile = $_FILES['inventory']['tmp_name'];

    // Normalize file names (trim spaces and convert to lowercase)
    $ordersFileName = strtolower(trim($_FILES['orders']['name']));
    $inventoryFileName = strtolower(trim($_FILES['inventory']['name']));

    // Validate file names
    if ($ordersFileName !== 'orders.csv' || $inventoryFileName !== 'inventory.csv') {
        die("Please upload the correct files: orders.csv and inventory.csv.");
    }

    // Handle Orders File
    if (isset($_FILES['orders']) && $_FILES['orders']['error'] == 0) {
        $ordersData = array_map('str_getcsv', file($ordersFile));
        $headers = array_shift($ordersData); // Remove header row

        // Insert orders into database
        $stmt = $conn->prepare("INSERT INTO orders (PRODUCT_ID, PRODUCT_NAME, QUANTITY, ORDER_DATE) VALUES (?, ?, ?, ?)");
        foreach ($ordersData as $row) {
            $stmt->bind_param("ssis", $row[0], $row[1], $row[2], $row[3]);
            $stmt->execute();
        }
        $stmt->close();
    }

    // Handle Inventory File
    if (isset($_FILES['inventory']) && $_FILES['inventory']['error'] == 0) {
        $inventoryData = array_map('str_getcsv', file($inventoryFile));
        $headers = array_shift($inventoryData); // Remove header row

        // Insert or update inventory into database
        $stmt = $conn->prepare("INSERT INTO inventory (PRODUCT_ID, LEAD_TIME) VALUES (?, ?) 
                                ON DUPLICATE KEY UPDATE LEAD_TIME = VALUES(LEAD_TIME)");
        foreach ($inventoryData as $row) {
            $stmt->bind_param("si", $row[0], $row[1]);
            $stmt->execute();
        }
        $stmt->close();
    }

    echo "Files uploaded and processed successfully!";
    $conn->close();
}
?>