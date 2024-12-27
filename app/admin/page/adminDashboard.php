<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<?php
include "adminHeader.php";
$_css = '../css/adminDashboard.css';
$topSellingQuery = "
    SELECT p.product_image, p.product_id, p.product_name, c.category_name, p.price, p.amount_sold
    FROM products p
    JOIN categories c ON p.category_name = c.category_name
    ORDER BY p.amount_sold DESC
    LIMIT 5";

$topSellingStm = $_db->prepare($topSellingQuery);
$topSellingStm->execute();
$topSellingProducts = $topSellingStm->fetchAll();

$actionLogQuery = "
    SELECT a.log_id, e.employee_id, a.action_type, a.action_details, a.action_date
    FROM actionlog a
    JOIN employees e ON a.employee_id = e.employee_id
    ORDER BY a.action_date DESC";


$actionLogStm = $_db->prepare($actionLogQuery);
$actionLogStm->execute();
$actionLogs = $actionLogStm->fetchAll(PDO::FETCH_ASSOC);



?>

<body>



    <div class='main'>

        <div class="sub-main">
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
        <div class="sub-main">
            <div class="action-log">
                <div class="top-selling-products">

                    <h2>Recent Action Logs</h2>
                    <form method="post" id="f">
                        <button formaction="deleteAction.php" onclick="return confirmDelete()">Delete</button>
                    </form>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Log ID</th>
                                <th>Employee ID</th>
                                <th>Action Type</th>
                                <th>Action Details</th>
                                <th>Action Date</th>
                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($actionLogs as $l): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox"
                                            name="id[]"
                                            value="<?= $l['log_id'] ?>"
                                            form="f">
                                    </td>
                                    <td><?= $l['log_id'] ?></td>
                                    <td><?= $l['employee_id'] ?></td>
                                    <td><?= $l['action_type'] ?></td>
                                    <td><?= $l['action_details'] ?></td>
                                    <td><?= $l['action_date'] ?></td>
                                    <td>
                                        <form action="deleteAction.php" method="post" style="display:inline;">
                                            <input type="hidden" name="id" value="<?= $l['log_id'] ?>">
                                            <button type="submit" class="button delete-action-button" onclick="return confirmDelete();">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>
    <script>
        function confirmDelete() {
            return confirm('Are you sure you want to delete this action log?');
        }
    </script>

</html>