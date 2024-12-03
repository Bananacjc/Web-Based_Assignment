function showPopup(message, type) {
    // Create the popup container
    const popup = document.createElement('div');
    popup.classList.add('popup');
    popup.style.position = 'fixed';
    popup.style.top = '0';
    popup.style.left = '0';
    popup.style.width = '100%';
    popup.style.height = '100%';
    popup.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    popup.style.display = 'flex';
    popup.style.justifyContent = 'center';
    popup.style.zIndex = '1000';

    // Create the popup content
    const popupContent = document.createElement('div');
    popupContent.style.backgroundColor = type === 'success' ? '#d4edda' : '#f8d7da'; // Light green for success, light red for error
    popupContent.style.color = type === 'success' ? '#155724' : '#721c24'; // Dark green for success, dark red for error
    popupContent.style.width = '300px';
    popupContent.style.height = '120px';
    popupContent.style.margin = '20px';
    popupContent.style.padding = '25px';
    popupContent.style.borderRadius = '10px';
    popupContent.style.textAlign = 'center';
    popupContent.style.display = 'flex';
    popupContent.style.flexDirection = 'column';
    popupContent.style.justifyContent = 'space-between';
    popupContent.style.alignItems = 'center';
    popupContent.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';

    // Add the title
    const popupTitle = document.createElement('h3');
    popupTitle.textContent = type === 'success' ? 'Success' : 'Failed';
    popupTitle.style.margin = '0';
    popupTitle.style.fontSize = '24px';
    popupTitle.style.fontWeight = 'bold';

    // Add the message text
    const popupMessage = document.createElement('p');
    popupMessage.textContent = message;
    popupMessage.style.margin = '10px 0';

    // Create the "OK" button
    const okButton = document.createElement('button');
    okButton.textContent = 'OK';
    okButton.style.padding = '10px 20px';
    okButton.style.backgroundColor = type === 'success' ? '#28a745' : '#dc3545'; // Button color matches the type
    okButton.style.color = '#fff';
    okButton.style.border = 'none';
    okButton.style.borderRadius = '5px';
    okButton.style.cursor = 'pointer';
    okButton.style.fontSize = '14px';

    // Add hover effect to the button
    okButton.addEventListener('mouseenter', () => {
        okButton.style.backgroundColor = type === 'success' ? '#218838' : '#c82333'; // Darker shade on hover
    });
    okButton.addEventListener('mouseleave', () => {
        okButton.style.backgroundColor = type === 'success' ? '#28a745' : '#dc3545'; // Original color
    });

    // Close the popup when the "OK" button is clicked
    okButton.onclick = () => {
        popup.remove();
    };

    // Append elements
    popupContent.appendChild(popupTitle);
    popupContent.appendChild(popupMessage);
    popupContent.appendChild(okButton);
    popup.appendChild(popupContent);
    document.body.appendChild(popup);
}
