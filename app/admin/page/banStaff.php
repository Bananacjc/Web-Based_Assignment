<?php
include '../_base.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employee_id = req('employee_id');

    if ($employee_id) {
        $query = "UPDATE employees SET banned = 1 WHERE employee_id = ?";
        $stm = $_db->prepare($query);
        $stm->execute([$employee_id]);

        temp('info',"Employee with ID: $employee_id has been banned successfully.");
        redirect('staff.php');
    }
}
?>