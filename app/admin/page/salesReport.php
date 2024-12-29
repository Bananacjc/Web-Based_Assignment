<?php
include 'adminHeader.php';

$selectedYear = req('year') ?? date('Y');
$selectedMonth = req('month') ?? 'all';
$selectedDay = req('day') ?? 'all';

$monthCondition = $selectedMonth === 'all' ? "" : "AND MONTH(order_time) = $selectedMonth";
$dayCondition = $selectedDay === 'all' ? "" : "AND DAY(order_time) = $selectedDay";

$totalQuery = "SELECT COUNT(*) AS total_orders, SUM(total) AS total_sales 
               FROM orders 
               WHERE YEAR(order_time) = $selectedYear $monthCondition $dayCondition";
$totalData = $_db->query($totalQuery)->fetch(PDO::FETCH_ASSOC);

$statusQuery = "SELECT status, COUNT(*) AS order_count 
                FROM orders 
                WHERE YEAR(order_time) = $selectedYear $monthCondition $dayCondition 
                GROUP BY status";
$statusResult = $_db->query($statusQuery);

$discountQuery = "SELECT SUM(promo_amount) AS total_discounts 
                  FROM orders 
                  WHERE YEAR(order_time) = $selectedYear $monthCondition $dayCondition";
$discountData = $_db->query($discountQuery)->fetch(PDO::FETCH_ASSOC);

$itemsQuery = "SELECT order_items FROM orders WHERE YEAR(order_time) = $selectedYear $monthCondition $dayCondition";
$itemsResult = $_db->query($itemsQuery);

$totalItemCount = 0;

while ($row = $itemsResult->fetch(PDO::FETCH_ASSOC)) {
    $orderItems = json_decode($row['order_items'], true);
    if (is_array($orderItems)) {
        foreach ($orderItems as $item) {
            $totalItemCount += $item['quantity'];
        }
    }
}


?>

<!DOCTYPE html>
<html>

<head>
    <title>Sales Report</title>
</head>

<body>

<head>
    <style>
body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }

      

        h1 {
            font-size: 2.5em;
            margin: 50px;

        }

        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .form-container {
            margin-bottom: 20px;
        }

        .form-container label {
            margin-right: 10px;
            font-weight: 500;
        }

        select {
            padding: 5px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #fafafa;
        }

        .section-title {
            font-size: 1.8em;
            color: #007BFF;
            margin-bottom: 10px;
        }

        .summary, .status, .discounts, .items {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f8f9fa;
            color: #007BFF;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .footer {
            text-align: center;
            padding: 15px;
            background-color: #f1f1f1;
            margin-top: 40px;
        }
    </style>
</head>

    <div class="main">
        <h1>Sales Report for
            <?= $selectedDay === 'all' ? ($selectedMonth === 'all' ? "Year $selectedYear" : "Month $selectedMonth, Year $selectedYear") : "Day $selectedDay, Month $selectedMonth, Year $selectedYear" ?>
        </h1>

        <form method="GET">
            <label for="year">Year:</label>
            <select name="year" id="year" onchange="this.form.submit()">
                <?php for ($year = 2023; $year <= date('Y'); $year++): ?>
                    <option value="<?= $year ?>" <?= $year == $selectedYear ? 'selected' : '' ?>><?= $year ?></option>
                <?php endfor; ?>
            </select>

            <label for="month">Month:</label>
            <select name="month" id="month" onchange="this.form.submit()">
                <option value="all" <?= $selectedMonth === 'all' ? 'selected' : '' ?>>All Months</option>
                <?php for ($month = 1; $month <= 12; $month++): ?>
                    <option value="<?= $month ?>" <?= $month == $selectedMonth ? 'selected' : '' ?>>
                        <?= date('F', mktime(0, 0, 0, $month, 1)) ?>
                    </option>
                <?php endfor; ?>
            </select>

            <label for="day">Day:</label>
            <select name="day" id="day" onchange="this.form.submit()">
                <option value="all" <?= $selectedDay === 'all' ? 'selected' : '' ?>>All Days</option>
                <?php for ($day = 1; $day <= 31; $day++): ?>
                    <option value="<?= $day ?>" <?= $day == $selectedDay ? 'selected' : '' ?>><?= $day ?></option>
                <?php endfor; ?>
            </select>
        </form>

        <h2>Total Sales Overview</h2>
        <p>Total Orders: <?= $totalData['total_orders'] ?></p>
        <p>Total Sales: RM<?= $totalData['total_sales'] ?: 0 ?></p>

        <h2>Order Status</h2>
        <ul>
            <?php while ($row = $statusResult->fetch(PDO::FETCH_ASSOC)): ?>
                <li><?= $row['status'] ?>: <?= $row['order_count'] ?> orders</li>
            <?php endwhile; ?>
        </ul>

        <h2>Total Discounts</h2>
        <p>Total Discounts Applied: RM<?= $discountData['total_discounts'] ?: 0 ?></p>

        <p>Total Items Sold: <?= $totalItemCount ?></p>

    </div>
</body>

</html>