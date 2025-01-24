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
    // Handle Orders File
    if (isset($_FILES['orders']) && $_FILES['orders']['error'] == 0) {
        $ordersFile = $_FILES['orders']['tmp_name'];
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
        $inventoryFile = $_FILES['inventory']['tmp_name'];
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

    // Your existing code for processing uploads and inserting data into the database

// After successful file processing and database insertion
header("Location: index.html"); // Replace with your frontend file path
exit();
}

?>
