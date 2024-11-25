<%@page contentType="text/html" pageEncoding="UTF-8"%>
<%@page import="entity.*, java.util.List" %>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="css/promotion.css" />
        <link rel="icon" href="img/logo.png">
        <title>Promotion</title>
    </head>
    <body>
        <%@ include file = "header.jsp" %>
        <div id="promo-container">
        <h1 id="promo-title">Promotions</h1>
        <%
            List<Promotion> promotions = (List<Promotion>)request.getAttribute("promotionList");
            for(Promotion promotion : promotions) {
        %>
            <div class="promo-card">
                <div class="promo-image">
                    <img src="img/logo.png" alt="Promotion Image">
                </div>
                <div class="promo-details">
                    <h2 class="promo-name"><%= promotion.getPromoName()%></h2>
                    <p class="promo-code">Use Code: <%= promotion.getPromoCode()%></p>
                    <p class="promo-desc"><%= promotion.getDescription()%></p>
                    <p class="promo-req">Requirement: Minimum purchase of RM<%= promotion.getRequirement()%>.</p>
                    <p class="promo-discount">Discount: RM<%= promotion.getPromoAmount()%> off total purchase.</p>
                    <p class="promo-limit">Limit: Can be used up to <%= promotion.getLimitUsage()%> times per user.</p>
                    <p class="promo-duration">Expiry Date: <%= promotion.getEndDateStr()%></p>
                    <form action="PromotionServlet" method="POST">
                        <input type="hidden" name="url" value="promotion">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="promoId" value="<%= promotion.getPromoId()%>">
                    <button class="promo-btn" type="submit">Get Voucher Code</button>
                    </form>
                </div>
            </div>
            <%}%>
        </div>
        <%@ include file = "footer.jsp" %>
    </body>
</html>
