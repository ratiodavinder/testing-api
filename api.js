jQuery(document).ready(function($) {
    $('#signup-form').on('submit', function(event) {
        event.preventDefault();

        var username = $('#username').val();
        var email = $('#email').val();
        var password = $('#password').val();

        
        $.ajax({
            url: apiVars.signUpUrl, 
            method: 'POST',
            data: {
                username: username,
                email: email,
                password: password
            },
            success: function(response) {
                console.log('User created successfully:', response);

                $('#form-feedback').html('<p>Registration successful!</p>');
                $('#signup-form')[0].reset(); 
                
            },
            error: function(xhr, status, error) {
                console.log('Error:', error);

                $('#form-feedback').html('<p class="error">Registration failed. Please try again.</p>');
            }
        });
    });
});


