$(document).ready(function () {
    $('#request-otp-btn').on('click', function () {
        const email = $('#email').val().trim();
        const $button = $(this);

        if (!email) {
            $button.text('Enter an Email!');
            setTimeout(() => $button.text('Request OTP'), 2000);
            return;
        }

        // Disable the button during the request
        $button.prop('disabled', true).text('Sending...');

        $.ajax({
            url: 'register.php',
            type: 'POST',
            data: {
                email: email,
                request_otp: true,
            },
            success: function (response) {
                if (response.trim() === 'Success') {
                    $button.html('<i class="ti ti-check"></i> Sent')
                        .addClass('otp-sent-success')
                        .prop('disabled', true);
                } else {
                    $button.text('Retry').prop('disabled', false);
                    console.error('Error:', response);
                }
            },
            error: function (xhr, status, error) {
                $button.text('Retry').prop('disabled', false);
                console.error('AJAX error:', status, error);
            },
        });
    });
});
