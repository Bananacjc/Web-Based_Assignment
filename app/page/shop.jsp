<%@page contentType="text/html" pageEncoding="UTF-8"%>
<%@page import="java.util.*" %>
<%@page import="entity.Product" %>
<%@page import="entity.Customer" %>
<%@page import="entity.ProductComment" %>
<%@page import="entity.SelectedProduct" %>
<jsp:useBean id="products" class="java.util.ArrayList" type="java.util.List<entity.Product>" scope="request" />
<%
    String[] categories = {"Fruits", "Vegetables", "Juices", "Meat", "Cold Drinks", "Breads"};
%>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="css/shop.css" />
        <link rel="icon" href="img/logo.png">
        <title>Shop</title>
    </head>
    <body>
        <% Customer customer = (Customer) session.getAttribute("customer");
            if (customer != null) {%>
        <%@ include file = "userheader.jsp" %>
        <% } else { %>
        <%@ include file = "header.jsp" %>
        <% } %>
        <h1 id="shop-title">Shop</h1>
        <div id="container">
            <div id="sidebar-container">
                <div class="sidebar">
                    <div id="searchbar-container">
                        <i class="ti ti-search"></i>
                        <input type="text" placeholder="Search" class="searchbar">
                    </div>
                    <h3>Categories</h3>
                    <ul>
                        <li><a href="#fruits">Fruits</a></li>
                        <li><a href="#vegetables">Vegetables</a></li>
                        <li><a href="#juices">Juices</a></li>
                        <li><a href="#meat">Meat</a></li>
                        <li><a href="#cold-drinks">Cold Drinks</a></li>
                        <li><a href="#breads">Breads</a></li>
                    </ul>
                    <h3>Filter by Price</h3>
                    <input type="range" min="0" max="200" value="100" class="slider" id="price-slider">
                    <span id="price-value">RM 0 - RM 200</span>
                </div>
            </div>
            <div id="main-content">
                <!-- Repeat this block for each product -->
                <% for (String category : categories) { %>
                <h2 class="category-title" id="<%= category.toLowerCase() %>"><%= category %></h2>
                <% for (Product product : products) {
            if (product.getCategory().equals(category)) { %>
                <div class="product-card">
                    <a href="ProductDetail?productId=<%= product.getProductId() %>" class="product-detail-link">
                        <img src="data:image/jpeg;base64,<%= product.getImage() %>" alt="<%= product.getProductName() %>">
                        <div class="product-info">
                            <h4 class="product-name"><%= product.getProductName() %></h4>
                            <p class="rating">Rating: ★★★☆☆</p>
                            <p class="price-tag">Price: RM <%= String.format("%.2f", product.getPrice()) %></p>
                        </div>
                    </a>
                    <form action="OrderServlet" method="post">
                        <input name="url" value="cart" type="hidden">
                        <input type="hidden" name="productId" value="<%= product.getProductId() %>">
                        <input type="hidden" name="action" value="add">
                        <button type="submit">Add to Cart</button>
                    </form>
                </div>
                <%}}}%>
            </div>
        </div>
        <%@ include file="footer.jsp" %>

        <% SelectedProduct selectedProduct = (SelectedProduct) request.getAttribute("selectedProduct");
            if (selectedProduct != null) { 
        %>
        <div id="productModal" class="modal" style="display:block;">
            <div class="modal-content">
                <div class="product-detail-container">
                    <img src="data:image/jpeg;base64,<%= selectedProduct.getImage() %>" alt="<%= selectedProduct.getProductName() %>" width="250" height="250" />
                    <div class="product-detail-subcontainer">
                        <span class="close" onclick="closeModal();"><i class="ti ti-x"></i></span>
                        <h4 class="selected-product-name"><%= selectedProduct.getProductName() %></h4>
                        <div class="rating-and-sold">
                            <p class="average-rating-stars">
                                <% 
                                    double rating = selectedProduct.getAverageRating();
                                    int fullStars = (int) rating;
                                    boolean hasHalfStar = (rating % 1.0 >= 0.5);
                                    int emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

                                    for (int i = 0; i < fullStars; i++) { 
                                %>
                                <i class="ti ti-star-filled"></i>
                                <% } 
                                    if (hasHalfStar) { 
                                %>
                                <i class="ti ti-star-half-filled"></i>
                                <% }
                                    for (int i = 0; i < emptyStars; i++) { 
                                %>
                                <i class="ti ti-star"></i>
                                <% } 
                                %>
                            </p>
                            <p class="rating-amount"><%= selectedProduct.getRatingAmount() %> Ratings</p>
                            <p class="amount-sold"><%= selectedProduct.getAmountSold() %> sold</p>
                        </div>
                        <p class="price">RM <%= selectedProduct.getPrice() %></p>
                        <p class="description"><%= selectedProduct.getDescription() %></p>
                    </div>
                </div>
                <div class="comment-container">
                    <h4 class="comments-title">Comments</h4>
                    <%
                        List<ProductComment> productComments = (List<ProductComment>) request.getAttribute("productComments");
                        if (productComments != null && !productComments.isEmpty()) {
                    %>
                    <% for (ProductComment comment : productComments) { %>
                    <div class="comments">
                        <img class="profile-pic" src="data:image/jpeg;base64,<%= comment.getImage() %>" alt="<%= comment.getCustomerName() %>" width="50px" height="50px" />
                        <div class="comments-detail-container">
                            <p class="user-name"><%= comment.getCustomerName() %></p>
                            <p class="rating-stars">
                                <% 
                                    rating = comment.getRating();
                                    fullStars = (int) rating;
                                    hasHalfStar = (rating % 1.0 >= 0.5);
                                    emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

                                    for (int i = 0; i < fullStars; i++) { 
                                %>
                                <i class="ti ti-star-filled"></i>
                                <% } 
                                    if (hasHalfStar) { 
                                %>
                                <i class="ti ti-star-half-filled"></i>
                                <% }
                                    for (int i = 0; i < emptyStars; i++) { 
                                %>
                                <i class="ti ti-star"></i>
                                <% } 
                                %>
                            </p>
                            <p class="date-time"><%= comment.getCommentDateTime() %></p>
                            <p class="comment"><%= comment.getComment() %></p>
                        </div>
                    </div>
                    <% } %>
                </div>
                <% } else { %>
                <h4>No comment</h4>
                <% } %>
            </div>
        </div>
        <% } %>

        <script src="js/slider.js"></script>
        <script src="js/searchproducts.js"></script>
        <script src="js/categoryFilter.js"></script>
        <script type="text/javascript">
                            function closeModal() {
                                var modal = document.getElementById('productModal');
                                modal.style.display = 'none';
                            }
        </script>
    </body>
</html>