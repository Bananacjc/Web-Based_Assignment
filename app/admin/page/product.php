<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../css/orderStatus.css" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Product Management</title>
</head>

<?php


include 'adminHeader.php'

?>
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


$product_name = req('product_name');
$category_name = req('category_name');


$sort = req('sort');
key_exists($sort, $fields) || $sort = 'product_id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';

$page = req('page', 1);
$limit = 10;
$offset = ($page - 1) * $limit;

$total_products_query = 'SELECT COUNT(*) FROM products WHERE product_name LIKE ? AND (category_name = ? OR ?)';
$total_products_stm = $_db->prepare($total_products_query);
$total_products_stm->execute(["%$product_name%", $category_name, $category_name == null]);
$total_products = $total_products_stm->fetchColumn();
$total_pages = ceil($total_products / $limit);

$query = "
       SELECT p.*, c.category_image 
       FROM products p
       JOIN categories c ON p.category_name = c.category_name
       WHERE p.product_name LIKE ? 
       AND (p.category_name = ? OR ?)
       ORDER BY  $sort $dir
       LIMIT $limit OFFSET $offset";

$stm = $_db->prepare($query);
$stm->execute(["%$product_name%", $category_name, $category_name == null]);
$arr = $stm->fetchAll();

$_categoryName = $_db->query('SELECT category_name, category_name FROM categories')
    ->fetchAll(PDO::FETCH_KEY_PAIR);
?>


<div class="main">
    <h1>PRODUCTS</h1>

    <form>
        <?= html_search('product_name', '', 'Search Product Name') ?>
        <?= html_select('category_name', $_categoryName, 'All Categories', $category_name) ?>
        <button>Search</button>
    </form>

    <?php if ($_user?->role == 'MANAGER'): ?>

        <form method="post" id="f">
            <button class="delete-btn" formaction="delete.php" onclick="return confirmDelete()">Batch Delete</button>
        </form>
    <?php endif; ?>

    <p><?= count($arr) ?> product(s) on this page | Total: <?= $total_products ?> product(s)</p>

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
                        <img src="../../admin/uploads/product_images/<?= $s->product_image ?>" class="resized-image">
                    </td>
                    <td><?= $s->product_id ?></td>
                    <td><?= $s->product_name ?></td>
                    <td>
                        <img src="../../uploads/category_images/<?= $s->category_image ?>" class="resized-image">
                    </td>
                    <td><?= $s->category_name ?></td>
                    <td><?= $s->price ?></td>
                    <td><?= $s->description ?></td>
                    <td><?= $s->current_stock ?></td>
                    <td><?= $s->amount_sold ?></td>
                    <td><?= $s->status ?></td>

                    <td>


                        <button class="button view-action-button" onclick="showViewForm(
    '<?= $s->product_id ?>', 
    '<?= $s->product_name ?>',
         '<?= $s->product_image ?>', 

    '<?= $s->category_name ?>', 
    '<?= $s->price ?>', 
    '<?= $s->description ?>', 
    '<?= $s->current_stock ?>', 
    '<?= $s->amount_sold ?>', 
    '<?= $s->status ?>'
    )">View


                            <?php if ($_user?->role == 'MANAGER'): ?>

                                <button class="button action-button" onclick="showUpdateProductForm(
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


                                <form action="delete.php" method="post">
                                    <input type="hidden" name="id" value="<?= $s->product_id ?>">
                                    <button type="submit" class="button delete-action-button" onclick="return confirmDelete();">Delete</button>
                                </form>
                            <?php endif; ?>

                        </button>
                    </td>


            </tr>
        </tbody>
    <?php endforeach ?>

    </table>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=1" class="first-page">First</a>
            <a href="?page=<?= $page - 1 ?>" class="prev-page">Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?>" class="page-number <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?= $page + 1 ?>" class="next-page">Next</a>
            <a href="?page=<?= $total_pages ?>" class="last-page">Last</a>
        <?php endif; ?>
    </div>


    <?php
    $_categories = [];
    try {
        $stmt = $_db->query("SELECT category_name, category_image FROM categories");
        $_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        temp('error', "Error fetching categories: " . $e->getMessage());
        redirect();
    }
    ?>


    <div style="margin: 30px;">
        <button id="addProductBtn" class="action-button" onclick="showAddForm()">Add new product</button>
    </div>



    <div id="viewModal" class="modal">
        <div class="modal-content">
            <form id='viewForm'>
                <span class="close-button" onclick="hideViewForm()">&times;</span>
                <h2>Product Details</h2>
                <p><strong>Product ID:</strong> <span id="viewProductID"></span></p>
                <p><strong>Product Name:</strong> <span id="viewProductName"></span></p>
                <p><strong>Product Image:</strong> <span id="viewProductImage"></span></p>
                <p><strong>Category Name:</strong> <span id="viewCategoryName"></span></p>
                <p><strong>Price(RM):</strong> <span id="viewPrice"></span></p>
                <p><strong>Description:</strong> <span id="viewDescription"></span></p>
                <p><strong>Current Stock:</strong> <span id="viewCurrentStock"></span></p>
                <p><strong>Amount Sold:</strong> <span id="viewAmountSold"></span></p>
                <p><strong>Status:</strong> <span id="viewStatus"></span></p>
                <button type="button" class="cancel-button" onclick="hideViewForm()">Close</button>
        </div>
        </form>

    </div>

    <div id="addProductModal" class="modal" style="margin-top: 80px;">
        <div class="modal-content">
            <span class="close-button" onclick="hideAddForm()">&times;</span>
            <form id="addForm" action="addProduct.php" method="POST" enctype="multipart/form-data" class="add-form">
                <input type="hidden" name="action" value="add">
                <label for="product_name">Product Name:</label>
                <?php html_text('product_name'); ?>
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
                <?php html_number('price'); ?>
                <span class="error"><?php err('price'); ?></span><br><br>

                <label for="description">Description:</label>
                <?php html_textarea('description'); ?>
                <span class="error"><?php err('description'); ?></span><br><br>

                <label for="current_stock">Current Stock:</label>
                <?php html_number('current_stock'); ?>
                <span class="error"><?php err('current_stock'); ?></span><br><br>

                <label for="product_image">Product Image:</label>
                <?php html_file('product_image', 'image/*'); ?>
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


    <div id="updateModal" class="modal" style="margin-top: 80px;">
        <div class="modal-content">
            <span class="close-button" onclick="hideUpdateProductForm()">&times;</span>
            <form id="updateForm" action="updateProduct.php" method="Post" enctype="multipart/form-data" class="update-form">
                <label for="product_id">Product ID:</label>
                <input id="product_id" name="product_id" value="<?= $product['product_id']; ?>" readonly>
                <br>

                <label for="product_name">Product Name:</label>
                <?php html_text('product_name'); ?>
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
                <?php html_number('price'); ?>
                <span class="error"><?php err('price'); ?></span><br><br>

                <label for="description">Description:</label>
                <?php html_textarea('description'); ?>
                <span class="error"><?php err('description'); ?></span><br><br>

                <label for="current_stock">Current Stock:</label>
                <?php html_number('current_stock'); ?>
                <span class="error"><?php err('current_stock'); ?></span><br><br>

                <label for="amount_sold">Amount Sold:</label>
                <?php html_number('amount_sold'); ?>
                <span class="error"><?php err('amount_sold'); ?></span><br><br>

                <label for="product_image">Product Image:</label>
                <input type="file" name="product_image" accept="image/*" />
                <br>
                <label for="current_image">Current Product Image:</label><br>
                <img id="currentImage" src="<?= isset($product['product_image']) && $product['product_image'] ? '../../uploads/product_images/' . $product['product_image'] : ''; ?>" alt="Current Product Image" style="max-width: 150px; max-height: 150px;">
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
    function hideViewForm() {
        document.getElementById('viewModal').style.display = 'none';
    }

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

        form.elements['status'].value = status;

        document.getElementById('currentImage').src = "/admin/uploads/product_images/" + productImage;
    }


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

    function showViewForm(productId, productName, productImage, category, price, productDescription, currentStock, amountSold, status) {
        var modal = document.getElementById('viewModal');
        modal.style.display = 'block';
        document.getElementById('viewProductID').innerText = productId;
        document.getElementById('viewProductName').innerText = productName;
        document.getElementById('viewProductImage').innerText = productImage;
        document.getElementById('viewCategoryName').innerText = category;
        document.getElementById('viewPrice').innerText = price;
        document.getElementById('viewDescription').innerText = productDescription;
        document.getElementById('viewCurrentStock').innerText = currentStock;
        document.getElementById('viewAmountSold').innerText = amountSold;
        document.getElementById('viewStatus').innerText = status;

    }
</script>



</html>