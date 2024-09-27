<?php
// Database connection details
$servername = "localhost:3306"; 
$username = "299PRO"; //MySQL username
$password = "spArk_007"; //MySQL password
$database = "299PRO_medilift"; //MySQL database name

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed : ".$conn->connect_error);
}

// Initialize variables with empty values to avoid "Undefined array key" warnings
$name = $email = $password = $confirm_password = $address_contact = $gender = $medical_condition = $date_of_birth = "";

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST") {
    if (
        isset($_POST['Name']) &&
        isset($_POST['email']) &&
        isset($_POST['password']) &&
        isset($_POST['confirm_password']) &&
        isset($_POST['address_contact']) &&
        isset($_POST['gender']) &&
        isset($_POST['medical_condition']) &&
        isset($_POST['date_of_birth'])
       ) {
        $name = $_POST['Name'];
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL); // Validate and sanitize email
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $address_contact = $_POST['address_contact'];
        $gender = $_POST['gender'];
        $medical_condition = $_POST['medical_condition'];
        $date_of_birth = $_POST['date_of_birth'];

        if ($password !== $confirm_password) {
            die("Passwords do not match.");
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users(Name, email, password, address_contact, gender, medical_condition, date_of_birth)
                VALUES ('$name', '$email', '$hashed_password', '$address_contact', '$gender', '$medical_condition', '$date_of_birth')";

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