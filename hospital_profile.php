<?php
// Database connection details
$servername = "localhost:3306"; // MySQL server address
$username = "299PRO"; // MySQL username
$password = "spArk_007"; // MySQL password
$database = "299PRO_medilift"; // MySQL database name

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in and their email is available in the session
session_start();
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    // Handle photo upload if the form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["photo"])) {
        $targetDirectory = "uploads/"; // Directory where photos will be saved
        $targetFile = $targetDirectory . basename($_FILES["photo"]["name"]);

        // Check if the file is an image
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedExtensions = array("jpg", "jpeg", "png", "gif");

        if (in_array($imageFileType, $allowedExtensions)) {
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
                // File uploaded successfully, store the file path in the database
                $updateSql = "UPDATE hospital_users SET photo_path = '$targetFile' WHERE email = '$email'";
                if ($conn->query($updateSql) === TRUE) {
                    echo "Photo uploaded successfully.";
                } else {
                    echo "Error updating database: " . $conn->error;
                }
            } else {
                echo "Error uploading photo.";
            }
        } else {
            echo "Invalid file format. Please upload an image (jpg, jpeg, png, gif).";
        }
    }

    // Retrieve hospital user data from the database
    $sql = "SELECT * FROM hospital_users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_1.css">
    <title>Hospital Profile</title> 
    <style>
        .profile-container {
            background-color: silver;
        }
        h1 {
            font-style: initial;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            text-align: center;
            margin-top: 0%;
            font-weight: 900;
        }
        .profile-image {
            margin-left: -87%;
        }
        .user-info a {
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <div class="profile-container">
        <h1>Hospital Profile</h1>
        <?php
            if (isset($row)) {
                echo '<div class="profile-image">';
                if (isset($row['photo_path'])) {
                    echo '<img src="' . $row['photo_path'] . '" alt="Hospital Photo">';
                }
                echo '</div>';
                
                echo '<div class="user-info">';
                echo '<p><strong>Hospital Name : </strong>' . $row['hospital_name'] . '</p>';
                echo '<p><strong>Email : </strong>' . $row['email'] . '</p>';
                echo '<p><strong>Address : </strong>' . $row['address'] . '</p>';
                echo '<p><strong>Administrator Name : </strong>' . $row['admin_name'] . '</p>';
                echo '<p><strong>Hospital Contact : </strong>' . $row['hospital_contact'] . '</p>';
                echo '<p><strong>Service Availability : </strong>' . $row['available'] . '</p>';
                echo '<p><strong>Registration Date : </strong>' . $row['registration_date'] . '</p>';
                echo '</div>';

                // Add a form to upload a photo
                echo '<form action="hospital_profile.php" method="post" enctype="multipart/form-data">';
                echo '    <input type="file" name="photo" accept="image/*">';
                echo '    <input type="submit" value="Upload Photo">';
                echo '</form>';

                // Buttons
                echo '<a href="edit_hospital_profile.php"><button style="margin-top: 10px;">Edit Profile</button></a>';
                echo '<a href="hospital_interface01.html"><button>HOME</button></a>';
                echo '<a href="logout.php"><button>LOGOUT</button></a>';
            } else {
                echo '<p>You are not logged in or there is no data available.</p>';
            }
        ?>
    </div>
</body>
</html>