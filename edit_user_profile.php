<?php
// Initialize session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: user_login.html"); // Redirect to login page if not logged in
    exit;
}

// Database connection details
$servername = "localhost:3306"; // MySQL server address
$username = "299PRO"; // MySQL username
$password = "spArk_007"; // MySQL password
$database = "299PRO_medilift"; // MySQL database name

// Create a connection to the MySQL database
$conn = new mysqli($servername, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user information from the database
$email = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    header("Location: user_login.html"); // Redirect to login page if user not found
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $address_contact = $_POST['address_contact'];
    $gender = $_POST['gender'];
    $medical_condition = $_POST['medical_condition'];
    $date_of_birth = $_POST['date_of_birth'];

    // Check if a new photo was uploaded
    if (isset($_FILES["photo"]) && $_FILES["photo"]["name"] != "") {
        $targetDirectory = "uploads/";
        $targetFile = $targetDirectory . basename($_FILES["photo"]["name"]);

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
            // File uploaded successfully, store the file path in the database
            $updateSql = "UPDATE users SET Name = '$name', address_contact = '$address_contact', gender = '$gender', medical_condition = '$medical_condition', date_of_birth = '$date_of_birth', photo_path = '$targetFile' WHERE email = '$email'";
            if ($conn->query($updateSql) === TRUE) {
                // Redirect to the user profile page after the update
                header("Location: user_profile.php");
                exit;
            } else {
                echo "Error updating database: " . $conn->error;
            }
        } else {
            echo "Error uploading photo.";
        }
    } else {
        // Update user information without changing the profile photo
        $updateSql = "UPDATE users SET Name = '$name', address_contact = '$address_contact', gender = '$gender', medical_condition = '$medical_condition', date_of_birth = '$date_of_birth' WHERE email = '$email'";
        if ($conn->query($updateSql) === TRUE) {
            // Redirect to the user profile page after the update
            header("Location: user_profile.php");
            exit;
        } else {
            echo "Error updating database: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_1.css"> <!-- Add your CSS file here -->
    <title>Edit Profile</title>
</head>
<body>
    <div class="edit-profile-container">
        <h1>Edit User Profile</h1>
        <form method="post" action="edit_profile.php" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Name" value="<?php echo $row['Name']; ?>" required>
            <input type="text" name="address_contact" placeholder="Address & Contact" value="<?php echo $row['address_contact']; ?>" required>
            <input type="text" name="gender" placeholder="Gender" value="<?php echo $row['gender']; ?>" required>
            <input type="text" name="medical_condition" placeholder="Medical Condition" value="<?php echo $row['medical_condition']; ?>" required>
            <input type="date" name="date_of_birth" value="<?php echo $row['date_of_birth']; ?>" required>
            <input type="file" name="photo" accept="image/*">
            <button type="submit">Update Profile</button>
        </form>
        <a href="user_profile.php">Go Back</a>
    </div>
</body>
</html>