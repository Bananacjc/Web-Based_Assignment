<?php
ob_start();
require_once '../_base.php';

if (is_post()) {
    // Get POST data
    $productID = post('product_id');
    $action = post('action');
    $quantity = post('quantity'); // Ensure quantity is fetched from POST data

    if ($productID && $action && isset($quantity)) {

        // Get the old cart
        $cart = get_cart();
        $currentQty = $cart[$productID];

        // Get the product price from the database
        $stmt = $_db->prepare('SELECT price FROM products WHERE product_id = ?');
        $stmt->execute([$productID]);
        $product = $stmt->fetch();

        if ($product) {
            
            if ($action === 'increase') {
                $currentQty += 1;
            } else if ($action === 'decrease') {
                if ($currentQty > 1) {
                    $currentQty -= 1;
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Cannot be zero'
                    ]);
                }
            }

            // Calculate new quantity and subtotal for the product
            $newQuantity = $currentQty; // Ensure the quantity exists in the cart
            update_cart($productID, $newQuantity);
            $newSubtotal = $newQuantity * $product->price;

            // Calculate total cart count (sum of all product quantities)
            $cartCount = array_sum($cart);

            // Return the updated cart data as a JSON response
            ob_end_clean();
            echo json_encode([
                'success' => true,
                'newQuantity' => $newQuantity,
                'newSubtotal' => $newSubtotal,
                'cartCount' => $cartCount
            ]);
            
        } else {
            // Product not found in the database, return error
            ob_end_clean();
            echo json_encode([
                'success' => false,
                'message' => 'Product not found'
            ]);
        }
    } else {
        // Missing required POST data
        ob_end_clean();
        echo json_encode([
            'success' => false,
            'message' => 'Invalid product ID, action, or quantity'
        ]);
    }
}

exit()
?>