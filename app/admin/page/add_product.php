<?php
session_start();
include '_base.php';
/*
if (!isset($_SESSION['employee_id'])) {
    header('Location: login.php');
    exit();
}

// Only Managers can add products
if (!checkUserRole('MANAGER')) {
    echo "You do not have permission to access this page.";
    exit();
}
*/
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product_name'];
    $category = json_encode($_POST['category']);  // Assuming categories are passed as an array
    $price = $_POST['price'];
    $description = $_POST['description'];
    $current_stock = $_POST['current_stock'];
    $amount_sold = $_POST['amount_sold'];
    $product_image = $_POST['product_image'];
    $status = $_POST['status'];

    $query = "INSERT INTO products (product_name, category, price, description, current_stock, amount_sold, product_image, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssdsdiss", $product_name, $category, $price, $description, $current_stock, $amount_sold, $product_image, $status);
    $stmt->execute();
    echo "Product added successfully!";
}
?>

<form method="POST" action="add_product.php">
    <input type="text" name="product_name" required>
    <textarea name="category[]" required></textarea>  <!-- Example for multiple categories -->
    <input type="number" name="price" required>
    <input type="text" name="description" required>
    <input type="number" name="current_stock" required>
    <input type="number" name="amount_sold" required>
    <input type="text" name="product_image" required>
    <select name="status">
        <option value="AVAILABLE">Available</option>
        <option value="UNAVAILABLE">Unavailable</option>
        <option value="OUT_OF_STOCK">Out of Stock</option>
    </select>
    <button type="submit">Add Product</button>
</form>
