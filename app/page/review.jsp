<%@page contentType="text/html" pageEncoding="UTF-8"%>
<%@ page import="entity.OrderItem" %>
<%@page import="entity.Customer" %>
<jsp:useBean id="orderItems" class="java.util.ArrayList" scope="request" type="java.util.List<entity.OrderItem>" />
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="css/review.css" />
        <link rel="icon" href="img/logo.png">
        <title>Review</title>
    </head>
    <body>
        <input type="hidden" id="status" value="<% if (request.getAttribute("status") != "") {out.print(request.getAttribute("status")); request.setAttribute("status", "");} %>">
        <% Customer customer = (Customer) session.getAttribute("customer");
            if (customer != null) {%>
        <%@ include file = "userheader.jsp" %>
        <% } else { %>
        <%@ include file = "header.jsp" %>
        <% } %>
        <h1 id="review-title">Review</h1>
        <table class="review-table">
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
                <% for (OrderItem orderItem : orderItems) { %>
                <tr>
                    <td>
                        <div class="product-info">
                            <img src="data:image/jpeg;base64, <%= orderItem.getImage() %>" alt="Product Image" width="100">
                            <span class="product-name"><%= orderItem.getProductName() %></span>
                        </div>
                    </td>
                    <td class="price"><%= orderItem.getPrice() %></td>
                    <td class="quantity"><%= orderItem.getQuantity() %></td>
                    <td class="total-price"><%= orderItem.getTotalPrice() %></td>
                    <td class="action">
                        <% if (orderItem.getStatus() == 0) { %>
                        <a class="reviewbtn" onclick="showModal('<%= orderItem.getOrderItemId() %>')"><span>Review&nbsp;&nbsp;</span><i class="ti ti-circle-filled"></i></a>
                            <% } else if (orderItem.getStatus() == 1) { %>
                        <a class="reviewbtn"><span>Review&nbsp;&nbsp;</span><i class="ti ti-check"></i></a>
                            <% } %>
                    </td>
                </tr>
                <% } %>
            </tbody>
        </table>
        <%@ include file = "footer.jsp" %>

        <div id="orderModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close" onclick="closeModal();"><i class="ti ti-x"></i></span>
                <form action="ReviewForm" method="post">
                    <input id="orderItemIdInput" type="hidden" name="orderItemId" value="">
                    <div class="form-container">
                        <div class="rating-stars">
                            <i class="star ti ti-star" onclick="setRating(1)"></i>
                            <i class="star ti ti-star" onclick="setRating(2)"></i>
                            <i class="star ti ti-star" onclick="setRating(3)"></i>
                            <i class="star ti ti-star" onclick="setRating(4)"></i>
                            <i class="star ti ti-star" onclick="setRating(5)"></i>
                        </div>
                        <input id="ratingInput" type="hidden" name="rating" value="">
                        <textarea name="comment" id="comment" rows="4" cols="50" placeholder="Leave a comment"></textarea>
                        <button class="submitbtn" type="submit">Submit Review</button>
                    </div>
                </form>
            </div>
        </div>

        <script type="text/javascript">
            function showModal(orderItemId) {
                var modal = document.getElementById('orderModal');
                var hiddenInput = document.getElementById('orderItemIdInput');
                hiddenInput.value = orderItemId;
                modal.style.display = 'block';
            }

            function closeModal() {
                var modal = document.getElementById('orderModal');
                modal.style.display = 'none';
            }

            function setRating(rating) {
                document.querySelectorAll('.rating-stars .star').forEach(function (star, index) {
                    star.style.transform = 'scale(1)'; // Reset scale for all stars
                    if (index < rating) {
                        star.classList.add('ti-star-filled');
                        star.style.transform = 'scale(1.1)'; // Scale up the filled stars
                        setTimeout(function () {
                            star.style.transform = 'scale(1)'; // Scale back after 0.3s
                        }, 300); // Animation duration 300 ms
                    } else {
                        star.classList.remove('ti-star-filled');
                    }
                });
                document.getElementById('ratingInput').value = rating;
            }

        </script>
        
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script type="text/javascript">
                            let status = document.getElementById("status").value;

                            if (status === "reviewSuccess") {
                                swal.fire("Congratulations", "Review submitted successfully.", "success");
                            }
                            if (status === "reviewFail") {
                                swal.fire("Sorry", "Review submit failed.", "error");
                            }

        </script>        
    </body>
</html>
