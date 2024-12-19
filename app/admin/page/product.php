<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../css/productStaffAdmin.css" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Product Management</title>
</head>
<style>
    .modal {
        margin-top: 80px;
        display: none;
        position: absolute;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: auto;
        background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
        background: white;
        width: 60%;
        margin: 35px auto;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        box-sizing: border-box;
    }

    .close-button {
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .productTable th a.asc::after {
        content: ' ▴';
    }

    .productTable th a.desc::after {
        content: ' ▾';
    }
</style>
<?php


include 'adminHeader.php' ?>

<?php
$fields = [
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
    'action' => 'Action'
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


                        <?php if ($_user?->role=='MAR'): ?>
                            <!-- Allowed roles: Display Update and Delete buttons -->
                            <button class="action-button" data-get="update.php?=<?= $s->$product_id ?>"
                                onclick="showUpdateProductForm('<%= product.getProductId() %>', '<%= product.getProductName() %>', '<%= product.getCategory() %>', '<%= product.getPrice() %>', '<%= product.getDescription() %>','<%= product.getQuantity() %>', '<%= product.getAmountSold() %>', '<%= product.getImage() %>')">
                                Update
                            </button>
                            <form action="delete.php" method="post" style="display: inline;">
                                <input type="hidden" name="product_id" value="<?= $s->product_id ?>">
                                <button type="submit" class="delete-action-button" onclick="return confirmDelete();">Delete</button>
                            </form>
                            <?php else: ?>
                                <!-- Denied roles: Show specific denied message -->
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
    $roles = ['MANAGER', 'STAFF']; //define the role for access
    $_categories = [];
    try {
        $stmt = $_db->query("SELECT category_name, category_image FROM categories");
        $_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        temp('error', "Error fetching categories: " . $e->getMessage());
        redirect(); // Redirect to prevent further execution
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

                    <input type="submit" value="Add Product">
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

    <script>
        function showAccessDenied() {
            document.getElementById('accessDeniedMessage').style.display = 'block';
        }

        function hideAccessDenied() {
            document.getElementById('accessDeniedMessage').style.display = 'none';
        }
    </script>






    <!-- Update Product Modal -->
    <div id="updateModal" class="modal" style="margin-top: 80px;">
        <div class="modal-content">
            <span class="close-button" onclick="hideUpdateProductForm()">&times;</span>
            <form id="updateForm" action="updateProduct.php" method="Post" enctype="multipart/form-data" class="update-form">
                <input type="hidden" id="updateAction" name="action" value="update">
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

                <input type="submit" value="Update">
                <button type="button" class="cancel-button" onclick="hideUpdateProductForm()">Cancel</button>
            </form>
        </div>
    </div>
</div>


<script>
    function filterByCategory() {
        var select = document.getElementById("categoryFilter");
        var category = select.value;
        var table = document.getElementById("productTable");
        var rows = table.getElementsByTagName("tr");

        for (var i = 1; i < rows.length; i++) { // Start from 1 to skip the table header
            var currentCategory = rows[i].getAttribute("data-category");
            if (category === "All" || currentCategory === category) {
                rows[i].style.display = ""; // Show row
            } else {
                rows[i].style.display = "none"; // Hide row
            }
        }
    }


    function checkOtherCategory(select, inputId) {
        var value = select.value;
        var input = document.getElementById(inputId);
        if (value === 'Other') {
            input.style.display = 'block';
        } else {
            input.style.display = 'none';
            input.value = ''; // Clear the input when other categories are selected
        }
    }

    function showUpdateProductForm(productImage, productId, productName, categcategory, price, productDescription, currentStock, amountSold) {
        var modal = document.getElementById('updateModal');
        var form = document.getElementById('updateForm');
        modal.style.display = "block";
        form.elements['productId'].value = productId;
        form.elements['productName'].value = productName;
        form.elements['category'].value = category;
        form.elements['price'].value = productPrice;
        form.elements['description'].value = productDescription;
        form.elements['quantity'].value = quantity;
        form.elements['amountSold'].value = amountSold;
        document.getElementById('currentImage').src = 'data:image/jpeg;base64,' + imageUrl;
        document.getElementById('currentImage').alt = productName;
    }

    // Function to hide the update product modal
    function hideUpdateProductForm() {
        document.getElementById('updateModal').style.display = "none";
    }

    // Confirmation dialog for deleting a product
    function confirmDelete() {
        return confirm("Are you sure you want to delete this product?");
    }

    // Function to show the modal for adding a new product
    function showAddForm() {
        var modal = document.getElementById('addProductModal');
        modal.style.display = "block";
    }

    // Function to hide the add product modal
    function hideAddForm() {
        document.getElementById('addProductModal').style.display = "none";
    }
</script>


</html>