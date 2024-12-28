<?php
include 'adminHeader.php';


$category_name = req('category_name');
$stmt = $_db->prepare("SELECT COUNT(*) FROM categories WHERE category_name = ?");
$stmt->execute([$category_name]);
$stmt->fetchColumn();


if (is_post()) {
    if (isset($_POST['add_category'])) {

        $category_name = req('category_name');
        $category_image = get_file('category_image');

        if (empty($category_name)) {
            $_err['category_name'] = "Category Name is required.";
            redirect('category.php');
        } else {
            $stmt = $_db->prepare("SELECT category_name FROM categories WHERE category_name = ?");
            $stmt->execute([$category_name]);
            if ($stmt->rowCount() === 1) {
                $_err['category_name'] = "Category is exist.";
            }
            redirect('category.php');
        }

        if ($category_image) {
            if (!str_starts_with($category_image->type, 'image/')) {
                $_err['category_image'] = "Category image must be a valid image file.";
                redirect('category.php');
            } elseif ($category_image->size > 2 * 1024 * 1024) {
                $_err['category_image'] = "Category image exceeds the size limit (2MB).";
                redirect('category.php');
            }
        }

        if ($category_image) {
            $category_image_path = save_photo($category_image, '../../uploads/category_images');
        } else {
            $category_image_path = null;
        }

        if (!$_err) {

            $stmt = $_db->prepare("INSERT INTO categories (category_name, category_image) VALUES (?, ?)");
            $stmt->execute([$category_name, $category_image]);

            if ($_user && isset($_user->employee_id)) {
                $employeeId = $_user->employee_id;
                log_action($employeeId, 'Add Category', 'Added new category: ' . $category_name, $_db);
            }

            temp('info', "Category added successfully!");
            redirect('category.php');
        }
    } elseif (isset($_POST['edit_category'])) {
        $old_category_name = req('old_categgory_name');
        $category_image = get_file('category_image');

        if ($category_name != $old_category_name) {
            $_err['category_name'] = "Category is existing.";
            redirect('category.php');
        }


        if ($category_image) {
            if (!str_starts_with($category_image->type, 'image/')) {
                $_err['category_image'] = "Category image must be a valid image file.";
                redirect('category.php');
            } elseif ($category_image->size > 2 * 1024 * 1024) {
                $_err['category_image'] = "Category image exceeds the size limit (2MB).";
                redirect('category.php');
            }
        }
        if (!$_err) {

            if ($category_image) {
                $category_image_path = save_photo($category_image, '../../uploads/category_images');
            } else {
                $stmt = $_db->prepare("SELECT category_image FROM categories WHERE category_name = ?");
                $stmt->execute([$category_name]);
                $category_image_path = $stmt->fetchColumn();
            }
        }

        $stmt = $_db->prepare("UPDATE categories SET category_name = ?, category_image = ? WHERE category_name = ?");
        $stmt->execute([$category_name, $category_image, $old_category_name]);

        if ($_user && isset($_user->employee_id)) {
            $employeeId = $_user->employee_id;
            log_action($employeeId, 'Updated Customer', "Updated Customer: $customer_id", $_db);
        }
        temp('info', 'Customer updated successfully!');
        redirect('customer.php');
    } else {
        temp('error', $_err);
        redirect('customer.php');
    }
}


$categories = $_db->query('SELECT * FROM categories')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/productStaffAdmin.css" />

    <title>Category Management</title>
</head>

<body>
    <h1>Category Management</h1>
    <div class="main">

        <h2>Category List</h2>
        <table>
            <thead>
                <tr>
                    <th>Category Name</th>
                    <th>Category Image</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?= $category['category_name'] ?></td>
                        <td><img src="../../uploads/category_images/<?= $category['category_image'] ?>" class="resized-image" alt="<?= $category['category_name'] ?>"></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div id="addCategoryModal" class="modal" style="margin-top: 80px;">
            <div class="modal-content">
                <span class="close-button" onclick="hideAddForm()">&times;</span>
                <form method="POST" enctype="multipart/form-data" class="add-form">
                    <label for="category_name">Category Name:</label>
                    <input type="text" name="category_name" required>

                    <label for="category_image">Category Image:</label>
                    <input type="file" name="category_image" required>

                    <button type="submit" name="add_category">Add Category</button>
                </form>
            </div>
        </div>

        <div id="updateModal" class="modal" style="margin-top: 80px;">
            <div class="modal-content">
                <span class="close-button" onclick="hideUpdateProductForm()">&times;</span>
                <form method="POST" enctype="multipart/form-data" class="update-form">
                    <label for="old_category_name">Select Category to Edit:</label>
                    <select name="old_category_name" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['category_name'] ?>"><?= $category['category_name'] ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label for="category_name">New Category Name:</label>
                    <input type="text" name="category_name" required>

                    <label for="category_image">New Category Image:</label>
                    <input type="file" name="category_image">

                    <button type="submit" name="edit_category">Edit Category</button>
                </form>
            </div>
        </div>
    </div>



    <!-- Category List -->

</body>

</html>