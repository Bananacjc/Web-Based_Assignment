<?php
// Get the page title from the query parameter or set a default value
$pageTitle = isset($_GET['title']) ? htmlspecialchars($_GET['title']) : 'Dashboard';
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../css/adminHead.css" />
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <title><?php echo $pageTitle; ?></title> <!-- Set the page title -->
</head>

<body>
    <input type="checkbox" id="nav-toggle" hidden>

    <div id="sidebar" class="sidebar">
        <div class="h-100">
            <div class="sidebar-logo">
                <a href="adminDashboard.php?title=Dashboard">
                    <img src="../images/logo.png" alt="Logo" width="25" height="25" />
                    <span class="label">BananaSis</span>
                </a>
            </div>

            <ul class="sidebar-nav">
                <li class="sidebar-header">
                    <span class="label">Admin Panel</span>
                </li>

                <li class="sidebar-item">
                    <a href="adminDashboard.php?title=Dashboard" class="sidebar-link">
                        <span class="icon">📋</span>
                        <span class="label">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="product.php?title=Product" class="sidebar-link">
                        <span class="icon">🏷️</span>
                        <span class="label">Product</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="orderStatus.php?title=Order Status" class="sidebar-link">
                        <span class="icon">📊</span>
                        <span class="label">Order Status</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="customer.php?title=Customer" class="sidebar-link">
                        <span class="icon">👥</span>
                        <span class="label">Customer</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="staff.php?title=Staff" class="sidebar-link">
                        <span class="icon">👤</span>
                        <span class="label">Staff</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link" onclick="toggleSubMenu()">
                        <span class="icon">⚙️</span>
                        <span class="label">Settings</span>
                    </a>
                    <ul id="settings-menu" class="nested-menu">
                        <li class="nested-item">
                            <a href="account.php?title=Account" class="nested-link">Account</a>
                        </li>
                        <li class="nested-item">
                            <a href="logout.php?title=Log Out" class="nested-link">Log Out</a>
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
                <img src="" width="40px" height="40px" alt="">
                <div>
                    <h4>Chuntian</h4>
                    <small>SuperAdmin</small>
                </div>
            </div>
        </header>
    </div>
</body>

</html>
