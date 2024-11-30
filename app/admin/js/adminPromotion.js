function showAddForm() {
    document.getElementById('addPromoModal').style.display = 'block';
}

// Function to hide the Add Promotion Modal
function hideAddForm() {
    document.getElementById('addPromoModal').style.display = 'none';
}

// Function to display the Update Promotion Modal
function showUpdateForm(promoId, promoName, promoCode, requirement, promoAmount, description, limitUsage, startDate,endDate, image) {
    document.getElementById('updatePromoId').value = promoId;
    document.getElementById('updatePromoName').value = promoName;
    document.getElementById('updatePromoCode').value = promoCode;
    document.getElementById('updateRequirement').value = requirement;
    document.getElementById('updatePromoAmount').value = promoAmount;
    document.getElementById('updateDescription').value = description;
    document.getElementById('updateLimitUsage').value = limitUsage;
    document.getElementById('updateStartDate').value = startDate;
    document.getElementById('updateEndDate').value = endDate;
    document.getElementById('currentImage').src = 'data:image/jpeg;base64,' + image;
    document.getElementById('currentImage').alt = promoName;
    document.getElementById('updatePromoModal').style.display = 'block';
}

// Function to hide the Update Promotion Modal
function hideUpdateForm() {
    document.getElementById('updatePromoModal').style.display = 'none';
}

// Function to confirm deletion
function confirmDelete() {
    return confirm('Are you sure you want to delete this promotion?');
}