<?php
$servername = "localhost:3306"; // MySQL server address
$username = "299PRO"; // MySQL username
$password = "spArk_007"; // MySQL password
$database = "299PRO_medilift"; // MySQL database name

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed : " . $conn->connect_error);
}

$email = $password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the email exists in the users table
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            session_start();
            $_SESSION['email'] = $email;

            header("location: user_profile.php");
        } else {
            echo "Invalid password. Please try again.";
        }
    } else {
        // If the email is not found in the users table, check ambulance_users
        $sql = "SELECT * FROM ambulance_users WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                session_start();
                $_SESSION['email'] = $email;

                header("location: ambulance_profile.php");
            } else {
                echo "Invalid password. Please try again.";
            }
        } else {
            // If the email is not found in ambulance_users, check hospital_users
            $sql = "SELECT * FROM hospital_users WHERE email = '$email'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                if (password_verify($password, $row['password'])) {
                    session_start();
                    $_SESSION['email'] = $email;

                    header("location: hospital_profile.php");
                } else {
                    echo "Invalid password. Please try again.";
                }
            } else {
                echo "User with this email does not exist.";
            }
        }
    }
}
$conn->close();
?>