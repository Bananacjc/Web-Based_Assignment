<%@page contentType="text/html" pageEncoding="UTF-8"%>
<%@page import="entity.*, java.util.List" %>
<%--<jsp:useBean id="product" class="entity.Products" scope="session" />--%>
<!DOCTYPE html>
<html>
    <head>
<!--        <style>
            .unavailable-product {
                background-color: #f2f2f2; /* Light gray background */
                color: #666; /* Dark gray text color */
                opacity: 0.7; /* Reduce opacity to indicate disabled state */
            }
            .disabled-icon {
                pointer-events: none; /* Disable pointer events */
                opacity: 0.5; /* Reduce opacity to visually indicate disabled state */
                cursor: not-allowed; /* Change cursor to indicate that the icon is not clickable */
            }
        </style>-->
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="css/cart.css" />
        <link rel="icon" href="img/logo.png">
        <title>Cart</title>
    </head>
    <body>
        <% Customer customer = (Customer) session.getAttribute("customer");
            if (customer != null) {%>
        <%@ include file = "userheader.jsp" %>
        <% } else { %>
        <%@ include file = "header.jsp" %>
        <% } %>
<?php
require '../base.php';
$_title = 'Cart';
$_css = '../css/cart.css';
include '../head.php';
?>
        <h1 id="cart-title">Cart</h1>
        <table class="cart-table">
            <thead>
                <tr>
                    <th class="product-header">PRODUCT</th>
                    <th>PRICE (RM)</th>
                    <th>QUANTITY</th>
                    <th>TOTAL (RM)</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
                <!-- Rows for each product in the cart -->
                <%          List<Orderitems> orderItems = (List<Orderitems>) request.getAttribute("orderItemList");
                            List<Product> products = (List<Product>) request.getAttribute("products");
                            if (orderItems != null) {
                    for(Orderitems orderItem : orderItems) {
                    for(Product product : products) {
                    if (orderItem.getProductId().equals(product.getProductId())) {
//                    if (product.getStatus == ProductStatus.UNVAILABLE.getValue()) {                   
                %>
                <tr class="unavailable-product">
                    <td>
                        <!-- Image and name of the product -->
                        <div class="product-info">
                            <img src="img/c-img-1.webp" alt="Product Image" />
                            <span class="product-name"><%= product.getProductName()%></span>
                        </div>
                    </td>
                    <td class="price"><%= product.getPrice()%></td>
                    <td class="quantity">
                        <!-- Quantity control -->
                        <div class="quantity-control">
                            <form id="minusForm<%= orderItem.getOrderItemId()%>" action="OrderServlet" method="POST">
                                <input name="url" value="cart" type="hidden">
                                <input name="action" value="update" type="hidden">
                                <input name="orderItemId" value="<%= orderItem.getOrderItemId()%>" type="hidden">
                                <input name="m" value="minus" type="hidden">
                                <i class="ti ti-minus" onclick="submitForm('minusForm<%= orderItem.getOrderItemId()%>')"></i>
                            </form>
                            <span class="quantity-value"><%= orderItem.getQuantity()%></span>
                            <form id="plusForm<%= orderItem.getOrderItemId()%>" action="OrderServlet" method="POST">
                                <input name="url" value="cart" type="hidden">
                                <input name="action" value="update" type="hidden">
                                <input name="orderItemId" value="<%= orderItem.getOrderItemId()%>" type="hidden">
                                <input name="m" value="plus" type="hidden">
                                <i class="ti ti-plus" onclick="submitForm('plusForm<%= orderItem.getOrderItemId()%>')"></i>
                            </form>
                        </div>
                    </td>
                    <td class="total-price"><%= orderItem.getTotalPrice(product.getPrice(), orderItem.getQuantity()) %></td>
                    <td class="action">
                        <!-- Remove icon for action -->
                        <form id="removeForm<%= orderItem.getOrderItemId()%>" action="OrderServlet" method="POST">
                            <input name="url" value="cart" type="hidden">
                            <input name="action" value="delete" type="hidden">
                            <input name="orderItemId" value="<%= orderItem.getOrderItemId()%>" type="hidden">
                            <i class="ti ti-x" onclick="submitForm('removeForm<%= orderItem.getOrderItemId()%>')"></i>
                        </form>
                    </td>
                </tr>
                <%// } else { %>
<!--                <tr>
                    <td>
                         Image and name of the product 
                        <div class="product-info">
                            <img src="img/c-img-1.webp" alt="Product Image" />
                            <span class="product-name"><%= product.getProductName()%></span>
                        </div>
                    </td>
                    <td class="price"><%= product.getPrice()%></td>
                    <td class="quantity">
                         Quantity control 
                        <div class="quantity-control">
                            <a href="OrderServlet?action=update&orderId=<%= orderItem.getOrderId()%>&m=minus"></a><i class="ti ti-minus"></i></a>
                            <span class="quantity-value"><%= orderItem.getQuantity()%></span>
                            <a href="OrderServlet?action=update&orderId=<%= orderItem.getOrderId()%>&m=plus"><i class="ti ti-plus"></i></a>
                        </div>
                    </td>
                    <td class="total-price"><%= orderItem.getTotalPrice(product.getPrice(), orderItem.getQuantity()) %></td>
                    <td class="action">
                         Remove icon for action 
                        <a href="OrderServlet?action=delete&orderId=<%= orderItem.getOrderId()%>"><i class="ti ti-x"></i></a>
                    </td>
                </tr>-->
                <% // } 
                              
                            }
                     
                      }
                    }
}
                %> 
            </tbody>
        </table>
        <div class="button-container">
            <form action="OrderServlet" method="POST">
                <input name="url" value="cart" type="hidden">
                <input name="action" value="clear" type="hidden">
            <button id="clearbtn" type="submit">Clear Cart</button>
            </form>
            <button id="paymentbtn" onclick="goToPayment()">Proceed to Payment</button>
        </div>
        <%@ include file = "footer.jsp" %>
       <script>
           function submitForm(formId) {
        // Perform validation or other tasks before submitting
        var form = document.getElementById(formId);
        form.submit(); // This will submit the form
    }
        function goToPayment() {
            window.location.href = "OrderServlet?url=payment";
        }
    </script>
    </body>
</html>
