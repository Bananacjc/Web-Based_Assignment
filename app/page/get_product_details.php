<?php
header('Content-Type: application/json');

// Mock database connection
try {
    $db = new PDO('mysql:host=127.0.0.1;dbname=db_bananasis;charset=utf8mb4', 'root', '');

    $productId = $_GET['product_id'] ?? null;
    if (!$productId) {
        echo json_encode(['error' => 'Product ID is required']);
        exit;
    }

    $stmt = $db->prepare("
        SELECT 
            p.product_id,
            p.product_name,
            p.category_name,
            p.price,
            p.description,
            p.product_image,
            p.amount_sold,
            COALESCE(AVG(r.rating), 0) as avg_rating,
            COUNT(r.rating) as review_count
        FROM products p
        LEFT JOIN reviews r ON p.product_id = r.product_id
        WHERE p.product_id = ?
        GROUP BY p.product_id
    ");
    $stmt->execute([$productId]);
    $productDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$productDetails) {
        echo json_encode(['error' => 'Product not found']);
        exit;
    }

    echo json_encode([
        'product' => $productDetails,
        'comments' => []
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Error fetching product details: ' . $e->getMessage()]);
}
