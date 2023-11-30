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

if ($_SESSION['acctype'] === 'Librarian') {

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
    <?php echo '<title>'. $firstname .' '. $lastname .' / Librarian - MyLibro </title>'; ?>
    <!--Link for Tab ICON-->
    <link rel="icon" type="image/x-icon" href="/LibMS/resources/images/logov1.png">
    <!--Link for Bootstrap-->
    <link rel="stylesheet" type="text/css" href="/LibMS/resources/bootstrap/css/bootstrap.min.css"/>
    <script type="text/javascript" src="/LibMS/resources/bootstrap/js/bootstrap.min.js"></script>
    <!--Link for CSS File-->
    <link rel="stylesheet" type="text/css" href="/LibMS/users/librarian/index.css">
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
          <a class="nav-link" aria-current="page" href="#"><i class="fa-solid fa-house fa-xs"></i> Home</a>
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
            <img src="/LibMSv1/resources/images/user.png" 
            width="40" height="40" style="border:1px solid #000000;" class="rounded-circle">
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
                    <a href="#">
                        <i class="fa fa-user fa-sm"></i>
                        <span class="sidebar-name">
                            Dashboard
                        </span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="fa fa-cogs fa-sm"></i>
                        <span class="sidebar-name">
                            User Options
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
                    <a href="#">
                        <i class="fa fa-solid fa-qrcode fa-sm"></i>
                        <span class="sidebar-name">
                            QR
                        </span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="fa fa-book fa-sm"></i>
                        <span class="sidebar-name">
                            Books
                        </span>
                    </a>
                </li>

                <li>
                    <a href="/LibMS/users/admin/logs/history.php">
                        <i class="fa fa-book fa-sm"></i>
                        <span class="sidebar-name">
                            Books Log
                        </span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="fa fa-clock-rotate-left fa-sm"></i>
                        <span class="sidebar-name">
                            Book Borrow Requests
                        </span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="fa fa-clock-rotate-left fa-sm"></i>
                        <span class="sidebar-name">
                            Renewal Requests
                        </span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="fa fa-clock-rotate-left fa-sm"></i>
                        <span class="sidebar-name">
                             Pending Returns
                        </span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="fa fa-clock-rotate-left fa-sm"></i>
                        <span class="sidebar-name">
                             Books Deletion History
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

<!-- ID Card Container -->
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <p class="text-center">MyLibro ID</p>
                </div>
                <div class="card-body">
                    <!-- User's Profile Image -->
                    <div class="text-center mb-2">
                        <img src="/LibMS/resources/images/logov1.png" 
                            width="75" height="75" class="Idlogo">

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
                                            // Use the "width" and "height" attributes to resize the image
                                            echo '<img src="data:image/png;base64,' . base64_encode($row["user_pic_data"]) . '" width="100" height="100" class="rounded-circle"/>';
                                            
                                        } else {
                                            // If not found in the database, display the default image
                                            echo '<img src="/LibMS/resources/images/user.png" width=100" height="100" class="rounded-circle" style="margin-top: 10px; margin-bottom: 10px;">';
                                        }
                                    } else {
                                        // Error in executing the SQL query
                                        echo '<img src="/LibMS/resources/images/user.png" width="100" height="100" class="rounded-circle" style="margin-top: 10px; margin-bottom: 10px;">';
                                                                }
                                }
                                                            
                            ?>
                            
                    </div>
                    <!-- User's Details -->
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>Name:</strong> <?php echo "$firstname $lastname"; ?>
                        </li>
                        <li class="list-group-item">
                            <strong>ID Number:</strong> <?php echo "$idNo"; ?>
                        </li>
                        <li class="list-group-item">
                            <strong>Email:</strong> <?php echo "$email"; ?>
                        </li>
                        <li class="list-group-item">
                            <strong>Account Type:</strong> <?php echo "$acctype"; ?>
                        </li>
                        <li class="list-group-item">
                            <strong>Barangay:</strong> <?php echo "$brgy"; ?>
                        </li>
                    </ul>

                    <?php
                    // Check if the user is logged in
                    if (isset($_SESSION['id_no']) && isset($_SESSION['username'])) {
                        $idNo = $_SESSION['id_no'];
                        $username = $_SESSION['username'];

                        // Query to retrieve the necessary columns from the database
                        $qrCodePath = "SELECT qr_code_data, qr_code_type FROM qr_codes WHERE user_id = ? AND username = ?";
                        $statement = $conn->prepare($qrCodePath);
                        $statement->bind_param("is", $idNo, $username);

                        if ($statement->execute()) {
                            $result = $statement->get_result();

                            if ($row = $result->fetch_assoc()) {
                                // Define the desired width and height for the image
                                $width = 150; // Set your desired width
                                $height = 150; // Set your desired height

                                echo '<div class="container col-sm-6 center">';
                                // Use the "width" and "height" attributes to resize the image
                                echo '<img src="data:image/png;base64,' . base64_encode($row["qr_code_data"]) . '" width="' . $width . '" height="' . $height . '"/>';
                                echo '</div>';
                            } else {
                                // QR code not found in the database
                                echo '<div class="text-center mb-3">';
                                echo '<p><i class="fa fa-solid fa-triangle-exclamation fa-sm"></i> QR Code not found.</p>';
                                echo '</div>';
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
            </div>
        </div>
    </div>
</div>


</body>
</html>