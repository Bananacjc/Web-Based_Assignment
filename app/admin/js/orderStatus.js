function showViewOrderForm(orderId, customerId, orderItems, promo_amount, subtotal, shipping_fee, total, payment_method, order_time, status) {
    var modal = document.getElementById('viewOrderModal');
    modal.style.display = 'block';
    document.getElementById('viewOrderID').innerText = orderId;
    document.getElementById('viewCusId').innerText = customerId;
    document.getElementById('viewPromoAmount').innerText = promo_amount;
    document.getElementById('viewSubTotal').innerText = subtotal;
    document.getElementById('viewShippingFee').innerText = shipping_fee;
    document.getElementById('viewTotal').innerText = total;
    document.getElementById('viewOrderItems').innerText = orderItems;
    document.getElementById('viewPaymentMethod').innerText = payment_method;
    document.getElementById('viewOrderTime').innerText = order_time;
    document.getElementById('viewStatus').innerText = status;

}

function showUpdateOrderForm(orderId, customerId, orderItems, promoAmount, subtotal, shippingFee, paymentMethod, orderTime, status) {
    var modal = document.getElementById('updateOrderModal');
    var form = document.getElementById('updateForm');
    modal.style.display = 'block';

    form.elements['order_id'].value = orderId;
    form.elements['customer_id'].value = customerId;
    form.elements['order_items'].value = orderItems; 
    form.elements['promo_amount'].value = promoAmount;
    form.elements['sub_total'].value = subtotal; 
    form.elements['shipping_fee'].value = shippingFee;
    form.elements['payment_method'].value = paymentMethod;
    form.elements['order_time'].value = orderTime; 
    form.elements['status'].value = status;
}




function hideUpdateForm() {
    document.getElementById('updateOrderModal').style.display = 'none';
}



function hideViewForm() {
    document.getElementById('viewOrderModal').style.display = 'none';
}

function hideAddForm() {
    document.getElementById('addOrderModal').style.display = "none";
}

function hideUpdateForm() {
    document.getElementById('updateOrderModal').style.display = "none";
}

function confirmDelete() {
    return confirm("Are you sure you want to delete this voucher?");
}

function showAddForm() {
    var modal = document.getElementById('addOrderModal');
    modal.style.display = "block";
}


function confirmAddVoucher() {
    const confirmation = confirm("Are you sure you want to add this voucher?");
    return confirmation;
}