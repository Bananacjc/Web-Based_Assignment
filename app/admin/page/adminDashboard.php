<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
    <title>Admin Dashboard</title>

<head>
    <style>
        /*set the table to be scrollable*/
        .data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .data-table thead {
            background-color: #f1f1f1;
        }

        .data-table {
            display: block;
            max-height: 500px;
            overflow-y: auto;
            overflow-x: hidden;
            width: 100%;
        }

        .data-table th,
        .data-table td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
    </style>
</head>
<?php
include "adminHeader.php";


$topSellingQuery = "
    SELECT p.product_image, p.product_id, p.product_name, c.category_name, p.price, p.amount_sold
    FROM products p
    JOIN categories c ON p.category_name = c.category_name
    ORDER BY p.amount_sold DESC
    LIMIT 5";

$topSellingStm = $_db->prepare($topSellingQuery);
$topSellingStm->execute();
$topSellingProducts = $topSellingStm->fetchAll();

$search = req('search');
$searchQuery = "%$search%";

$actionLogQuery = "
    SELECT a.log_id, e.employee_id, a.action_type, a.action_details, a.action_date
    FROM actionlog a
    JOIN employees e ON a.employee_id = e.employee_id
     WHERE e.employee_id LIKE ?
    ORDER BY a.action_date DESC";
$actionLogStm = $_db->prepare($actionLogQuery);
$actionLogStm->execute([$searchQuery]);
$actionLogs = $actionLogStm->fetchAll(PDO::FETCH_ASSOC);
$userRole=['MANAGER','STAFF'];
?>

<body>

    <div class='main'>

        <div class="sub-main">
            <div class="top-selling-products">
            <?php if (in_array($_user?->role, $userRole)): ?>
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
<?php endif; ?>
        </div>

        <?php if ($_user?->role == 'MANAGER'): ?>

            <div class="sub-main">
                <div class="action-log">
                    <div class="top-selling-products">

                        <h2>Recent Action Logs</h2>
                        <form method="post" id="f">
                            <button class="delete-btn" formaction="deleteAction.php" onclick="return confirmDelete()">Batch Delete</button>
                        </form>

                        <form>
                            <input type="search" name="search" placeholder="Search by Employee ID" />
                            <button type="submit">Search</button>
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
                                                <button type="submit" class="delete-btn" onclick="return confirmDelete();">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
    <script>
        function confirmDelete() {
            return confirm('Are you sure you want to delete this action log?');
        }
    </script>

</html>