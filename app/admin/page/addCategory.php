<?php
require '../_base.php';

if (is_post()) {
    $category_name = req('category_name');
    $category_image = get_file('category_image');
    $_err = [];

    if (empty($category_name)) {
        $_err['category_name'] = "Category Name is required.";

    } else {
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

    if (empty($_err)) {
        $category_image_path = $category_image ? save_photo($category_image, '../../uploads/category_images') : null;

        $stmt = $_db->prepare("INSERT INTO categories (category_name, category_image) VALUES (?, ?)");
        $stmt->execute([$category_name, $category_image_path]);

        if ($_user && isset($_user->employee_id)) {
            log_action($_user->employee_id, 'Add Category', "Added new category: $category_name", $_db);
        }

        temp('info', "Category added successfully!");
        redirect('category.php');
    } else {
        temp('error', $_err);
        redirect('category.php');
    }
}
?>
