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
$schlvl = "";
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
        $schlvl = $row['schlvl'];
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

if (isset($_GET['notif_id'])) {
    $notifId = $_GET['notif_id'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE notifications SET read_status = 'READ' WHERE notif_id = ?");
    $stmt->bind_param("i", $notifId); // 'i' represents integer, adjust accordingly if notif_id is of a different type

    // Execute the update
    if ($stmt->execute()) {
        // Update successful
        // echo 'Notification marked as read successfully';
    } else {
        // Update failed
        echo 'Error updating notification: ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    // Retrieve additional information about the notification
    $notif_sender = "";
    $notif_receiver = "";
    $notif_message = "";
    $notif_timestamp = "";

    // Use a different variable name for the statement to avoid conflicts
    $notifStmt = $conn->prepare("SELECT * FROM notifications WHERE notif_id = ?");
    if ($notifStmt) {
        $notifStmt->bind_param('i', $notifId);
        $notifStmt->execute();
        $notifResult = $notifStmt->get_result();

        if ($notifResult->num_rows === 1) {
            $row = $notifResult->fetch_assoc();

            $notif_sender = $row['sender_user_id'];
            $notif_receiver = $row['receiver_user_id'];
            $notif_message = $row['notification_message'];
            $notif_timestamp = $row['notif_timestamp'];
        } else {
            echo "Notification was not found.";
        }

        // Close the statement for retrieving additional information
        $notifStmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    // Handle the case where notif_id is not provided
    echo 'Invalid request';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo '<title>'. $firstname .' '. $lastname .' / Admin: Notifications - MyLibro </title>'; ?>
    <!--Link for Tab ICON-->
    <link rel="icon" type="image/x-icon" href="/LibMS/resources/images/logov1.png">
    <!--Link for Bootstrap-->
    <link rel="stylesheet" type="text/css" href="/LibMS/resources/bootstrap/css/bootstrap.min.css"/>
    <script type="text/javascript" src="/LibMS/resources/bootstrap/js/bootstrap.min.js"></script>
    <!--Link for CSS File-->
    <link rel="stylesheet" type="text/css" href="/LibMS/users/admin/notification/css/open-notif.css">
    <!--Link for NAVBAR and SIDEBAR styling-->
    <link rel="stylesheet" type="text/css" href="/LibMS/users/admin/css/navbar-sidebar.css">
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
          <a class="nav-link" aria-current="page" href="#"><i class="fa-solid fa-cogs fa-xs"></i> Login Page Banner</a>
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

                                //echo '<div class="container col-sm-6 center">';
                                // Use the "width" and "height" attributes to resize the image
                                echo '<img src="data:image/png;base64,' . base64_encode($row["user_pic_data"]) . '" width="40" height="40" class="rounded-circle"/>';
                                //echo '</div>';
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
                        <i class="fa fa-house fa-sm"></i>
                        <span class="sidebar-name">
                            Home
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
                    <a href="/LibMS/users/admin/qrpage/qr-landing-page.php">
                        <i class="fa fa-solid fa-qrcode fa-sm"></i>
                        <span class="sidebar-name">
                            QR
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
                    <a href="/LibMS/users/admin/notification/notification.php">
                        <i class="fa fa-bell fa-sm"></i>
                        <span class="sidebar-name">
                            Notifications
                        </span>
                    </a>
                </li>

                <li>
                    <a href="/LibMS/users/admin/requests/issue_requests.php">
                        <i class="fa fa-bookmark fa-sm"></i>
                        <span class="sidebar-name">
                            Issue Requests
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
                    <a href="/LibMS/users/admin/logs/recent-deletion-books.php">
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
        <div class="row">
            <div class="box-1 col-12">
                <div class="card-body bg-dark">
                    <div class="col-md-8 mx-auto">

                        <div class="container head-title bg-dark">
                            <p class="sender-head">From: <?php echo $notif_sender; ?></p>
                        </div>
                        <br>

                        <div class="container message-body bg-dark">
                            <label for="message-box">Notification Content:</label>
                            <p class="message-box"><?php echo $notif_message; ?></p>
                        </div><br>

                        <div class="container message-body bg-dark">
                            <label for="message-box">Received:</label>
                            <p class="message-box"><?php echo $notif_timestamp; ?></p>
                        </div>

                        <div class="action-box">
                            <a href="/LibMS/users/admin/notification/notification.php">
                                <button class="btn btn-primary btn-sm" style="width:50%; margin-bottom:10px;"><i class="fa fa-solid fa-arrow-left fa-sm"></i> Go Back</button>
                            </a>

                            <a href="/LibMS/users/admin/requests/issue_requests.php">
                                <button class="btn btn-primary btn-sm" style="width:50%; margin-bottom:10px;"><i class="fa-solid fa-arrow-right-to-bracket fa-sm"></i> Go to Requests Pages</button>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>