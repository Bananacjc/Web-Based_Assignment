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
    background-color: rgba(0,0,0,0.4);
}
.modal-content {
    background: white;
    width: 60%;
    margin: 35px auto;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    box-sizing: border-box;
} 
.close-button {
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}
</style>
<?php include 'adminHeader.php' ?>


<div class="main">
        <h1>PRODUCTS</h1>
        <div class="category-filter">
            <label for="categoryFilter">Category:</label>
            <select id="categoryFilter" onchange="filterByCategory()">
                <option value="All">All</option>
                <option value="Fruits">Fruits</option>
                <option value="Vegetables">Vegetables</option>
                <option value="Juices">Juices</option>
                <option value="Cold Drinks">Cold Drinks</option>
                <option value="Meat">Meat</option>
                <option value="Breads">Breads</option>
            </select>
        </div>

    <?php    
    $arr = $_db->query('SELECT * FROM products')->fetchAll();
?>
        <!-- Product Table -->
        <table id="productTable" class="data-table">
            <thead>
                <tr>
                    <th>Product Image</th>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Category Image</th>
                    <th>Category</th>
                    <th>Price (RM)</th>
                    <th>Description</th>
                    <th>Current Stock</th>
                    <th>Amount Sold</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr class="product-row" data-category="">
                    <?php foreach ($arr as $s): ?>
                    <td><?= $s-> product_image?></td>
                    <td><?= $s-> product_id?></td>
                    <td><?= $s-> product_name?></td>
                    <td><?= $s-> category_image?></td>
                    <td><?= $s-> category_name?></td>
                    <td><?= $s-> price?></td>
                    <td><?= $s-> description?></td>
                    <td><?= $s-> current_stock?></td>
                    <td><?= $s-> amount_sold?></td>
                    <td><?= $s-> status?></td>

                    <td>
                        <button class="action-button" data-get="update.php?=<?= $s->$product_id?>" onclick="showUpdateProductForm('<%= product.getProductId() %>', '<%= product.getProductName() %>', '<%= product.getCategory() %>', '<%= product.getPrice() %>', '<%= product.getDescription() %>','<%= product.getQuantity() %>', '<%= product.getAmountSold() %>', '<%= product.getImage() %>')">Update</button>
                        <form action="../product" method="post" style="display: inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="productId" value="<%= product.getProductId() %>">
                            <button type="submit" class="delete-action-button" onclick="return confirmDelete();">Delete</button>
                        </form>
                    </td>
                </tr>
            </tbody>
            <?php endforeach ?>

        </table>
        <div style="margin: 30px;">
            <button id="addProductBtn" class="action-button" onclick="showAddForm()">Add new product</button>
        </div>
        

        <!-- Add Product Modal -->
        <div id="addProductModal" class="modal" style="margin-top: 80px;">
            <div class="modal-content">
                <span class="close-button" onclick="hideAddForm()">&times;</span>
                <form id="addForm" action="addProduct.php" method="POST" enctype="multipart/form-data" class="add-form">
                    <input type="hidden" name="action" value="add">
                    <label for="newProductId">Product ID:</label>
                    <input type="text" id="newProductId" name="productId"><br>

                    <label for="newProductName">Product Name:</label>
                    <input type="text" id="newProductName" name="productName"><br>

                    <label for="newCategory">Category:</label>
                    <select id="newCategory" name="category">
                        <option value="Fruits">Fruits</option>
                        <option value="Vegetables">Vegetables</option>
                        <option value="Juices">Juices</option>
                        <option value="Cold Drinks">Cold Drinks</option>
                        <option value="Meat">Meat</option>
                        <option value="Breads">Breads</option>
                    </select><br>


                    <label for="newPrice">Price:</label>
                    <input type="number" id="newPrice" name="price"><br>

                    <label for="newDescription">Description:</label>
                    <textarea id="newDescription" name="description"></textarea><br>
                    
                    <label for="newQauntity">Quantity:</label>
                    <input type="number" id="newQuantity" name="quantity"><br>

                    <label for="newAmountSold">Amount Sold:</label>
                    <input type="number" id="newAmountSold" name="amountSold"><br>

                    <label for="newImage">Image:</label>
                    <input type="file" id="newImage" name="productImage"><br>

                    <input type="submit" value="Add Product">
                    <button type="button" class="cancel-button" onclick="hideAddForm()">Cancel</button>
                </form>
            </div>
        </div>


        <!-- Update Product Modal -->
        <div id="updateModal" class="modal" style="margin-top: 80px;">
            <div class="modal-content">
                <span class="close-button" onclick="hideUpdateProductForm()">&times;</span>
                <form id="updateForm" action="../product" method="POST" enctype="multipart/form-data" class="update-form">
                    <input type="hidden" id="updateAction" name="action" value="update">
                    <input type="hidden" id="productId" name="productId">
                    <label for="productName">Product Name:</label>
                    <input type="text" id="productName" name="productName">
                    <label for="category">Category:</label>
                    <select id="category" name="category">
                        <option value="Fruits">Fruits</option>
                        <option value="Vegetables">Vegetables</option>
                        <option value="Juices">Juices</option>
                        <option value="Cold Drinks">Cold Drinks</option>
                        <option value="Meat">Meat</option>
                        <option value="Breads">Breads</option>
                    </select>

                    <label for="price">Price:</label>
                    <input type="number" id="price" name="price">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description"></textarea>
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity"><br>
                    <label for="amountSold">Amount Sold:</label>
                    <input type="number" id="amountSold" name="amountSold">
                    <label for="image">Current Image:</label>
                    <img id="currentImage" style="width: 100px; height: auto;">
                    <label for="newImage">Change Image:</label>
                    <input type="file" id="newImage" name="productImage">
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
                    input.value = '';  // Clear the input when other categories are selected
                }
            }

            function showUpdateProductForm(productImage,productId, productName, categcategory, price, productDescription, currentStock, amountSold) {
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
