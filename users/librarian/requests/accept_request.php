<?php

session_start();

$servername = "localhost";
$user_name = "root";
$Password = "";
$database = "mylibro";

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

$borrower_user_id = "";
$borrower_username = "";
$book_id = "";
$book_title = "";
$borrow_days = "";
$borrow_status = "";
$request_date = "";
$request_timestamp = "";

if (isset($_GET['borrow_id'])) {
        $borrow_id = $_GET['borrow_id'];

        $borrowQuery = "SELECT * FROM borrow_requests WHERE borrow_id = ?";
        $borrowStmt = $conn->prepare($borrowQuery);

        if (is_numeric($borrow_id)) {
            $borrowStmt->bind_param('i', $borrow_id);
            $borrowStmt->execute();
            $borrowResult = $borrowStmt->get_result();

            if($borrowResult->num_rows === 1) {
                $row = $borrowResult->fetch_assoc();

                $borrower_user_id = $row['borrower_user_id'];
                $borrower_username = $row['borrower_username'];
                $book_id = $row['book_id'];
                $book_title = $row['book_title'];
                $borrow_days = $row['borrow_days'];
                $borrow_status = $row['borrow_status'];
                $request_date = $row['request_date'];
                $request_timestamp = $row['request_timestamp'];
            } else {
                //echo "<script>alert('Request Not Found');</script>";
            }
        } else {
            //echo "<script>alert('Invalid Borrow ID');</script>";
        }
    } else {
        //echo "<script>alert('Borrow ID Not Set');</script>";
    }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo '<title>'. $firstname .' '. $lastname .' / Accept Request - MyLibro </title>'; ?>
    <!--Link for Tab ICON-->
    <link rel="icon" type="image/x-icon" href="/LibMS/resources/images/logov1.png">
    <!--Link for Bootstrap-->
    <link rel="stylesheet" type="text/css" href="/LibMS/resources/bootstrap/css/bootstrap.min.css"/>
    <script type="text/javascript" src="/LibMS/resources/bootstrap/js/bootstrap.min.js"></script>
    <!--Link for CSS File-->
    <link rel="stylesheet" type="text/css" href="/LibMS/users/librarian/requests/css/accept_request.css">
    <!--Link for NAVBAR and SIDEBAR styling-->
    <link rel="stylesheet" type="text/css" href="/LibMS/users/librarian/css/navbar-sidebar.css">
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

            <li class="nav-item">
            <a class="nav-link" aria-current="page" href="/LibMS/users/librarian/requests/issue_requests.php"><i class="fa-solid fa-bookmark fa-xs"></i> Issue Requests</a>
            </li>

            <li class="nav-item">
            <a class="nav-link" aria-current="page" href="/LibMS/users/librarian/requests/approved_requests.php"><i class="fa-solid fa-clock-rotate-left fa-xs"></i> Approved Requests</a>
            </li>

            <li class="nav-item">
            <a class="nav-link" aria-current="page" href="/LibMS/users/librarian/requests/return_requests.php"><i class="fa-solid fa-rotate-left fa-xs"></i> Pending Return</a>
            </li>

            <li class="nav-item">
            <a class="nav-link" aria-current="page" href="/LibMS/users/librarian/requests/renew_requests.php"><i class="fa-solid fa-clock-rotate-left fa-xs"></i> Renewal Requests</a>
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

<div class="main-box">
    <div class="container">
        <div class="row">
            <div class="box-1 col-12">
                <div class="card-body bg-dark">
                    <div class="col-md-8 mx-auto">

                        <form method="POST">

                            <div class="form-group">
                                    <h3>Designate a Pick up Date for the Borrowed Book of the Student:</h3>
                            </div>


                            <div class="info-box">
                                <p>Book: <?php echo $book_title; ?></p>
                                <p>Status: <?php echo $borrow_status; ?></p>
                                <p>Borrower: <?php echo $borrower_username; ?></p>
                                <p>Borrow Days: <?php echo $borrow_days; ?></p>
                                <p>Sent Request Date: <?php echo $request_date; ?></p>
                            </div>

                            <div class="form-group" style="margin-top:10px; margin-bottom:10px;">
                                <input type="date" name="pickup-date" id="pickup-date" onchange="validateDate()" required style="width:50%;">
                            </div>

                            <button class="btn btn-success btn-sm" type="submit" style="width:50%;" onclick="sendApproveRequest(<?php echo $book_id; ?>)"><i class="fa fa-solid fa-calendar-days fa-sm"></i> Set & Grant</button>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
        // Set the default value to the current date in the format "YYYY-MM-DD"
        function setDefaultDate() {
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
            var yyyy = today.getFullYear();

            var currentDate = yyyy + '-' + mm + '-' + dd;
            document.getElementById('pickup-date').value = currentDate;
        }

        // Call the function to set the default date when the page loads
        window.onload = setDefaultDate;

        // Optional: You can add a validation function to ensure the selected date is not in the past
        function validateDate() {
            var selectedDate = document.getElementById('pickup-date').value;
            var today = new Date().toISOString().split('T')[0];

            if (selectedDate < today) {
                alert("Please select a future date.");
                document.getElementById('pickup-date').value = today;
            }
        }

        function sendApproveRequest(book_id) {
            var xhr = new XMLHttpRequest();
            var url = "/LibMS/users/librarian/requests/request_accept.php";
            var pickup_date = document.getElementById('pickup-date').value;
            var params = "book_id=" + book_id + "&pickup_date=" + pickup_date; // Add other parameters as needed

            xhr.open("POST", url, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Handle the response from the server
                    alert(xhr.responseText);
                }
            };

            xhr.send(params);
        }

    </script>


</body>
</html>