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

// Fetch orders and inventory data
$sql = "SELECT o.ORDER_ID, o.PRODUCT_ID, o.PRODUCT_NAME, o.QUANTITY, o.ORDER_DATE, i.LEAD_TIME 
        FROM orders o
        LEFT JOIN inventory i ON o.PRODUCT_ID = i.PRODUCT_ID";
$result = $conn->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($data);

$conn->close();
?>
