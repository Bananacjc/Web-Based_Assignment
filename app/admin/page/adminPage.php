<%@page contentType="text/html" pageEncoding="UTF-8"%
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="css/adminPage.css" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Dashboard Staff</title>
    </head>
    <%@ include file = "adminHeader.jsp" %>
    <body>
        <div class="dashboard-container">
            <div class="dashboard-title-container">
                <h1>Dashboard</h1>
            </div>
            <div class="dashboard-subcontainer">
                <div class="dashboard-item">
                    <div class="icon-bg blue"><i class="ti ti-shopping-cart"></i></div>
                    <div class="total-detail">
                        <h4>Total Products</h4>
                        <p>${totalProduct}</p>
                    </div>
                </div>
                <div class="dashboard-item">
                    <div class="icon-bg light-blue"><i class="ti ti-user"></i></div>
                    <div class="total-detail">
                        <h4>Total Customers</h4>
                        <p>${totalCustomer}</p>
                    </div>
                </div>
                <div class="dashboard-item">
                    <div class="icon-bg dark-blue"><i class="ti ti-cash"></i></div>
                    <div class="total-detail">
                        <h4>Total Sales</h4>
                        <p>RM ${totalSales}</p>
                    </div>
                </div>
            </div>


            <div class="top-sales">
                <h1 class="top-sales-title">Top 10 Sales</h1>
                <table class="top-sales-table">
                    <tr>
                        <th class="align-left">Product Image</th>
                        <th class="align-left">Product ID</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th class="align-right">Price (RM)</th>
                        <th class="align-right">Amount Sold</th>
                    </tr>
                    <% for (Product product : topSalesList) { %>
                    <tr>
                        <td class="align-left"><img src="data:image/jpeg;base64,<%= product.getImage() %>" alt="" width="80" height="80" ></td>
                        <td class="align-left"><%= product.getProductId() %></td>
                        <td><%= product.getProductName() %></td>
                        <td><%= product.getCategory() %></td>
                        <td  class="align-right"><%= product.getPriceFormatted() %></td>
                        <td  class="align-right"><%= product.getAmountSold() %></td>
                    </tr>
                    <% } %>
                </table>
            </div>
        </div>
    </body>
</html>
