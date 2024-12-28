<?php
require '../_base.php';

if (is_post()) {
    $old_category_name = req('old_category_name');
    $category_name = req('category_name');
    $category_image = get_file('category_image');
    $_err = [];

    if (empty($category_name)) {
        $_err['category_name'] = "Category Name is required.";
    } elseif ($category_name !== $old_category_name) {
        $stmt = $_db->prepare("SELECT category_name FROM categories WHERE category_name = ?");
        $stmt->execute([$category_name]);
        if ($stmt->rowCount() > 0) {
            $_err['category_name'] = "Category already exists.";
        }
    }

    if ($category_image) {
        if (!str_starts_with($category_image->type, 'image/')) {
            $_err['category_image'] = "Category image must be a valid image file.";
        } elseif ($category_image->size > 2 * 1024 * 1024) {
            $_err['category_image'] = "Category image exceeds the size limit (2MB).";
        }
    }

    // If no errors
    if (empty($_err)) {
        $category_image_path = $category_image ? save_photo($category_image, '../../uploads/category_images') : null;

        if (!$category_image_path) {
            $stmt = $_db->prepare("SELECT category_image FROM categories WHERE category_name = ?");
            $stmt->execute([$old_category_name]);
            $category_image_path = $stmt->fetchColumn();
        }


        try {
            $_db->exec("SET foreign_key_checks = 0");

            $stmt = $_db->prepare("UPDATE products SET category_name = ? WHERE category_name = ?");
            $stmt->execute([$category_name, $old_category_name]);

            $stmt = $_db->prepare("UPDATE categories SET category_name = ?, category_image = ? WHERE category_name = ?");
            $stmt->execute([$category_name, $category_image_path, $old_category_name]);

            $_db->exec("SET foreign_key_checks = 1");

            if ($_user && isset($_user->employee_id)) {
                log_action($_user->employee_id, 'Update Category', "Updated category: $old_category_name to $category_name", $_db);
            }


            temp('info', "Category updated successfully!");
            redirect('category.php');
        } catch (Exception $e) {
            temp('error', $_err);
            redirect('category.php');
        }
    } else {
        temp('error', $_err);
        redirect('category.php');
    }
}
