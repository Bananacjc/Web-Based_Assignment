function showAddForm() {
    document.getElementById('addStaffModal').style.display = 'block';
}

function hideAddForm() {
    document.getElementById('addStaffModal').style.display = 'none';
}

function showUpdateEmployeeForm(id, name, email,role) {
    var modal = document.getElementById('updateEmployeeModal');
    var form = document.getElementById('updateEmployeeForm');
    modal.style.display = "block";

    form.elements['employee_id'].value = id;
    form.elements['employee_name'].value = name;
    form.elements['email'].value = email;
    form.elements['role'].value = role;

}

function hideUpdateEmployeeForm() {
    document.getElementById('updateEmployeeModal').style.display = 'none';
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