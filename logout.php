<?php
// Start the session
session_start();

if (isset($_SESSION['email'])) {
    session_unset();

    session_destroy();

    header("Location: user_login.html"); 
    exit();
} else {
    header("Location: user_login.html"); 
    exit();
}
?>





