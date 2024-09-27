<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ambulance_login.html"); // Redirect to login page if not logged in
    exit;
}

$servername = "localhost:3306"; // MySQL server address
$username = "299PRO"; // MySQL username
$password = "spArk_007"; // MySQL password
$database = "299PRO_medilift"; // MySQL database name

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user information from the database
$email = $_SESSION['email'];
$sql = "SELECT * FROM ambulance_users WHERE email = '$email'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    header("Location: ambulance_login.html"); // Redirect to login page if user not found
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $license_number = $_POST['license_number'];
    $vehicle_type = $_POST['vehicle_type'];

    // Check if a new photo was uploaded
    if (isset($_FILES["photo"]) && $_FILES["photo"]["name"] != "") {
        $targetDirectory = "uploads/";
        $targetFile = $targetDirectory . basename($_FILES["photo"]["name"]);

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
            // File uploaded successfully, store the file path in the database
            $updateSql = "UPDATE ambulance_users SET Name = '$name', address = '$address', vehicle_type = '$vehicle_type', license_number = '$license_number', photo_path = '$targetFile' WHERE email = '$email'";
            if ($conn->query($updateSql) === TRUE) {
                // Redirect to the profile page after the update
                header("Location: ambulance_profile.php");
                exit;
            } else {
                echo "Error updating database: " . $conn->error;
            }
        } else {
            echo "Error uploading photo.";
        }
    } else {
        // Update user information without changing the profile photo
        $updateSql = "UPDATE ambulance_users SET Name = '$name', address = '$address', vehicle_type = '$vehicle_type', license_number = '$license_number' WHERE email = '$email'";
        if ($conn->query($updateSql) === TRUE) {
            // Redirect to the profile page after the update
            header("Location: ambulance_profile.php");
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
    <link rel="stylesheet" href="style_1.css">
    <title>Edit Profile</title>
</head>
<body>
    <div class="edit-profile-container">
        <h1>Edit Ambulance Profile</h1>
        <form method="post" action="edit_ambulance_profile.php" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Name" value="<?php echo $row['Name']; ?>" required>
            <input type="text" name="address" placeholder="Address" value="<?php echo $row['address']; ?>" required>
            <input type="text" name="vehicle_type" placeholder="Type of Vehicle" value="<?php echo $row['vehicle_type']; ?>" required>
            <input type="text" name="license_number" placeholder="License Number" value="<?php echo $row['license_number']; ?>" required>
            
            <input type="file" name="photo" accept="image/*">
            <button type="submit">Update Profile</button>
        </form>
        <a href="ambulance_profile.php">Go Back</a>
    </div>
</body>
</html>