<%@page contentType="text/html" pageEncoding="UTF-8"%>
<%@page import="java.util.*, entity.Promotion" %>
<jsp:include page="/promotion" />

<% List<Promotion> promotions = (List<Promotion>) session.getAttribute("promotionList"); %>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="css/adminPromotion.css" />
        <script src="js/adminPromotion.js"></script>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>PROMOTION</title>
    </head>
    <%@ include file = "adminHeader.jsp" %>
   
    <body>  
        <h1>PROMOTION</h1>
        <table id="promoTable" class="data-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Promo ID</th>
                    <th>Promo Name</th>
                    <th>Promo Code</th>
                    <th>Requirement (RM)</th>
                    <th>Promo Amount (RM)</th>
                    <th>Description</th>
                    <th>Limit Usage</th>
                    <th>Duration</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <% for (Promotion promotion : promotions) { %>
                <tr>
                    <% String image = promotion.getPromoImage();%>
                    <td><img src="data:image/jpeg;base64,<%= promotion.getPromoImage() %>" alt="<%= promotion.getPromoName() %>"></td>
                    <td><%= promotion.getPromoId() %></td>
                    <td><%= promotion.getPromoName() %></td>
                    <td><%= promotion.getPromoCode() %></td>
                    <td><%= promotion.getRequirement() %></td>
                    <td><%= promotion.getPromoAmount() %></td>
                    <td><%= promotion.getDescription() %></td>
                    <td><%= promotion.getLimitUsage() %></td>
                    <td><%= promotion.getStartDateStr() %> <b>TO</b> <%= promotion.getEndDateStr() %></td>
                    <td>
                        <button class="action-button" onclick="showUpdateForm('<%= promotion.getPromoId() %>', '<%= promotion.getPromoName() %>', '<%= promotion.getPromoCode() %>', '<%= promotion.getRequirement() %>', '<%= promotion.getPromoAmount() %>', '<%= promotion.getDescription() %>', '<%= promotion.getLimitUsage() %>','<%= promotion.getStartDateStr() %>','<%= promotion.getEndDateStr() %>', '<%= promotion.getPromoImage() %>')">Update</button>
                        <form action="../promotion" method="post" style="display: inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="promoId" value="<%= promotion.getPromoId() %>">
                            <button type="submit" class="delete-action-button" onclick="return confirmDelete();">Delete</button>
                        </form>
                    </td>
                </tr>
                <% } %>
            </tbody>
        </table>

        <div style="margin: 30px;">
            <button id="addPromoBtn" class="action-button" onclick="showAddForm()">Add New Promotion</button>
        </div>
        <!-- Add Promotion Modal -->
        <div id="addPromoModal" class="modal">
            <div class="modal-content">
                <span class="close-button" onclick="hideAddForm()">&times;</span>
                <form id="addForm" action="../promotion" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    <label for="newPromoId">Promo ID:</label>
                    <input type="text" id="newPromoId" name="promoId"><br>

                    <label for="newPromoName">Promo Name:</label>
                    <input type="text" id="newPromoName" name="promoName"><br>

                    <label for="newPromoCode">Promo Code:</label>
                    <input type="text" id="newPromoCode" name="promoCode"><br>

                    <label for="newRequirement">Requirement (RM):</label>
                    <input type="number" id="newRequirement" name="requirement"><br>

                    <label for="newPromoAmount">Promo Amount (RM):</label>
                    <input type="number" id="newPromoAmount" name="promoAmount"><br>

                    <label for="newDescription">Description:</label>
                    <textarea id="newDescription" name="description"></textarea><br>

                    <label for="newLimitUsage">Limit Usage:</label>
                    <input type="number" id="newLimitUsage" name="limitUsage"><br>

                    <label for="newStartDate">Start Date:</label>
                    <input type="date" id="newStartDate" name="startDate"><br>
                    
                    <label for="newEndDate">End Date:</label>
                    <input type="date" id="newEndDate" name="endDate"><br>

                    <label for="newImage">Image:</label>
                    <input type="file" id="newImage" name="promoImage"><br>

                    <input type="submit" value="Add Promotion">
                    <button type="button" class="cancel-button" onclick="hideAddForm()">Cancel</button>
                </form>
            </div>
        </div>

        <!-- Update Promotion Modal -->
        <div id="updatePromoModal" class="modal">
            <div class="modal-content">
                <span class="close-button" onclick="hideUpdateForm()">&times;</span>
                <form id="updateForm" action="../promotion" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update">

                    <label for="updatePromoId">Promo ID:</label>
                    <input type="text" id="updatePromoId" name="promoId" readonly><br>

                    <label for="updatePromoName">Promo Name:</label>
                    <input type="text" id="updatePromoName" name="promoName"><br>

                    <label for="updatePromoCode">Promo Code:</label>
                    <input type="text" id="updatePromoCode" name="promoCode"><br>

                    <label for="updateRequirement">Requirement (RM):</label>
                    <input type="number" id="updateRequirement" name="requirement"><br>

                    <label for="updatePromoAmount">Promo Amount (RM):</label>
                    <input type="number" id="updatePromoAmount" name="promoAmount"><br>

                    <label for="updateDescription">Description:</label>
                    <textarea id="updateDescription" name="description"></textarea><br>

                    <label for="updateLimitUsage">Limit Usage:</label>
                    <input type="number" id="updateLimitUsage" name="limitUsage"><br>
              
                    <label for="updateStartDate">Start Date:</label>
                    <input type="date" id="updateStartDate" name="startDate"><br>
                    
                    <label for="updateEndDate">End Date:</label>
                    <input type="date" id="updateEndDate" name="endDate"><br>
                    
                    <label for="image">Current Image:</label>
                    <img id="currentImage" style="width: 100px; height: auto;">
                    <label for="updateImage">Change Image:</label>
                    <input type="file" id="updateImage" name="promoImage"><br>

                    <input type="submit" value="Update Promotion">
                    <button type="button" class="cancel-button" onclick="hideUpdateForm()">Cancel</button>
                </form>
            </div>
        </div>


    </body>
</html>
