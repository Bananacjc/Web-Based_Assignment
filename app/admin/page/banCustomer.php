<?php
include '../_base.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = req('customer_id');

    if ($customer_id) {
        $query = "UPDATE customers SET banned = 1 WHERE customer_id = ?";
        $stm = $_db->prepare($query);
        $stm->execute([$customer_id]);

        temp('info',"Employee with ID: $customer_id has been banned successfully.");
        redirect('customer.php');
    }
}
?>