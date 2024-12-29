<?php
function req($key, $value = null)
{
    $value = $_REQUEST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

header('Content-Type: application/json');
$_db = new PDO('mysql:host=127.0.0.1;dbname=db_bananasis;charset=utf8mb4', 'root', '');

$category_name = req('category_name');
$page = req('page', 1);
$limit = 9; // 9 products per page
$offset = ($page - 1) * $limit;

// Total products for the category
$total_products_query = "
    SELECT COUNT(*) 
    FROM products 
    WHERE category_name = ? AND status IN ('AVAILABLE', 'OUT_OF_STOCK')
";
$total_stm = $_db->prepare($total_products_query);
$total_stm->execute([$category_name]);
$total_products = $total_stm->fetchColumn();

// Fetch paginated products
$product_query = "
    SELECT p.product_id, p.product_name, p.category_name, p.price, p.description, 
           p.product_image, p.status, COALESCE(AVG(r.rating), 0) AS avg_rating, COUNT(r.rating) AS review_count
    FROM products p
    LEFT JOIN reviews r ON p.product_id = r.product_id
    WHERE p.category_name = ? AND p.status IN ('AVAILABLE', 'OUT_OF_STOCK')
    GROUP BY p.product_id
    LIMIT $limit OFFSET $offset
";
$product_stm = $_db->prepare($product_query);
$product_stm->execute([$category_name]);
$products = $product_stm->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'products' => $products,
    'total_pages' => ceil($total_products / $limit),
]);
?>
