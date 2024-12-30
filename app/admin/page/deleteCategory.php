<?php
include '../_base.php';

if (is_post()) {
    $categoryName = req('category_name');

    $_db->exec('SET FOREIGN_KEY_CHECKS = 0;');

    $stm = $_db->prepare('SELECT category_name, category_image FROM categories WHERE category_name = ?');
    $stm->execute([$categoryName]);
    $category = $stm->fetch(PDO::FETCH_OBJ);

    if ($category) {
        if ($category->category_image && file_exists("../../uploads/category_images/{$category->category_image}")) {
            unlink("../../uploads/category_images/{$category->category_image}");
        }

        $stm = $_db->prepare('DELETE FROM products WHERE category_name = ?');
        $stm->execute([$category->category_name]);

        $stm = $_db->prepare('DELETE FROM categories WHERE category_name = ?');
        $stm->execute([$category->category_name]);

        if ($_user && isset($_user->employee_id)) {
            $employeeId = $_user->employee_id;
            log_action($employeeId, 'Delete Category', "Deleted Category: {$category->category_name}", $_db);
        }

        temp('info', "Category '{$category->category_name}' deleted successfully.");
    } else {
        temp('error', "Category not found.");
    }

    $_db->exec('SET FOREIGN_KEY_CHECKS = 1;');

    redirect('category.php');
}
?>
