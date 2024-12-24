<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../css/productStaffAdmin.css" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Product Management</title>
</head>

<?php


include 'adminHeader.php' ?>

<?php
$fields = [
    '',
    'product_image' => 'Product Image',
    'product_id'    => 'Product ID',
    'product_name'  => 'Product Name',
    'category_image'  => 'Category Image',
    'category_name' => 'Category',
    'price'         => 'Price',
    'description'   => 'Description',
    'current_stock' => 'Stock',
    'amount_sold'   => 'Amount Sold',
    'status'        => 'Status',
    'Action'
];


$sort = req('sort');
key_exists($sort, $fields) || $sort = 'product_id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';



$product_name = req('product_name');
$category_name = req('category_name');

// Fetch filtered data with optional filters
$query = '
       SELECT p.*, c.category_image 
       FROM products p
       JOIN categories c ON p.category_name = c.category_name
       WHERE p.product_name LIKE ? 
       AND (p.category_name = ? OR ?)
       ORDER BY ' . $sort . ' ' . $dir;

$stm = $_db->prepare($query);
$stm->execute(["%$product_name%", $category_name, $category_name == null]);
$arr = $stm->fetchAll();

// Fetch category options for dropdown
$_categoryName = $_db->query('SELECT category_name, category_name FROM categories')
    ->fetchAll(PDO::FETCH_KEY_PAIR);
?>


<div class="main">
    <h1>PRODUCTS</h1>
    
    <form>
        <?= html_search('product_name', 'Search Product Name', $product_name) ?>
        <?= html_select('category_name', $_categoryName, 'All Categories', $category_name) ?>
        <button>Search</button>
    </form>

    <form method="post" id="f">
        <button formaction="restore.php">Restore</button>
        <button formaction="delete.php" onclick="return confirmDelete()">Delete</button>
    </form>


    <p><?= count($arr) ?> product(s)</p>


    <!-- Product Table -->
    <table id="productTable" class="data-table">

        <thead>
            <tr>
                <?= table_headers($fields, $sort, $dir) ?>


            </tr>
        </thead>
        <tbody>
            <tr class="product-row" data-category="">
                <?php foreach ($arr as $s): ?>
                    <td>
                        <input type="checkbox"
                            name="id[]"
                            value="<?= $s->product_id ?>"
                            form="f">
                    </td>
                    <td>
                        <img src="/admin/uploads/product_images/<?= $s->product_image ?>" class="resized-image">
                    </td>
                    <td><?= $s->product_id ?></td>
                    <td><?= $s->product_name ?></td>
                    <td>
                        <img src="/admin/uploads/category_images/<?= $s->category_image ?>" class="resized-image">
                    </td>
                    <td><?= $s->category_name ?></td>
                    <td><?= $s->price ?></td>
                    <td><?= $s->description ?></td>
                    <td><?= $s->current_stock ?></td>
                    <td><?= $s->amount_sold ?></td>
                    <td><?= $s->status ?></td>

                    <td>


                        <?php if ($_user?->role == 'MANAGER'): ?>


                            <button class="action-button"  onclick="showUpdateProductForm(
    '<?= $s->product_image ?>', 
    '<?= $s->product_id ?>', 
    '<?= $s->product_name ?>', 
    '<?= $s->category_name ?>', 
    '<?= $s->price ?>', 
    '<?= $s->description ?>', 
    '<?= $s->current_stock ?>', 
    '<?= $s->amount_sold ?>', 
    '<?= $s->status ?>'
)">
                                Update
                            </button>


                            <form action="delete.php" method="post" style="display: inline;">
                                <input type="hidden" name="id" value="<?= $s->product_id ?>">
                                <button type="submit" class="delete-action-button" onclick="return confirmDelete();">Delete</button>
                            </form>
                        <?php elseif ($_user?->role == 'STAFF'): ?>
                            <button class="action-button" onclick="showAccessDenied()">Update</button>
                            <button type="submit" class="deleteButton" onclick="showAccessDenied();">Delete</button>


                            <div id="accessDeniedMessage" class="modal" style="margin-top: 80px; display: none;">
                                <div class="modal-content">
                                    <span class="close-button" onclick="hideAccessDenied()">&times;</span>
                                    <p>You do not have the necessary permissions to update or delete products. Your role is restricted.</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <div style="margin: 30px;">
                                <button id="updateProductBtn" class="action-button" onclick="showAccessDenied()">Update</button>
                            </div>
                            <div id="accessDeniedMessage" class="modal" style="margin-top: 80px; display: none;">
                                <div class="modal-content">
                                    <span class="close-button" onclick="hideAccessDenied()">&times;</span>
                                    <p>You do not have the necessary permissions to update or delete products. Your role is restricted.</p>
                                </div>
                            </div>
                        <?php endif; ?>

                    </td>
            </tr>
        </tbody>
    <?php endforeach ?>

    </table>

    <?php
    $roles = ['MANAGER', 'STAFF'];
    $_categories = [];
    try {
        $stmt = $_db->query("SELECT category_name, category_image FROM categories");
        $_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        temp('error', "Error fetching categories: " . $e->getMessage());
        redirect();
    }
    ?>


    <!-- Add Product Modal -->
    <?php if (in_array($_user?->role, $roles)): ?>
        <div style="margin: 30px;">
            <button id="addProductBtn" class="action-button" onclick="showAddForm()">Add new product</button>
        </div>

        <div id="addProductModal" class="modal" style="margin-top: 80px;">
            <div class="modal-content">
                <span class="close-button" onclick="hideAddForm()">&times;</span>
                <form id="addForm" action="addProduct.php" method="POST" enctype="multipart/form-data" class="add-form">
                    <input type="hidden" name="action" value="add">
                    <label for="product_name">Product Name:</label>
                    <?php html_text('product_name', 'required'); ?>
                    <span class="error"><?php err('product_name'); ?></span><br><br>

                    <label for="categories">Existing Categories:</label>
                    <?php html_select('category_name', array_column($_categories, 'category_name', 'category_name'), '- Select Category -'); ?>
                    <span class="error"><?php err('category_name'); ?></span><br><br>

                    <label for="new_category_name">New Category Name:</label>
                    <?php html_text('new_category_name'); ?><br><br>

                    <label for="new_category_image">New Category Image:</label>
                    <?php html_file('new_category_image', 'image/*'); ?>
                    <span class="error"><?php err('new_category_image'); ?></span><br><br>

                    <label for="price">Price:</label>
                    <?php html_number('price', '0', '', '0.01', 'required'); ?>
                    <span class="error"><?php err('price'); ?></span><br><br>

                    <label for="description">Description:</label>
                    <?php html_textarea('description', 'required'); ?>
                    <span class="error"><?php err('description'); ?></span><br><br>

                    <label for="current_stock">Current Stock:</label>
                    <?php html_number('current_stock', '0', '', '1', 'required'); ?>
                    <span class="error"><?php err('current_stock'); ?></span><br><br>

                    <label for="product_image">Product Image:</label>
                    <?php html_file('product_image', 'image/*', 'required'); ?>
                    <span class="error"><?php err('product_image'); ?></span><br><br>

                    <label for="status">Status:</label>
                    <?php html_select('status', [
                        'AVAILABLE' => 'Available',
                        'UNAVAILABLE' => 'Unavailable',
                        'OUT_OF_STOCK' => 'Out of Stock'
                    ], '- Select Status -', 'required'); ?>
                    <span class="error"><?php err('status'); ?></span><br><br>

                    <input type="submit" value="Add Product" onclick="return confirmAddProduct()">
                    <button type="button" class="cancel-button" onclick="hideAddForm()">Cancel</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div style="margin: 30px;">
            <button id="addProductBtn" class="action-button" onclick="showAccessDenied()">Add new product</button>
        </div>
        <div id="accessDeniedMessage" class="modal" style="margin-top: 80px; display: none;">
            <div class="modal-content">
                <span class="close-button" onclick="hideAccessDenied()">&times;</span>
                <p>You do not have the necessary permissions to add a product.</p>
            </div>
        </div>
    <?php endif; ?>

    <div id="updateModal" class="modal" style="margin-top: 80px;">
        <div class="modal-content">
            <span class="close-button" onclick="hideUpdateProductForm()">&times;</span>
            <form id="updateForm" action="updateProduct.php" method="Post" enctype="multipart/form-data" class="update-form">
                <label for="product_id">Product ID:</label>
                <input id="product_id" name="product_id" value="<?= $product['product_id']; ?>">
                <br>

                <label for="product_name">Product Name:</label>
                <?php html_text('product_name', 'required'); ?>
                <span class="error"><?php err('product_name'); ?></span><br><br>

                <label for="categories">Existing Categories:</label>
                <?php html_select('category_name', array_column($_categories, 'category_name', 'category_name'), '- Select Category -'); ?>
                <span class="error"><?php err('category_name'); ?></span><br><br>

                <label for="new_category_name">New Category Name:</label>
                <?php html_text('new_category_name'); ?><br><br>

                <label for="new_category_image">New Category Image:</label>
                <?php html_file('new_category_image', 'image/*'); ?>
                <span class="error"><?php err('new_category_image'); ?></span><br><br>

                <label for="price">Price:</label>
                <?php html_number('price', '0', '', '0.01', 'required'); ?>
                <span class="error"><?php err('price'); ?></span><br><br>

                <label for="description">Description:</label>
                <?php html_textarea('description', 'required'); ?>
                <span class="error"><?php err('description'); ?></span><br><br>

                <label for="current_stock">Current Stock:</label>
                <?php html_number('current_stock', '0', '', '1', 'required'); ?>
                <span class="error"><?php err('current_stock'); ?></span><br><br>

                <label for="amount_sold">Amount Sold:</label>
                <?php html_number('amount_sold', '0', '', '1', 'required'); ?>
                <span class="error"><?php err('amount_sold'); ?></span><br><br>

                <label for="product_image">Product Image:</label>
                <input type="file" name="product_image" accept="image/*" />
                <br>
                <label for="current_image">Current Product Image:</label><br>
                <img id="currentImage" src="<?= isset($product['product_image']) && $product['product_image'] ? '/admin/uploads/product_images/' . $product['product_image'] : ''; ?>" alt="Current Product Image" style="max-width: 150px; max-height: 150px;">
                <span class="error"><?php err('product_image'); ?></span><br><br>


                <label for="status">Status:</label>
                <?php html_select('status', [
                    'AVAILABLE' => 'Available',
                    'UNAVAILABLE' => 'Unavailable',
                    'OUT_OF_STOCK' => 'Out of Stock'
                ], '- Select Status -', 'required'); ?>
                <span class="error"><?php err('status'); ?></span><br><br>

                <input type="submit" value="Update">
                <button type="button" class="cancel-button" onclick="hideUpdateProductForm()">Cancel</button>
            </form>
        </div>
    </div>


</div>


<script>
    function showUpdateProductForm(productImage, productId, productName, category, price, productDescription, currentStock, amountSold, status) {
        var modal = document.getElementById('updateModal');
        var form = document.getElementById('updateForm');
        modal.style.display = "block";
        form.elements['product_id'].value = productId;
        form.elements['product_name'].value = productName;
        form.elements['category_name'].value = category;
        form.elements['price'].value = price;
        form.elements['description'].value = productDescription;
        form.elements['current_stock'].value = currentStock;
        form.elements['amount_sold'].value = amountSold;

        // Set the status dropdown value
        form.elements['status'].value = status;

        // Optionally set the image field if needed
        document.getElementById('currentImage').src = "/admin/uploads/product_images/" + productImage;
    }


    // Function to hide the update product modal
    function hideUpdateProductForm() {
        document.getElementById('updateModal').style.display = "none";
    }

    function confirmDelete() {
        return confirm("Are you sure you want to delete this product?");
    }

    function showAddForm() {
        var modal = document.getElementById('addProductModal');
        modal.style.display = "block";
    }

    // Function to hide the add product modal
    function hideAddForm() {
        document.getElementById('addProductModal').style.display = "none";
    }

    function showAccessDenied() {
        document.getElementById('accessDeniedMessage').style.display = 'block';
    }

    function hideAccessDenied() {
        document.getElementById('accessDeniedMessage').style.display = 'none';
    }
    function confirmAddProduct() {
        const confirmation = confirm("Are you sure you want to add this product?");
        return confirmation; 
    }
</script>


</html>