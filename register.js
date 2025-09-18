$(document).ready(function() {
    $('#register-form').submit(function(e) {
        e.preventDefault();

        let name = $('#name').val().trim();
        let email = $('#email').val().trim();
        let password = $('#password').val();
        let phone_number = $('#phone_number').val().trim();
        let role = $('input[name="role"]:checked').val();

        // Regex patterns
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const phoneRegex = /^[0-9]{7,15}$/;

        // Empty fields check
        if (name === '' || email === '' || password === '' || phone_number === '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please fill in all fields!',
            });
            return;
        }

        // Email format check
        if (!emailRegex.test(email)) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please enter a valid email address!',
            });
            return;
        }

        // Phone number validation
        if (!phoneRegex.test(phone_number)) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Phone number must be 7–15 digits long!',
            });
            return;
        }

        // Password strength check
        if (password.length < 6 || !password.match(/[a-z]/) || !password.match(/[A-Z]/) || !password.match(/[0-9]/)) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Password must be at least 6 characters and contain lowercase, uppercase, and a number!',
            });
            return;
        }

        // Send AJAX request
        $.ajax({
            url: '../actions/register_user_action.php', 
            type: 'POST',
            dataType: 'json',
            data: {
                name: name,
                email: email,
                password: password,
                phone_number: phone_number,
                role: role
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'login.php';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.message,
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'An error occurred! Please try again later.',
                });
            }
        });
    });
});
