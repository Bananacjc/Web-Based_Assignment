<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<?php
 include "adminHeader.php"; 
$_css1='../base.css';
$_css ='../css/adminDashboard.css';
$_title='Admin Dashboard';
$topSellingQuery = "
    SELECT p.product_image, p.product_id, p.product_name, c.category_name, p.price, p.amount_sold
    FROM products p
    JOIN categories c ON p.category_name = c.category_name
    ORDER BY p.amount_sold DESC
    LIMIT 5";

$topSellingStm = $_db->prepare($topSellingQuery);
$topSellingStm->execute();
$topSellingProducts = $topSellingStm->fetchAll();
?>

<body>

<div class='main'>

<div class="top-selling-products">
    <h2>Top 5 Selling Products</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>Product Image</th>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Amount Sold</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($topSellingProducts as $p): ?>
                <tr>
                    <td>
                        <img src="../../uploads/product_images/<?= $p->product_image ?>" class="resized-image">
                    </td>
                    <td><?= $p->product_id ?></td>
                    <td><?= $p->product_name ?></td>
                    <td><?= $p->category_name ?></td>
                    <td><?= $p->price ?></td>
                    <td><?= $p->amount_sold ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</div>

                    <!-- Other cards -->
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Customers</h4>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Date Joined</th>
                                                <th>Phone</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <% for (Users users: usersList) { %>
                                            <tr>
                                                <td><%= users.getUserid() %></td>
                                                <td><%= users.getUsername() %></td>
                                                <td><%= users.getUserdate() %></td>
                                                <td><%= users.getUserphonenumber() %></td>
                                            </tr>
                                            <% } %>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </main>
        </div>
    </div>
</html>