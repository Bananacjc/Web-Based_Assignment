<?php
include '../_base.php';

if (is_post()) {
    global $_err;

    $order_id = req('order_id');
    $customer_id = req('customer_id');
    $order_items = req('order_items');
    $promo_amount = req('promo_amount');
    $sub_total = req('sub_total');
    $shipping_fee = req('shipping_fee');
    $payment_method = req('payment_method');
    $order_time = req('order_time');
    $status = req('status');

    $_err = [];

    if (empty($order_items)) {
        $_err['order_items'] = "Order items are required.";
    }
    if (empty($payment_method)) {
        $_err['payment_method'] = "Payment method is required.";
    }

    if (empty($sub_total)) {
        $_err['sub_total'] = "Sub total is required.";
    } elseif (!is_numeric($sub_total)) {
        $_err['sub_total'] = "Sub total must be numeric.";
    }

    if (!is_numeric($promo_amount)) {
        $_err['promo_amount'] = "Promo amount must be numeric.";
    }

    if (empty($shipping_fee)) {
        $_err['shipping_fee'] = "Shipping fee is required.";
    } elseif (!is_numeric($shipping_fee)) {
        $_err['shipping_fee'] = "Shipping fee must be numeric.";
    }

    if (empty($order_time)) {
        $_err['order_time'] = "Order time is required.";
    }

    if (empty($status)) {
        $_err['status'] = "Status is required.";
    } elseif (!in_array($status, ['PAID', 'SHIPPING', 'DELIVERED'])) {
        $_err['status'] = "Invalid status.";
    }

    if (!$_err) {
        try {
            $order_items_raw = $order_items;
            $order_items = [];
            if (!empty($order_items_raw)) {
                preg_match_all('/(PRO-\d+-[a-zA-Z0-9]+):\s*(\d+)/', $order_items_raw, $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {
                    $order_items[$match[1]] = (int)$match[2];
                }
                $order_items = json_encode($order_items);
                if (!$order_items) {
                    $_err['order_items'] = "Invalid order items format.";
                }
            } else {
                $_err['order_items'] = "Order items cannot be empty.";
            }
            
            $valid_methods = ['link', 'alipay', 'grabpay', 'fpx', 'other']; // Predefined methods

            if (!empty($payment_method)) {
                // Check for bank payment method
                if (preg_match('/accNum:\s*([\d]+)cvv:\s*([\d]+)expiry:\s*([\d\-]+)/', $payment_method, $matches)) {
                    // Convert to JSON if it matches the bank payment method format
                    $payment_method = json_encode([
                        'accNum' => $matches[1],
                        'cvv' => $matches[2],
                        'expiry' => $matches[3],
                    ], JSON_UNESCAPED_SLASHES);
                } elseif (in_array(trim($payment_method), $valid_methods, true)) {
                    // For predefined methods, keep as plain text
                    $payment_method = json_encode(trim($payment_method));
                } else {
                    // Invalid format
                    $_err['payment_method'] = "Invalid payment method format. Expected 'accNum: <value> cvv: <value> expiry: <value>' or a valid predefined method: " . implode(', ', $valid_methods) . ".";
                }
            } else {
                // Handle empty input
                $_err['payment_method'] = "Payment method cannot be empty.";
            }

            $total = ($sub_total + $shipping_fee) - $promo_amount;


            $query = "
                UPDATE orders 
                SET customer_id = ?, order_items = ?, promo_amount = ?, subtotal = ?, 
                    shipping_fee = ?, total = ?, payment_method = ?, order_time = ?, status = ?
                WHERE order_id = ?
            ";

            $stmt = $_db->prepare($query);
            $stmt->execute([
                $customer_id,
                $order_items,
                $promo_amount,
                $sub_total,
                $shipping_fee,
                $total,
                $payment_method,
                $order_time,
                $status,
                $order_id
            ]);

            if ($_user && isset($_user->employee_id)) {
                $employeeId = $_user->employee_id;
                log_action($employeeId, 'Update Order', "Updated Order: $order_id", $_db);
            }

            temp('info', "Order with ID: $order_id has been updated successfully.");
            redirect('orderStatus.php');
        } catch (PDOException $e) {
            $_err['error'] = "Error updating order: " . $e->getMessage();
        }
    } else {
        temp('error', $_err);
        redirect('orderStatus.php');
    }
}
