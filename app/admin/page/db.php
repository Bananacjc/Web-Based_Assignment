<?php
$servername = "localhost"; // Your database server (localhost for local development)
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "db_bananasis"; // Your database name

try {
    // Create connection using PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If there is an error, display the message
    die("Connection failed: " . $e->getMessage());
}
?>
