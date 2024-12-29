<?php
require '../_base.php';

if (is_post()) {
    $orderId = req('order_id');
    $status1 = req('status1');

    if (in_array($status1, ['PAID','SHIPPING', 'DELIVERED'])) {
        $query = "UPDATE orders SET status = ? WHERE order_id = ?";
        $stmt = $_db->prepare($query);
        if ($stmt->execute([$status1, $orderId])) {
            temp('info','Successfully updated order status');
            redirect('orderStatus.php');
        }
    }
}

?>