function showAddForm() {
    document.getElementById("addCustomerModal").style.display = "block";
}

function hideAddForm() {
    document.getElementById("addCustomerModal").style.display = "none";
}

function showUpdateCustomerForm(customerId, username, email, contactNum, banks, addresses, cart, promotionRecords) {
    var modal = document.getElementById('updateCustomerModal');
    var form = document.getElementById('updateForm');
    modal.style.display = "block";

    form.elements['customer_id'].value = customerId;
    form.elements['username'].value = username;
    form.elements['email'].value = email;
    form.elements['contact_num'].value = contactNum;
    form.elements['banks'].value = banks;
    form.elements['addresses'].value = addresses;
    form.elements['cart'].value = cart;
    form.elements['promotion_records'].value = promotionRecords;
}


function hideUpdateForm() {
    document.getElementById("updateCustomerModal").style.display = "none";
}

function confirmDelete() {
    return confirm('Are you sure you want to delete this customer?');
}

function confirmBlock() {
    return confirm('Are you sure you want to block this customer?');
}

function confirmUnblock() {
    return confirm('Are you sure you want to unblock this customer?');
}