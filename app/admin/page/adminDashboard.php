<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<?php 
$_css ='../css/adminDashboard.css';
$_title='Admin Dashboard';
require '../_base.php';



?>



<body>

    <?php include "adminHeader.php" ?>

    <div class="wrapper">
        <div class="main">
            <main class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-md-flex align-items-center">
                                        <div>
                                            <h4 class="card-title">Top Selling Products</h4>
                                            <p class="card-subtitle">Overview of Top Selling Items</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr class="bg-light">
                                                <th>Products</th>
                                                <th>License</th>
                                                <th>Support Agent</th>
                                                <th>Technology</th>
                                                <th>Tickets</th>
                                                <th>Sales</th>
                                                <th>Earnings</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Elite Admin</td>
                                                <td>Single Use</td>
                                                <td>John Doe</td>
                                                <td><label class="label label-danger">Angular</label></td>
                                                <td>46</td>
                                                <td>356</td>
                                                <td>$2850.06</td>
                                            </tr>
                                            <!-- Add other rows here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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