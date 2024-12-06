<?php
require '../_base.php'; // Include your database connection

// Fetch existing categories from the database
$categories = [];
try {
    $stmt = $_db->query("SELECT DISTINCT JSON_EXTRACT(category, '$[*]') AS category_list FROM products");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $decodedCategories = json_decode($row['category_list'], true);
        if (is_array($decodedCategories)) {
            $categories = array_merge($categories, $decodedCategories);
        }
    }
    $categories = array_unique($categories); // Remove duplicates
} catch (PDOException $e) {
    echo "Error fetching categories: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = $_POST['product_name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $currentStock = $_POST['current_stock'];
    $productImage = $_POST['product_image'];
    $status = $_POST['status'];

    // Handle category selection or new category addition
    $selectedCategories = isset($_POST['existing_categories']) ? $_POST['existing_categories'] : [];
    $newCategory = isset($_POST['new_category']) && !empty($_POST['new_category']) ? $_POST['new_category'] : null;

    if ($newCategory) {
        $selectedCategories[] = $newCategory; // Add new category to the list
    }

    // Encode selected categories as JSON
    $categoryJson = json_encode($selectedCategories);

    try {
        $productId = generate_unique_id('PRO', 'products', 'product_id', $_db); // Generate unique product ID

        $stmt = $_db->prepare("
            INSERT INTO products (product_id, product_name, category, price, description, current_stock, amount_sold, product_image, status)
            VALUES (?, ?, ?, ?, ?, ?, 0, ?, ?)
        ");
        $stmt->execute([$productId, $productName, $categoryJson, $price, $description, $currentStock, $productImage, $status]);

        echo "Product added successfully!";
    } catch (PDOException $e) {
        echo "Error adding product: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
</head>
<body>
    <form method="POST" action="">
        <label for="product_name">Product Name:</label>
        <input type="text" id="product_name" name="product_name" required><br><br>

        <label for="categories">Categories:</label><br>
        <select id="categories" name="existing_categories[]" multiple>
            <?php foreach ($categories as $category): ?>
                <option value="<?= htmlspecialchars($category) ?>"><?= htmlspecialchars($category) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="new_category">Add New Category:</label>
        <input type="text" id="new_category" name="new_category" placeholder="New Category"><br><br>

        <label for="price">Price:</label>
        <input type="number" id="price" name="price" step="0.01" required><br><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea><br><br>

        <label for="current_stock">Current Stock:</label>
        <input type="number" id="current_stock" name="current_stock" required><br><br>

        <label for="product_image">Product Image URL:</label>
        <input type="text" id="product_image" name="product_image" required><br><br>

        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="AVAILABLE">Available</option>
            <option value="UNAVAILABLE">Unavailable</option>
            <option value="OUT_OF_STOCK">Out of Stock</option>
        </select><br><br>

        <button type="submit">Add Product</button>
    </form>
</body>
</html>
