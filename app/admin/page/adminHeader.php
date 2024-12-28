<?php
// Get the page title from the query parameter or set a default value
$pageTitle = isset($_GET['title']) ? htmlspecialchars($_GET['title']) : 'Dashboard';
$_css = '../css/base.css';
require '../_base.php';
require_login();
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../css/adminHead.css" />
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <title><?php echo $pageTitle; ?></title> <!-- Set the page title -->

</head>
<?php
$userRole = ['MANAGER', 'STAFF'];
$user3Role = ['MANAGER', 'STAFF', 'DELIVERY_GUY'];

?>

<body>

    <?php
    $info = temp('info');
    if (!empty($info)) {
        echo "<div id='info'>" . (is_array($info) ? implode(", ", $info) : $info) . "</div>";
    }

    $error = temp('error');
    if (!empty($error)) {
        echo "<div id='error'>" . (is_array($error) ? implode("<br>", $error) : $error) . "</div>";
    }
    ?>




    <input type="checkbox" id="nav-toggle" hidden>

    <div id="sidebar" class="sidebar">
        <div class="h-100">
            <div class="sidebar-logo">
                <a href="adminDashboard.php?title=Dashboard">
                    <img src="../../images/logo.png" alt="Logo" width="25" height="25" />
                    <span class="label">BananaSis</span>
                </a>
            </div>

            <ul class="sidebar-nav">
                <li class="sidebar-header">
                    <span class="label">Admin Panel</span>
                </li>
                <?php if (in_array($_user?->role, $userRole)): ?>
                    <li class="sidebar-item">
                        <a href="adminDashboard.php?title=Dashboard" class="sidebar-link">
                            <span class="icon">📋</span>
                            <span class="label">Dashboard</span>
                        </a>
                    <?php endif ?>

                    </li>

                    <li class="sidebar-item">

                        <?php if (in_array($_user?->role, $userRole)): ?>
                            <a href="product.php?title=Product" class="sidebar-link">
                                <span class="icon">🏷️</span>
                                <span class="label">Product</span>
                            </a>
                        <?php endif ?>
                    </li>
                    <?php if (in_array($_user?->role, $user3Role)): ?>
                        <li class="sidebar-item">
                            <a href="orderStatus.php?title=Order Status" class="sidebar-link">
                                <span class="icon">📊</span>
                                <span class="label">Order Status</span>
                            </a>
                        </li>
                    <?php endif ?>

                    <?php if (in_array($_user?->role, $userRole)): ?>
                        <li class="sidebar-item">
                            <a href="customer.php?title=Customer" class="sidebar-link">
                                <span class="icon">👥</span>
                                <span class="label">Customer</span>
                            </a>
                        </li>
                    <?php endif ?>
                    <?php if ($_user?->role == 'MANAGER'): ?>
                        <li class="sidebar-item">
                            <a href="staff.php?title=Staff" class="sidebar-link">
                                <span class="icon">👤</span>
                                <span class="label">Staff</span>
                            </a>
                        </li>
                    <?php endif ?>

                    <?php if (in_array($_user?->role, $userRole)): ?>
                        <li class="sidebar-item">
                            <a href="promotionVoucher.php?title=Promotion" class="sidebar-link">
                                <span class="icon">🎫</span>
                                <span class="label">Promotion Voucher</span>
                            </a>
                        <?php endif ?>

                        <?php if (in_array($_user?->role, $userRole)): ?>
                        <li class="sidebar-item">
                            <a href="category.php?title=Product Category" class="sidebar-link">
                                <span class="icon">🏷️</span>
                                <span class="label">Product Category</span>
                            </a>
                        <?php endif ?>


                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link" onclick="toggleSubMenu()">
                                <span class="icon">⚙️</span>
                                <span class="label">Settings</span>
                            </a>
                            <ul id="settings-menu" class="nested-menu">
                                <li class="nested-item">
                                    <a href="accountProfile.php?title=Account" class="nested-link">Account</a>
                                </li>
                                <li class="nested-item">
                                    <a href="logout.php" class="nested-link">Log Out</a>
                                </li>
                            </ul>
                        </li>
            </ul>
        </div>
    </div>

    <div class="main-content">

        <header>
            <h1>
                <label for="nav-toggle">
                    <span class="las la-bars"></span>
                </label> <?php echo $pageTitle; ?>
            </h1>

            <div class="search-wrapper">
                <span class="las la-search"></span>
                <input type="search" placeholder="Search Here" />
            </div>
            <div class="user-wrapper">
                <?php if ($_user): ?>
                    <a href="accountProfile.php">

                        <img src="../uploads/profile_images/<?= $_user->profile_image ?>" alt="Profile Picture" />
                    </a>
                    <div>
                        <?= $_user->employee_name ?><br>
                        <?= $_user->role ?>
                    </div>

                <?php endif ?>
            </div>
        </header>
    </div>
</body>

</html>