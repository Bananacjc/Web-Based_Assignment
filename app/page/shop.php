<!DOCTYPE html>
<html>
<?php
require '../_base.php';
$_title = 'Shop';
$_css = '../css/shop.css';
include '../_head.php';
?>
    
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
              
                <h2 class="category-title" id="<%= category.toLowerCase() %>"><%= category %></h2>
           
                <div class="product-card">
                    <a href="ProductDetail?productId=<%= product.getProductId() %>" class="product-detail-link">
                        <img src="data:image/jpeg;base64,<%= product.getImage() %>" alt="<%= product.getProductName() %>">
                        <div class="product-info">
                            <h4 class="product-name"></h4>
                            <p class="rating">Rating: ★★★☆☆</p>
                            <p class="price-tag">Price: RM </p>
                        </div>
                    </a>
                    <form action="OrderServlet" method="post">
                        <input name="url" value="cart" type="hidden">
                        <input type="hidden" name="productId" value="<%= product.getProductId() %>">
                        <input type="hidden" name="action" value="add">
                        <button type="submit">Add to Cart</button>
                    </form>
                </div>
     
            </div>
        </div>
    
        <div id="productModal" class="modal" style="display:block;">
            <div class="modal-content">
                <div class="product-detail-container">
                    <img src="data:image/jpeg;base64,<%= selectedProduct.getImage() %>" alt="<%= selectedProduct.getProductName() %>" width="250" height="250" />
                    <div class="product-detail-subcontainer">
                        <span class="close" onclick="closeModal();"><i class="ti ti-x"></i></span>
                        <h4 class="selected-product-name"></h4>
                        <div class="rating-and-sold">
                            <p class="average-rating-stars">
                           
                                <i class="ti ti-star-filled"></i>
                           
                                <i class="ti ti-star-half-filled"></i>
                             
                                <i class="ti ti-star"></i>
                            
                            </p>
                            <p class="rating-amount"></p>
                            <p class="amount-sold">sold</p>
                        </div>
                        <p class="price">RM </p>
                        <p class="description"></p>
                    </div>
                </div>
                <div class="comment-container">
                    <h4 class="comments-title">Comments</h4>
                  
                  
                    <div class="comments">
                        <img class="profile-pic" src="data:image/jpeg;base64,<%= comment.getImage() %>" alt="<%= comment.getCustomerName() %>" width="50px" height="50px" />
                        <div class="comments-detail-container">
                            <p class="user-name"><%= comment.getCustomerName() %></p>
                            <p class="rating-stars">
                              
                                <i class="ti ti-star-filled"></i>
                             
                                <i class="ti ti-star-half-filled"></i>
                             
                                <i class="ti ti-star"></i>
                              
                            </p>
                            <p class="date-time"></p>
                            <p class="comment"></p>
                        </div>
                    </div>
                  
                </div>
              
                <h4>No comment</h4>
              
            </div>
        </div>
     

        <script src="js/slider.js"></script>
        <script src="js/searchproducts.js"></script>
        <script src="js/categoryFilter.js"></script>
        <script type="text/javascript">
                            function closeModal() {
                                var modal = document.getElementById('productModal');
                                modal.style.display = 'none';
                            }
        </script>

<?php include '../_foot.php'; ?>

    </body>
</html>