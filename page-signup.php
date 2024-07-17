<?php
/*
Template Name: Sign-up
*/
get_header();
?>

<form id="signup-form">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>

    <button type="submit">Sign Up</button>
</form>

<div id="form-feedback"></div> 

<?php
get_footer();
?>
