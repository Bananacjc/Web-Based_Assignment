<html>
    <head>
        <link rel="stylesheet" href="css/customer.css" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Customer</title>
    </head>
    <style>
        .activate-action-button {
            background-color: #007BFF; /* Blue background */
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            transition: background-color 0.3s;
            cursor: pointer;
            border-radius: 5px;
        }

        .activate-action-button:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        .deactivate-action-button {
            background-color: #FFD700; /* Yellow background */
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            transition: background-color 0.3s;
            cursor: pointer;
            border-radius: 5px;
        }

        .deactivate-action-button:hover {
            background-color: #cca300; /* Darker yellow on hover */
        }
    </style>
    <%@ include file = "adminHeader.jsp" %>
    <body>
        <h1>Customer</h1>
        <table id="promoTable" class="data-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Customer ID</th>
                    <th>Customer Name</th>
                    <th>Username</th>
                    <th>Contact Number</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <% for (Customer customerThing : customers) { %>
                <tr>
                    <td><img src="data:image/jpeg;base64,<%= customerThing.getImage() %>" alt="<%= customerThing.getCustomerName() %>"></td>
                    <td><%= customerThing.getCustomerId() %></td>
                    <td><%= customerThing.getCustomerName() %></td>
                    <td><%= customerThing.getUsername() %></td>
                    <td><%= customerThing.getContactNumber() %></td>
                    <td><%= customerThing.getEmail() %></td>
                    <td><%= customerThing.getAddress() %></td>
                    <td><%= customerThing.getStatus().name() %></td> <!-- Using name() to display status -->
                    <td>
                        <form action="../CustomerServlet" method="post">
                            <input type="hidden" name="customerId" value="<%= customerThing.getCustomerId() %>">
                            <% if (customerThing.getStatus() == Status.ACTIVE) { %>
                            <input type="hidden" name="action" value="deactivate">
                            <button type="submit" class="deactivate-action-button">Deactivate</button>
                            <% } else { %>
                            <input type="hidden" name="action" value="activate">
                            <button type="submit" class="activate-action-button">Activate</button>
                            <% } %>
                        </form>
                        <form action="../CustomerServlet" method="post" style="display: inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="customerId" value="<%= customerThing.getCustomerId() %>">
                            <button type="submit" class="delete-action-button" onclick="return confirmDelete();">Delete</button>
                        </form>
                    </td>
                </tr>
                <% } %>


            </tbody>
        </table>



    </body>
    <script>


        function confirmDelete() {
            return confirm("Are you sure you want to delete this staff member?");
        }

    </script>


</html>
