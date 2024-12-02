<?php
$_title = 'Profile';
$_css = '../css/profile.css';
require '../_base.php';
include '../_head.php';
?>
<input type="hidden" id="status" value="<% if (request.getAttribute('status') != "") {out.print(request.getAttribute("status")); request.setAttribute("status", "");} %>">
<h1 class="h1 header-banner">Profile</h1>
<div id="profile-container">
    <div class="sidebar">
        <ul>
            <li id="personal-info-btn"><i class="ti ti-user-filled"></i> Personal Info</li>
            <li id="payment-method-btn"><i class="ti ti-credit-card-filled"></i> Payment Method</li>
            <li id="address-btn"><i class="ti ti-map-pins"></i>Address</li>
            <li id="order-history-btn"><i class="ti ti-shopping-cart-filled"></i> Order and Reviews</li>
            <li id="change-password-btn"><i class="ti ti-lock-filled"></i> Change Password</li>
            <li id="logout-btn"><a href="LogoutServlet" id="logout-link"><i class="ti ti-logout"></i>Logout</a></li>
        </ul>
    </div>
    <div class="content" id="personal-info-content" style="display: block;">
        <h2>Personal Info</h2>
        <form id="personal-info-container" action="ProfileModify" method="post" enctype="multipart/form-data">
            <div class="input-file-container">
                <div class="image-preview-container">
                    <img id="image-preview" src="data:image/jpeg;base64,${customer.image}" alt="">
                </div>
                <input type="file" name="profile-pic" id="profile-pic" class="input-file" onchange="previewFile()" />
                <label for="profile-pic" class="input-label">Upload Profile Picture</label>
            </div>
            <div>
                <div class="input-subcontainer">
                    <input type="text" name="username" value="${empty customer ? '' : customer.username}" class="input-box" spellcheck="false" />
                    <label for="username" class="label">Username</label>
                </div>
                <div class="input-subcontainer">
                    <input type="text" name="email" value="${empty customer ? '' : customer.email}" class="input-box" spellcheck="false" />
                    <label for="email" class="label">Email</label>
                </div>
                <button class="btn" type="submit">Save</button>
            </div>
            <div>
                <div class="input-subcontainer">
                    <input type="text" name="customer-name" value="${empty customer ? '' : customer.customerName}" class="input-box" spellcheck="false" />
                    <label for="customer-name" class="label">Name</label>
                </div>
                <div class="input-subcontainer">
                    <input type="text" name="phone" value="${empty customer ? '' : customer.contactNumber}" class="input-box" spellcheck="false" />
                    <label for="phone" class="label">Phone</label>
                </div>
            </div>
        </form>
    </div>
    <div class="content" id="payment-method-content" style="display: none;">
        <div id="payment-method-container" style="display: flex;">
            <!-- Bank form -->
            <form id="bank-container" action="BankServlet" method="post">
                <div>
                    <h2>Bank</h2>
                    <div class="input-subcontainer">
                        <input type="text" name="name" value="${empty bank ? '' : bank.name}" class="input-box" spellcheck="false" required />
                        <label for="name" class="label">Name</label>
                    </div>
                    <div class="input-subcontainer">
                        <input type="text" name="acc-num" value="${empty bank ? '' : bank.accNum}" class="input-box" spellcheck="false" required />
                        <label for="acc-num" class="label">Account Number</label>
                    </div>
                    <div class="input-subcontainer">
                        <input type="text" name="cvv" value="${empty bank ? '' : bank.cvv}" class="input-box" spellcheck="false" required />
                        <label for="cvv" class="label">CVV</label>
                    </div>
                    <div class="input-subcontainer">
                        <label for="expiry-date" class="normal-label">Expiry Date</label>
                        <input type="month" name="expiry-date" value="${empty bank ? '' : bank.expiryDate}" spellcheck="false" id="expiry-date-input" required />
                    </div>
                    <div class="input-subcontainer">
                        <label for="card-type" class="normal-label">Card Type</label>
                        <select name="card-type" id="card-type">
                            <option value="">Select a card type</option>
                            <option value="visa" ${bank !=null && 'visa' .equals(bank.cardType) ? 'selected' : '' }>Visa</option>
                            <option value="mastercard" ${bank !=null && 'mastercard' .equals(bank.cardType) ? 'selected' : '' }>MasterCard</option>
                        </select>
                    </div>
                    <button class="btn" type="submit">Save</button>
                </div>
            </form>
            <!-- E-Wallet form -->
            <form id="e-wallet-container" style="margin-left: 50px;" action="EwalletServlet" method="post">
                <div>
                    <h2>E-Wallet</h2>
                    <div class="input-subcontainer">
                        <input type="text" name="name" value="${empty ewallet ? '' : ewallet.name}" class="input-box" spellcheck="false" required />
                        <label for="name" class="label">Name</label>
                    </div>
                    <div class="input-subcontainer">
                        <input type="text" name="phone" value="${empty ewallet ? '' : ewallet.phone}" class="input-box" spellcheck="false" required />
                        <label for="phone" class="label">Phone</label>
                    </div>
                    <button class="btn" type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>
    <div class="content" id="address-content" style="display: none;">
        <h2>Address</h2>
        <form id="address-container" action="AddressServlet" method="post">
            <div class="input-subcontainer" id="address-input-container">
                <input type="text" name="address" value="${empty customer ? '' : customer.address}" class="input-box" spellcheck="false" />
                <label for="address" class="label">Address</label>
            </div>
            <button class="btn" type="submit">Save</button>
        </form>
    </div>
    <div class="content" id="order-history-content" style="display: none;">
        <h2>Order History</h2>
        <table class="history-table">
            <thead>
                <tr>
                    <th class="order-id-header">ORDER ID</th>
                    <th class="date-header">DATE</th>
                    <th class="time-header">TIME</th>
                    <th class="total-price-header">TOTAL (RM)</th>
                    <th class="delivery-status-header">STATUS</th>
                    <th class="action-header">ACTION</th>
                </tr>
            </thead>
            <tbody>
                <% 
                        for (OrderDetails details : orderDetailsList) {
                            int status = details.getStatus();
                            
                        %>
                <tr>
                    <td>
                        <p class="order-id"><%= details.getOrderId() %></p>
                    </td>
                    <td>
                        <p class="date"><%= details.getPaidDate() %></p>
                    </td>
                    <td>
                        <p class="time"><%= details.getPaidTime() %></p>
                    </td>
                    <td class="total-price"><%= String.format("%.2f", details.getTotal()) %></td>
                    <td class="delivery-status">
                        <% 
                                switch(status) {
                                    case 1:
                                        out.print("Paid");
                                        break;
                                    case 2:
                                        out.print("Packaging");
                                        break;
                                    case 3:
                                        out.print("Shipping");
                                        break;
                                    case 4:
                                        out.print("Delivery");
                                        break;
                                    default:
                                        out.print("Unknown");
                                }
                                %>
                    </td>
                    <td class="action">

                        <% if (details.getOrderReviewStatus() == ReviewStatus.NONE) {%>
                        <a class="reviewbtn" href="ReviewServlet?orderId=<%= details.getOrderId() %>"><span>Review&nbsp;&nbsp;</span><i class="ti ti-circle-filled"></i></a>
                        <% } else { %>
                        <a class="reviewbtn" href=""><span>Review&nbsp;&nbsp;</span><i class="ti ti-check"></i></a>
                        <% } %>
                    </td>
                </tr>
                <% 
                        } %>
            </tbody>
        </table>
    </div>
    <div class="content" id="change-password-content" style="display: none;">
        <h2>Change Password</h2>
        <form id="change-password-container" action="ChangePassword" method="post">
            <div class="input-subcontainer">
                <input type="password" name="password" id="password" class="input-box" spellcheck="false" required />
                <label for="password" class="label">Password</label>
                <i class="ti ti-eye-off" id="togglePassword"></i>
            </div>
            <div class="input-subcontainer">
                <input type="password" name="confirm-password" id="confirm-password" class="input-box" spellcheck="false" required />
                <label for="confirm-password" class="label">Confirm Password</label>
                <i class="ti ti-eye-off" id="toggleConfirmPassword"></i>
            </div>
            <button class="btn" type="submit">Save</button>
        </form>
    </div>
    <!-- Add other content divs similarly with display: none; -->
</div>
<script src="../js/profileSidebar.js"></script>
<script src="../js/imagePreview.js"></script>
<script src="../js/inputHasContent.js"></script>
<script src="../js/showPassword.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    let status = document.getElementById("status").value;

    if (status === "personalSuccess") {
        swal.fire("Congratulations", "Personal info submitted successfully.", "success");
    }
    if (status === "bankSuccess") {
        swal.fire("Congratulations", "Bank details submitted successfully.", "success");
    }
    if (status === "ewalletSuccess") {
        swal.fire("Congratulations", "E-wallet details submitted successfully.", "success");
    }
    if (status === "addressSuccess") {
        swal.fire("Congratulations", "Address submitted successfully.", "success");
    }
    if (status === "passwordChanged") {
        swal.fire("Congratulations", "Password changed successfully.", "success");
    }
    if (status === "incompleteForm") {
        swal.fire("Sorry", "Incomplete Form.", "error");
    }
    if (status === "personalProcessFail") {
        swal.fire("Sorry", "Personal info submit failed.", "error");
    }
    if (status === "bankProcessFail") {
        swal.fire("Sorry", "Bank Processing Failure.", "error");
    }
    if (status === "ewalletProcessFail") {
        swal.fire("Sorry", "E-wallet Processing Failure.", "error");
    }
    if (status === "addressProcessFail") {
        swal.fire("Sorry", "Address submit failed.", "error");
    }
    if (status === "invalidConfirmPassword") {
        swal.fire("Sorry", "Invalid Confirm Password.", "error");
    }
</script>
<?php include '../_foot.php'; ?>