<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Customer Contact</title>
        <style>
             h1 {
            font-size: 36px;
            text-align: center;
            margin-top: 50px;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        h1 {
            font-size: 36px;
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff; /* white background */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* slight shadow */
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 12px;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2; /* alternate row color */
        }


        .send-action-button, .delete-action-button {
            padding: 8px 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .send-action-button:hover, .delete-action-button:hover {
            background-color: #0056b3;
        }

        .delete-action-button {
            background-color: #dc3545; /* red */
        }

        .delete-action-button:hover {
            background-color: #c82333; /* darker red on hover */
        }

        #sendForm input[type="text"], #sendForm input[type="textarea"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        #sendForm button[type="submit"] {
            padding: 8px 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        #sendForm button[type="submit"]:hover {
            background-color: #218838;
        }

        .modal {
            margin-top: 80px;
            display: none;
            position: fixed;
            z-index: 100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background: white;
            width: 60%;
            margin: 35px auto;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            box-sizing: border-box;
        }

        .close-button {
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        </style>
    </head>
    <%@ include file = "adminHeader.jsp" %>
    <body>
        <h1 style="margin-top: 100px;">Customer Contacts</h1>
        <div class="contact-list">
            <table>
                <tr>
                    <th>Contact ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Message</th>
                    <th>Action</th>
                </tr>
                <% for (Contact contact : contacts) { %>
                <tr>
                    <td><%= contact.getContactId() %></td>
                    <td><%= contact.getContactName() %></td>
                    <td><%= contact.getContactEmail() %></td>
                    <td><%= contact.getContactSubject() %></td>
                    <td><%= contact.getContactMessage() %></td>
                    <td>

                        <button class="send-action-button" onclick="showSendForm('<%= contact.getContactId() %>', '<%= contact.getContactEmail() %>')">Send</button>

                        <form action="../AdminContactServlet" method="post" style="display: inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="contactId" value="<%= contact.getContactId() %>">
                            <button type="submit" class="delete-action-button" onclick="return confirmDelete();">Delete</button>
                        </form>
                    </td>
                </tr>
                <% } %>

            </table>
        </div>
        <div id="sendModal" class="modal">
            <div class="modal-content">
                <span class="close-button" onclick="hideSendForm()">&times;</span>
                <form id="sendForm" action="../AdminContactServlet" method="post" style="display: inline;">
                    <input type="hidden" name="action" value="send">
                    <input type="hidden" name="contactId" />
                    <input type="hidden" name="contactEmail" />  
                    <label>Enter Subject</label>
                    <input type="text" name="contactSubject" />
                    <label>Enter Text For the Content</label>
                    <input type="textarea" name="contactContent" />
                    <button type="submit">Submit</button>
                </form>
            </div>
        </div>
    </body>

    <script>
        function showSendForm(contactId, contactEmail) {
            var modal = document.getElementById('sendModal');
            var form = document.getElementById('sendForm');
            form.elements['contactId'].value = contactId;
            form.elements['contactEmail'].value = contactEmail;
            modal.style.display = "block";
        }

        function hideSendForm() {
            document.getElementById('sendModal').style.display = "none";
        }

        function confirmSendAgain() {
            return confirm("Are you sure you want to send the email again?");
        }

        function confirmDelete() {
            return confirm("Are you sure you want to delete this contact?");
        }
    </script>

</html>
