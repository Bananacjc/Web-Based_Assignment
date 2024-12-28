<html lang="en">
<?php
include 'adminHeader.php';
$category_name = req('category_name');
$stmt = $_db->prepare("SELECT COUNT(*) FROM categories WHERE category_name = ?");
$stmt->execute([$category_name]);
$stmt->fetchColumn();
$categories = $_db->query('SELECT * FROM categories')->fetchAll(PDO::FETCH_ASSOC);
$allowedRoles = ['MANAGER', 'STAFF'];

?>
<!DOCTYPE html>
?>

<head>
    <script src="../js/category.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/category.css" />

    <title>Category Management</title>
</head>

<body>
    <h1>Category Management</h1>
    <div class="main">
        <div class="sub-main">
            <h2>Category List</h2>
            <td>
                <?php if (in_array($_user?->role, $allowedRoles)): ?>

                    <button class="button action-button" onclick="showUpdateCategoryForm()">
                        Update
                    </button>
                    <button id="addProductBtn" class="button action-button" onclick="showAddForm()">Add new Category</button>

                <?php endif; ?>

            </td>
            <table>
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Category Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?= $category['category_name'] ?></td>
                            <td>
                                <img src="../../uploads/category_images/<?= $category['category_image'] ?>" class="resized-image" alt="<?= $category['category_name'] ?>">
                            </td>
                            <td>
                                <?php if ($_user && $_user->role == 'MANAGER'): ?>

                                    <form action="deleteCategory.php" method="post" style="display:inline;">
                                        <input type="hidden" name="category_name" value="<?= $category['category_name'] ?>">
                                        <button type="submit" class="button delete-action-button" onclick="return confirmDelete();">Delete</button>
                                    </form>

                                <?php endif; ?>

                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>


            <div id="addCategoryModal" class="modal" style="margin-top: 80px;">
                <div class="modal-content">
                    <span class="close-button" onclick="hideAddForm()">&times;</span>
                    <form action="addCategory.php" method="POST" enctype="multipart/form-data" class="add-form">
                        <h2>Add New Category</h2>

                        <label for="category_name">Category Name:</label>
                        <input type="text" name="category_name">

                        <label for="category_image">Category Image:</label>
                        <input type="file" name="category_image">

                        <button type="submit" name="add_category">Add Category</button>
                    </form>


                </div>
            </div>

            <div id="updateModal" class="modal" style="margin-top: 80px;">
                <div class="modal-content">
                    <span class="close-button" onclick="hideUpdateForm()">&times;</span>
                    <form action="updateCategory.php" id="updateForm" method="POST" enctype="multipart/form-data" class="update-form">
                        <h2>Update Category</h2>
                        <label for="old_category_name">Select Category to Edit:</label>
                        <select name="old_category_name" id="categorySelect" required onchange="updateImage()">
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['category_name'] ?>" data-image="<?= '../../uploads/category_images/' . $category['category_image'] ?>">
                                    <?= $category['category_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>


                        <label for="current_image">Current Category Image:</label>
                        <img id="current_image" src="../../uploads/category_images/<?= isset($category['category_image']) && $category['category_image'] ? $category['category_image'] : 'default.png'; ?>" alt="Current Category Image" class="resized-image">
                        <br><br>

                        <label for="category_name">New Category Name:</label>
                        <input type="text" name="category_name">

                        <label for="category_image">New Category Image:</label>
                        <input type="file" name="category_image">

                        <button type="submit" name="edit_category">Edit Category</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

</body>

</html>