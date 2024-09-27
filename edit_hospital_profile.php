<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: hospital_login.html"); // Redirect to login page if not logged in
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

// Fetch hospital user information from the database
$email = $_SESSION['email'];
$sql = "SELECT * FROM hospital_users WHERE email = '$email'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    header("Location: hospital_login.html"); // Redirect to login page if user not found
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hospital_name = $_POST['hospital_name'];
    $address = $_POST['address'];
    $admin_name = $_POST['admin_name'];
    $hospital_contact = $_POST['hospital_contact'];
    $available = $_POST['available'];

    // Check if a new photo was uploaded
    if (isset($_FILES["photo"]) && $_FILES["photo"]["name"] != "") {
        $targetDirectory = "uploads/";
        $targetFile = $targetDirectory . basename($_FILES["photo"]["name"]);

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
            // File uploaded successfully, store the file path in the database
            $updateSql = "UPDATE hospital_users SET hospital_name = '$hospital_name', address = '$address', admin_name = '$admin_name', hospital_contact = '$hospital_contact', available = '$available', photo_path = '$targetFile' WHERE email = '$email'";
            if ($conn->query($updateSql) === TRUE) {
                // Redirect to the hospital profile page after the update
                header("Location: hospital_profile.php");
                exit;
            } else {
                echo "Error updating database: " . $conn->error;
            }
        } else {
            echo "Error uploading photo.";
        }
    } else {
        // Update hospital information without changing the profile photo
        $updateSql = "UPDATE hospital_users SET hospital_name = '$hospital_name', address = '$address', admin_name = '$admin_name', hospital_contact = '$hospital_contact', available = '$available' WHERE email = '$email'";
        if ($conn->query($updateSql) === TRUE) {
            // Redirect to the hospital profile page after the update
            header("Location: hospital_profile.php");
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
        <h1>Edit Hospital Profile</h1>
        <form method="post" action="edit_hospital_profile.php" enctype="multipart/form-data">
            <input type="text" name="hospital_name" placeholder="Hospital Name" value="<?php echo $row['hospital_name']; ?>" required>
            <input type="text" name="address" placeholder="Address" value="<?php echo $row['address']; ?>" required>
            <input type="text" name="admin_name" placeholder="Administrator Name" value="<?php echo $row['admin_name']; ?>" required>
            <input type="text" name="hospital_contact" placeholder="Hospital Contact" value="<?php echo $row['hospital_contact']; ?>" required>
            <select name="available">
                <option value="Yes" <?php if ($row['available'] == 'Yes') echo 'selected'; ?>>Yes</option>
                <option value="No" <?php if ($row['available'] == 'No') echo 'selected'; ?>>No</option>
            </select>
            <input type="file" name="photo" accept="image/*">
            <button type="submit">Update Profile</button>
        </form>
        <a href="hospital_profile.php">Go Back</a>
    </div>
</body>
</html>