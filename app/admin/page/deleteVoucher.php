<?php
include '../_base.php';  

if (is_post()) {
    $id = req('id', []); 
    if (!is_array($id)) $id = [$id]; 
    try {
        foreach ($id as $v) {
            $stm = $_db->prepare('SELECT p.promo_image FROM promotions p WHERE p.promo_id = ?');
            $stm->execute([$v]);
            $promotion = $stm->fetch(PDO::FETCH_OBJ);

            if ($promotion && $promotion->promo_image && file_exists("../../uploads/promo_images/{$promotion->promo_image}")) {
                unlink("../../uploads/promo_images/{$promotion->promo_image}");
            }

            $stm = $_db->prepare('DELETE FROM promotions WHERE promo_id = ?');
            $stm->execute([$v]);
        }


        if ($_user && isset($_user->employee_id)) {
            $employeeId = $_user->employee_id;
            $deletedProductIds = implode(', ', $id);
            log_action($employeeId, 'Delete Voucher', "Delete Voucher: {$deletedProductIds}", $_db);
        }

        temp('info', count($id) . " Voucher(s) deleted successfully.");  
    } catch (Exception $e) {
        temp('error', 'Error deleting product(s): ' . $e->getMessage());
    }
} 
redirect('promotionVoucher.php');


