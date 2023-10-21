<?php
session_start();

$servername = "localhost"; // Replace with your server name if different
$user_name = "root"; // Replace with your database username
$Password = ""; // Replace with your database password
$database = "mylibro"; // Replace with your database name

// Create a connection
$conn = new mysqli($servername, $user_name, $Password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the logout parameter is set
if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to the login page
    header('Location: /LibMS/main/login.php');
    exit();
}

// Initialize variables with default values
$firstname = "";
$lastname = "";
$acctype = "";
$email = "";
$idNo = "";
$username = "";
$con_num = "";
$brgy = "";

if ($_SESSION['acctype'] === 'Admin') {

    $idNo = $_SESSION['id_no'];
    $username = $_SESSION['username'];

    // Prepare and execute the SQL query
    $query = "SELECT * FROM users WHERE id_no = ? AND username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $idNo, $username);
    $stmt->execute();

    // Fetch the result
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Retrieve the user's information
        $firstname = $row['firstname'];
        $lastname = $row['lastname'];
        $idNo = $row['id_no'];
        $acctype = $row['acctype'];
        $username = $row['username'];
        $email = $row['email'];
        $con_num = $row['con_num'];
        $brgy = $row['brgy'];
        
    } else {
        // Handle case when user is not found
        // For example, redirect to an error page or display an error message
        echo "User not found!";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo '<title>'. $firstname .' '. $lastname .' /Admin - MyLibro </title>'; ?>
    <!--Link for Tab ICON-->
    <link rel="icon" type="image/x-icon" href="/LibMS/resources/images/logov1.png">
    <!--Link for Bootstrap-->
    <link rel="stylesheet" type="text/css" href="/LibMS/resources/bootstrap/css/bootstrap.min.css"/>
    <script type="text/javascript" src="/LibMS/resources/bootstrap/js/bootstrap.min.js"></script>
    <!--Link for CSS File-->
    <link rel="stylesheet" type="text/css" href="/LibMS/users/admin/css/navbar-sidebar.css">
    <!--Link for CSS File-->
    <link rel="stylesheet" type="text/css" href="/LibMS/users/admin/profile/css/user_settings.css">
    <!--Link for Font Awesome Icons-->
    <link rel="stylesheet" href="/LibMS/resources/icons/fontawesome-free-6.4.0-web/css/all.css">
    <!--Link for Google Font-->
    <link rel="stylesheet" href="/LibMS/resources/fonts/fonts.css"/>

</head>

<body>

<!--NAVBAR-->
<nav class="navbar navbar-expand-lg" id="navbar">
  <div class="container-fluid">
    <a class="navbar-brand" href="#"><img src="/LibMS/resources/images/logov1.png" width="30px" height="30px"></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="#"><i class="fa-solid fa-cogs fa-xs"></i> Homepage Settings</a>
        </li>
      </ul>

      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="?logout=true"><i class="fa-solid fa-right-from-bracket fa-xs"></i> Logout</a>
        </li>
      </ul>

      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="#">

            <?php
                if (isset($_SESSION['id_no']) && isset($_SESSION['username'])) {
                    $idNo = $_SESSION['id_no'];
                    $username = $_SESSION['username'];
                                                
                    // Query to retrieve the necessary columns from the database
                    $UserPicPath = "SELECT user_pic_data, user_pic_type FROM user_pics WHERE user_id = ? AND username = ?";
                    $statement = $conn->prepare($UserPicPath);
                    $statement->bind_param("is", $idNo, $username);
                                                
                        if ($statement->execute()) {
                            $result = $statement->get_result();
                                                
                            if ($row = $result->fetch_assoc()) {
                                // Define the desired width and height for the image
                                $width = 200; // Set your desired width
                                $height = 200; // Set your desired height
                                                
                                echo '<div class="container col-sm-6 center">';
                                // Use the "width" and "height" attributes to resize the image
                                echo '<img src="data:image/png;base64,' . base64_encode($row["user_pic_data"]) . '" width="40" height="40" class="rounded-circle"/>';
                                echo '</div>';
                            } else {
                                // If not found in the database, display the default image
                                echo '<img src="/LibMS/resources/images/user.png" width=40" height="40" class="rounded-circle" style="margin-top: 10px; margin-bottom: 10px;">';
                            }
                        } else {
                            // Error in executing the SQL query
                            echo '<img src="/LibMS/resources/images/user.png" width="200" height="200" class="rounded-circle" style="margin-top: 10px; margin-bottom: 10px;">';
                                                    }
                    }
                                                    
            ?>

          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<!--NAVBAR-->

<!--SIDEBAR-->
    <div id="sidebar">
            <ul>
                <li></li>
                <li>
                    <a href="/LibMS/users/admin/index.php">
                        <i class="fa fa-user fa-sm"></i>
                        <span class="sidebar-name">
                            Dashboard
                        </span>
                    </a>
                </li>

                <li>
                    <a href="/LibMS/users/admin/profile/user_settings.php">
                        <i class="fa fa-cogs fa-sm"></i>
                        <span class="sidebar-name">
                            User Options
                        </span>
                    </a>
                </li>

                <li>
                    <a href="/LibMS/users/admin/profile/accounts.php">
                        <i class="fa fa-users fa-sm"></i>
                        <span class="sidebar-name">
                            Accounts
                        </span>
                    </a>
                </li>
                
                <li>
                    <a href="#">
                        <i class="fa fa-solid fa-qrcode fa-sm"></i>
                        <span class="sidebar-name">
                            QR Code and ID Card
                        </span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="fa fa-comments fa-sm"></i>
                        <span class="sidebar-name">
                            Messages
                        </span>
                    </a>
                </li>

                <li>
                    <a href="/LibMS/users/admin/books/books.php">
                        <i class="fa fa-book fa-sm"></i>
                        <span class="sidebar-name">
                            Books
                        </span>
                    </a>
                </li>

                <li>
                    <a href="/LibMS/users/admin/books/add_books.php">
                        <i class="fa fa-plus fa-sm"></i>
                        <span class="sidebar-name">
                            Add a Book
                        </span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="fa fa-bars fa-sm"></i>
                        <span class="sidebar-name">
                            Issue/Return Requests
                        </span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="fa fa-book fa-sm"></i>
                        <span class="sidebar-name">
                            Issued/Returned Books Log
                        </span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="fa fa-trash fa-sm"></i>
                        <span class="sidebar-name">
                            Recent Deletion Books
                        </span>
                    </a>
                </li>

            </ul>

            <ul>
                <li>
                    <a href="?logout=true">
                        <i class="fa fa-right-from-bracket fa-sm"></i>
                        <span class="sidebar-name">
                            Logout
                        </span>
                    </a>
                </li>
            </ul>
    </div>
<!--SIDEBAR-->

<div class="main-box">
    <div class="container">
            <ul class="mini-sidebar">
                <li class="mini-item">
                    <span class="item-content">
                        My ID Card
                    </span>
                </li>

                <li class="mini-item active">
                    <span class="item-content">
                        My Account's Information
                    </span>
                </li>

                <li class="mini-item">
                    <span class="item-content">
                        Homepage Banners
                    </span>
                </li>

                <li class="mini-item">
                    <span class="item-content">
                        My ID Card
                    </span>
                </li>

                <div class="dash-box-1">
                    <div class="container">
                        <div class="dash-content">
                            <div class="content-box">
                                <table class="dash-table">
                                    <thead></thead>
                                    <tbody>
                                        <tr>
                                            <td class="box-1">
                                                <form class="dash-form" action="#" method="POST">

                                                <label for="firstname">Firstname:</label>
                                                <div class="form-group">
                                                    <input type="text" name="firstname" id="firstname" required="" placeholder="<?php echo $firstname;?>">
                                                </div>

                                                <label for="lastname">Lastname:</label>
                                                <div class="form-group">
                                                    <input type="text" name="lastname" id="lastname" required="" placeholder="<?php echo $lastname;?>">
                                                </div>

                                                <label for="username">Username:</label>
                                                <div class="form-group">
                                                    <input type="text" name="username" id="username" required="" placeholder="<?php echo $username;?>">
                                                </div>
                                                
                                                <label for="email">Email Address:</label>
                                                <div class="form-group">
                                                    <input type="text" name="email" id="email" required="" placeholder="<?php echo $email;?>">
                                                </div>

                                                <label for="con_num">Contact Number:</label>
                                                <div class="form-group">
                                                    <input type="text" name="con_num" id="con_num" required="" placeholder="<?php echo $con_num;?>">
                                                </div>

                                                <label for="brgy">Barangay: </label>
                                                        <select name="brgy" class="form-select" id="brgy">
                                                            <option selected disabled><?php echo $brgy; ?></option>
                                                                <option value="Bagong Ilog">Bagong Ilog</option>
                                                                <option value="Bagong Katipunan">Bagong Katipunan</option>
                                                                <option value="Bambang">Bambang</option>
                                                                <option value="Buting">Buting</option>
                                                                <option value="Caniogan">Caniogan</option>
                                                                <option value="Dela Paz">Dela Paz</option>
                                                                <option value="Kalawaan">Kalawaan</option>
                                                                <option value="Kapasigan">Kapasigan</option>
                                                                <option value="Kapitolyo">Kapitolyo</option>
                                                                <option value="Malinao">Malinao</option>
                                                                <option value="Manggahan">Manggahan</option>
                                                                <option value="Maybunga">Maybunga</option>
                                                                <option value="Orando">Orando</option>
                                                                <option value="Palatiw">Palatiw</option>
                                                                <option value="Pinagbuhatan">Pinagbuhatan</option>
                                                                <option value="Pineda">Pineda</option>
                                                                <option value="Rosario">Rosario</option>
                                                                <option value="Sagad">Sagad</option>
                                                                <option value="San Antonio">San Antonio</option>
                                                                <option value="San Joaquin">San Joaquin</option>
                                                                <option value="San Jose">San Jose</option>
                                                                <option value="San Miguel">San Miguel</option>
                                                                <option value="San Nicolas">San Nicolas</option>
                                                                <option value="Santa Cruz">Santa Cruz</option>
                                                                <option value="Santa Lucia">Santa Lucia</option>
                                                                <option value="Santa Rosa">Santa Rosa</option>
                                                                <option value="Santo Tomas">Santo Tomas</option>
                                                                <option value="Santolan">Santolan</option>
                                                                <option value="Sumilang">Sumilang</option>
                                                                <option value="Ugong">Ugong</option>
                                                        </select>

                                                <button type="submit" class="btn btn-primary btn-sm" id="save-btn">
                                                    <i class="fa-solid fa-floppy-disk"></i> Save
                                                </button>

                                                </form>

                                            </td>

                                            <td class="box-2">

                                            <label class="dp-label" for="user_picture">Account User Picture:</label>

                                            <div class="dp-box center">

                                                <?php 
                                                
                                                if (isset($_SESSION['id_no']) && isset($_SESSION['username'])) {
                                                    $idNo = $_SESSION['id_no'];
                                                    $username = $_SESSION['username'];
                                                
                                                    // Query to retrieve the necessary columns from the database
                                                    $UserPicPath = "SELECT user_pic_data, user_pic_type FROM user_pics WHERE user_id = ? AND username = ?";
                                                    $statement = $conn->prepare($UserPicPath);
                                                    $statement->bind_param("is", $idNo, $username);
                                                
                                                    if ($statement->execute()) {
                                                        $result = $statement->get_result();
                                                
                                                        if ($row = $result->fetch_assoc()) {
                                                            // Define the desired width and height for the image
                                                            $width = 200; // Set your desired width
                                                            $height = 200; // Set your desired height
                                                
                                                            echo '<div class="container col-sm-6 center">';
                                                            // Use the "width" and "height" attributes to resize the image
                                                            echo '<img src="data:image/png;base64,' . base64_encode($row["user_pic_data"]) . '" width="' . $width . '" height="' . $height . '" class="rounded-circle"/>';
                                                            echo '</div>';
                                                        } else {
                                                            // If not found in the database, display the default image
                                                            echo '<img src="/LibMS/resources/images/user.png" width="200" height="200" class="rounded-circle" style="margin-top: 10px; margin-bottom: 10px;">';
                                                        }
                                                    } else {
                                                        // Error in executing the SQL query
                                                        echo '<div class="text-center mb-3">';
                                                        echo '<p>Error in executing the SQL query.</p>';
                                                        echo '</div>';
                                                    }
                                                
                                                    $statement->close();
                                                } else {
                                                    // User is not logged in
                                                    echo '<div class="text-center mb-3">';
                                                    echo '<p>You are not logged in.</p>';
                                                    echo '</div>';
                                                }
                                                
                                                ?>
                                                
                                            </div>
                                            

                                                <!-- HTML form for image upload -->
                                            <form action="#" method="post" enctype="multipart/form-data" class="dp-form">
                                                <input type="file" name="image" style="font-size: 13px; margin-top: 10px; margin-bottom: 10px;">
                                                <br>
                                                <div class="button-div">
                                                    <button type="submit" name="upload" class="btn btn-primary" id="upload-btn"><i class="fa fa-solid fa-image"></i> Upload Picture</button>
                                                </div>
                                                
                                            </form>

                                            <?php

                                            if (isset($_POST['upload'])) {
                                            if (isset($_FILES['image'])) {
                                                // Define the allowed file extensions and types
                                                $allowedExtensions = ["jpeg", "png", "jpg"];
                                                $allowedTypes = ["image/jpeg", "image/jpg", "image/png"];

                                                // Get file details
                                                $img_name = $_FILES['image']['name'];
                                                $img_type = $_FILES['image']['type'];
                                                $tmp_name = $_FILES['image']['tmp_name'];
                                                $img_size = $_FILES['image']['size'];

                                                // Get the file extension
                                                $img_explode = explode('.', $img_name);
                                                $img_ext = end($img_explode);

                                                // Check if the extension and type are valid
                                                if (in_array($img_ext, $allowedExtensions) && in_array($img_type, $allowedTypes)) {
                                                    // Read the image file content
                                                    $imageData = file_get_contents($tmp_name);
                                                    $imageType = $img_type;

                                                    $db = new PDO('mysql:host=localhost;dbname=mylibro', 'root', '');

                                                    $user_id = $idNo; // Replace with the actual user ID
                                                    $username = $username; // Replace with the actual username

                                                    // Check if a record with the same user_id and username already exists
                                                    $existingRecordQuery = "SELECT id FROM user_pics WHERE user_id = :user_id AND username = :username";
                                                    $existingRecordStmt = $db->prepare($existingRecordQuery);
                                                    $existingRecordStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                                                    $existingRecordStmt->bindParam(':username', $username, PDO::PARAM_STR);
                                                    $existingRecordStmt->execute();

                                                    if ($existingRecordStmt->rowCount() > 0) {
                                                        // A record already exists, update it
                                                        $updateQuery = "UPDATE user_pics SET user_pic_data = :imageData, user_pic_type = :imageType WHERE user_id = :user_id AND username = :username";
                                                        $stmt = $db->prepare($updateQuery);
                                                    } else {
                                                        // No record exists, insert a new record
                                                        $query = "INSERT INTO user_pics (user_id, username, user_pic_data, user_pic_type) VALUES (:user_id, :username, :imageData, :imageType)";
                                                        $stmt = $db->prepare($query);
                                                    }

                                                    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                                                    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                                                    $stmt->bindParam(':imageData', $imageData, PDO::PARAM_LOB);
                                                    $stmt->bindParam(':imageType', $imageType, PDO::PARAM_STR);

                                                    if ($stmt->execute()) {
                                                        echo "<p style='border: 1px solid green; padding: 5px; margin-top: 10px; border-radius: 10px;'>
                                                        Image uploaded and updated in the database successfully.</p>";
                                                    } else {
                                                        echo "<p style='border: 1px solid red; padding: 5px; margin-top: 10px; border-radius: 10px;'>
                                                        Error updating or inserting image into the database.</p>";
                                                    }
                                                } else {
                                                    echo "<p style='border: 1px solid red; padding: 5px; margin-top: 10px; border-radius: 10px;'>
                                                    Invalid file type or extension. Please upload a valid image file.</p>";
                                                }
                                            }
                                        }

                                            ?>


                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </ul>
    </div>
</div>



</body>
</html>