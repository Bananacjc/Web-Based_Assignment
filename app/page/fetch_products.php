<?php
header('Content-Type: application/json');
$_db = new PDO('mysql:host=127.0.0.1;dbname=db_bananasis;charset=utf8mb4', 'root', '');

$category_name = req('category_name');
$page = req('page', 1);
$limit = 9;
$offset = ($page - 1) * $limit;

$query = "
    SELECT p.*, c.category_image 
    FROM products p
    JOIN categories c ON p.category_name = c.category_name
    WHERE p.category_name = ? 
    LIMIT $limit OFFSET $offset
";

$stm = $_db->prepare($query);
$stm->execute([$category_name]);
$products = $stm->fetchAll(PDO::FETCH_ASSOC);

$total_products_query = "SELECT COUNT(*) FROM products WHERE category_name = ?";
$total_stm = $_db->prepare($total_products_query);
$total_stm->execute([$category_name]);
$total_products = $total_stm->fetchColumn();

echo json_encode([
    'products' => $products,
    'total_products' => $total_products,
    'total_pages' => ceil($total_products / $limit)
]);
?>
