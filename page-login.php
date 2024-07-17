<?php
/*
Template Name: Custom Login Template
*/

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <h2>Login</h2>
        <form id="loginForm">
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username"><br>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password"><br><br>
            <button type="submit">Login</button>
        </form>

        <div id="message"></div>

    </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const loginForm = document.getElementById('loginForm');
        const messageDiv = document.getElementById('message');

        loginForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            // Make POST request to custom API endpoint
            fetch('<?php echo esc_url(rest_url('custom/v1/sign-in')); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    username: username,
                    password: password
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Store JWT token in localStorage
                    localStorage.setItem('jwt_token', data.token);

                    // Redirect user to WP dashboard
                    window.location.href = '<?php echo esc_url(admin_url()); ?>';
                } else {
                    messageDiv.innerHTML = '<div class="error">' + data.error + '</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageDiv.innerHTML = '<div class="error">An error occurred. Please try again.</div>';
            });
        });
    });
</script>
