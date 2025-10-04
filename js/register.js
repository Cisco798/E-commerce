$(document).ready(function() {
    $('#register-form').submit(function(e) {
        e.preventDefault();

        // Get values using correct field names from HTML
        const full_name = $('#full_name').val().trim();
        const email = $('#email').val().trim();
        const password = $('#password').val();
        const contact_number = $('#contact_number').val().trim();
        const country = $('#country').val().trim();
        const city = $('#city').val().trim();
        const user_role = $('input[name="user_role"]:checked').val();

        // Validate all required fields
        if (full_name === '' || email === '' || password === '' || contact_number === '' || country === '' || city === '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please fill in all required fields!',
            });
            return;
        }

        // Validate email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Email',
                text: 'Please enter a valid email address!',
            });
            return;
        }

        // Validate password
        if (password.length < 6 || !password.match(/[a-z]/) || !password.match(/[A-Z]/) || !password.match(/[0-9]/)) {
            Swal.fire({
                icon: 'error',
                title: 'Weak Password',
                text: 'Password must be at least 6 characters long and contain at least one lowercase letter, one uppercase letter, and one number!',
            });
            return;
        }

        // Validate contact number (basic validation for numbers and common formats)
        const phoneRegex = /^[\d\s\-\+\(\)]+$/;
        if (!phoneRegex.test(contact_number)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Contact Number',
                text: 'Please enter a valid contact number!',
            });
            return;
        }

        // Show loading state
        $('#register-btn').prop('disabled', true);
        $('.btn-text').hide();
        $('.loading').css('display', 'inline-block');

        // Submit form via AJAX
        $.ajax({
            url: '../actions/register_user_action.php',
            type: 'POST',
            data: {
                full_name: full_name,
                email: email,
                password: password,
                contact_number: contact_number,
                country: country,
                city: city,
                user_role: user_role
            },
            success: function(response) {
                // Reset button state
                $('#register-btn').prop('disabled', false);
                $('.btn-text').show();
                $('.loading').hide();

                // Handle response
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registration Successful',
                        text: response.message || 'Your account has been created successfully!',
                        confirmButtonColor: '#D19C97'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'login.php';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Registration Failed',
                        text: response.message || 'An error occurred during registration!',
                        confirmButtonColor: '#D19C97'
                    });
                }
            },
            error: function(xhr, status, error) {
                // Reset button state
                $('#register-btn').prop('disabled', false);
                $('.btn-text').show();
                $('.loading').hide();

                console.error('Registration error:', error);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'An error occurred! Please try again later.',
                    confirmButtonColor: '#D19C97'
                });
            }
        });
    });
});