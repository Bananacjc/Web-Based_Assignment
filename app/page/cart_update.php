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
        //$currentQty = $cart[$productID];

        // Get the product price from the database
        $stmt = $_db->prepare('SELECT price FROM products WHERE product_id = ?');
        $stmt->execute([$productID]);
        $product = $stmt->fetch();

        // Perform AJAX
        if ($product) {
            if ($action === 'increase') {
                $newQty = $quantity + 1;
                update_cart($productID, $newQty);
                $newSubtotal = $newQty * $product->price;
            } else if ($action === 'decrease') {
                if ($quantity > 1) {
                    $newQty = $quantity - 1;
                    update_cart($productID, $newQty);
                    $newSubtotal = $newQty * $product->price;
                } else {
                    ob_end_clean();
                    echo json_encode([
                        'action' => $action,
                        'quantity' => $quantity,
                        'success' => false,
                        'message' => 'Each product must have at least 1 in cart'
                    ]);
                    exit();
                }
            } else if ($action === 'remove') {
                unset($cart[$productID]);
                set_cart($cart);
            } else if ($action === 'change') {
                if ($quantity > 1) {
                    $newQty = $quantity;
                    update_cart($productID, $newQty);
                    $newSubtotal = $newQty * $product->price;
                } else {
                    ob_end_clean();
                    echo json_encode([
                        'action' => $action,
                        'quantity' => $cart[$productID],
                        'success' => false,
                        'message' => 'Cannot set product to ' . $quantity
                    ]);
                    exit();
                }
            }

            $cart = get_cart();
            $newTotal = array_sum(array_map(function ($id, $quantity) use ($_db) {
                $stmt = $_db->prepare('SELECT price FROM products WHERE product_id = ?');
                $stmt->execute([$id]);
                $product = $stmt->fetch();
                return $quantity * $product->price;
            }, array_keys($cart), $cart));

            // Calculate total cart count (sum of all product quantities)
            $cartCount = count($cart);

            // Return the updated cart data as a JSON response
            ob_end_clean();
            echo json_encode([
                'action' => $action,
                'success' => true,
                'newQuantity' => $newQty ?? 0,
                'newSubtotal' => $newSubtotal ?? 0,
                'newTotal' => $newTotal ?? 0,
                'cartCount' => $cartCount ?? ''
            ]);
        } else {
            // Product not found in the database, return error
            ob_end_clean();
            echo json_encode([
                'action' => $action,
                'quantity' => $quantity,
                'success' => false,
                'message' => 'Product not found'
            ]);
        }
    } else {
        // Missing required POST data
        ob_end_clean();
        echo json_encode([
            'action' => $action,
            'quantity' => $quantity,
            'success' => false,
            'message' => 'Error when parsing data'
        ]);
    }
}
exit();
