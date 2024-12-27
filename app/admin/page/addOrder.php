<?php
require '../_base.php';

if (is_post()) {
    global $_err;

    $customer_id = req('customer_id');
    $order_items = req('order_items'); 
    $promo_amount = req('promo_amount');
    $sub_total = req('sub_total');
    $shipping_fee = req('shipping_fee');
    $payment_method = req('payment_method'); 
    $order_time = req('order_time');
    $status = req('status');

    $_err = [];

    if (empty($customer_id)) {
        $_err['customer_id'] = "Customer ID is required.";
    } else {
        $stmt = $_db->prepare("SELECT customer_id FROM customers WHERE customer_id = ?");
        $stmt->execute([$customer_id]);
        if ($stmt->rowCount() === 0) {
            $_err['customer_id'] = "Customer ID does not exist.";
        }
    }

    if (empty($order_items)) {
        $_err['order_items'] = "Order items are required.";
    } 
    if (empty($payment_method)) {
        $_err['payment_method'] = "Payment method is required.";
    } 

    if (!is_numeric($promo_amount)) {
        $_err['promo_amount'] = "Promo amount must be numeric.";
    }

    if (empty($sub_total)) {
        $_err['sub_total'] = "Sub total is required.";
    } elseif (!is_numeric($sub_total)) {
        $_err['sub_total'] = "Sub total must be numeric.";
    }

    if(!is_numeric($shipping_fee)) {
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

        $order_items = json_encode($order_items);
        $payment_method = json_encode($payment_method);

        $total = ($sub_total + $shipping_fee) - $promo_amount;

        $order_id = generate_unique_id('ORD', 'orders', 'order_id', $_db);

        try {
            $stmt = $_db->prepare("
                INSERT INTO orders (
                    order_id, customer_id, order_items, promo_amount, subtotal,
                    shipping_fee, total, payment_method, order_time, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $order_id,
                $customer_id,
                $order_items, 
                $promo_amount,
                $sub_total,
                $shipping_fee,
                $total,
                $payment_method, 
                $order_time,
                $status
            ]);

            if ($_user && isset($_user->employee_id)) {
                $employeeId = $_user->employee_id;
                log_action($employeeId, 'Add Order', 'Added new order: ' . $order_id, $_db);
            }

            temp('info', "Order added successfully!");
            redirect('orderStatus.php');
        } catch (PDOException $e) {
            $_err['error'] = 'Error adding order: ' . $e->getMessage();
        }
    } else {
        temp('error', $_err);
        redirect('orderStatus.php');
    }
}
?>
