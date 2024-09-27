<?php
// Database connection details
$servername = "localhost:3306";
$username = "299PRO"; // MySQL username
$password = "spArk_007"; // MySQL password
$database = "299PRO_medilift"; // MySQL database name

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed : " . $conn->connect_error);
}

$hospital_name = $email = $password = $confirm_password = $admin_name = $address = $hospital_contact = $available = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        isset($_POST['hospital_name']) &&
        isset($_POST['email']) &&
        isset($_POST['password']) &&
        isset($_POST['confirm_password']) &&
        isset($_POST['admin_name']) &&
        isset($_POST['address']) &&
        isset($_POST['hospital_contact']) &&
        isset($_POST['available'])
    ) {
        $hospital_name = $_POST['hospital_name'];
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL); // Validate and sanitize email
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $admin_name = $_POST['admin_name'];
        $address = $_POST['address'];
        $hospital_contact = $_POST['hospital_contact'];
        $available = $_POST['available'];

        if ($password !== $confirm_password) {
            die("Passwords do not match.");
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO hospital_users(hospital_name, email, password, admin_name, address, hospital_contact, available)
                VALUES ('$hospital_name', '$email', '$hashed_password', '$admin_name', '$address', '$hospital_contact', '$available')";

        if ($conn->query($sql) === TRUE) {
            // Notify the user with JavaScript popup
            echo '<script>alert("Registration Successful! Please login now.");</script>';
            // Redirect to user_login.html
            echo '<script>window.location.href = "user_login.html";</script>';
            exit;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "One or more required fields are missing.";
    }
} else {
    echo "Form not submitted.";
}

$conn->close();
?>