<?php
header('Content-Type: application/json');

try {
    // Database connection
    $_db = new PDO('mysql:host=127.0.0.1;dbname=db_bananasis;charset=utf8mb4', 'root', '');
    
    $productId = $_GET['product_id'] ?? null;

    if (!$productId) {
        echo json_encode(['error' => 'Product ID is required.']);
        exit;
    }

    // Fetch product details
    $stmt = $_db->prepare("
        SELECT 
            p.product_id, 
            p.product_name, 
            p.product_image, 
            p.price, 
            p.description, 
            COALESCE(AVG(r.rating), 0) AS avg_rating, 
            COUNT(r.rating) AS review_count,
            (SELECT COUNT(*) 
             FROM orders o 
             WHERE JSON_CONTAINS(o.order_items, JSON_OBJECT('product_id', p.product_id))
            ) AS amount_sold
        FROM products p
        LEFT JOIN reviews r ON p.product_id = r.product_id
        WHERE p.product_id = :productId
        GROUP BY p.product_id
    ");
    $stmt->execute(['productId' => $productId]);

    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo json_encode(['error' => 'Product not found.']);
        exit;
    }

    // Fetch comments
    $stmt = $_db->prepare("
        SELECT 
            c.username, 
            c.profile_image, 
            r.rating, 
            r.comment, 
            r.review_image, 
            r.comment_date_time
        FROM reviews r
        JOIN customers c ON r.customer_id = c.customer_id
        WHERE r.product_id = :productId
        ORDER BY r.comment_date_time DESC
    ");
    $stmt->execute(['productId' => $productId]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the result
    echo json_encode(['product' => $product, 'comments' => $comments]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error fetching product details: ' . $e->getMessage()]);
}
