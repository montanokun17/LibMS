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

$borrower_user_id = "";
$borrower_username = "";
$book_id = "";
$book_title = "";
$borrow_days = "";
$borrow_status = "";
$request_approval_date = "";
$due_date = "";
$pickup_date = "";


if (isset($_GET['borrow_id'])) {
    $borrow_id = $_GET['borrow_id'];

    $borrowQuery = "SELECT * FROM approved_borrow_requests WHERE borrow_id = ?";
    $borrowStmt = $conn->prepare($borrowQuery);

    if (is_numeric($borrow_id)) {
        $borrowStmt->bind_param('i', $borrow_id);
        $borrowStmt->execute();
        $borrowResult = $borrowStmt->get_result();

        if($borrowResult->num_rows === 1) {
            $row = $borrowResult->fetch_assoc();
            
            $borrower_user_id =  $row['borrower_user_id'];
            $borrower_username =  $row['borrower_username'];
            $book_id = $row['book_id'];
            $book_title = $row['book_title'];
            $borrow_days = $row['borrow_days'];
            $borrow_status = $row['borrow_status'];
            $request_approval_date = $row['request_approval_date'];
            $due_date = $row['due_date'];
            $pickup_date = $row['pickup_date'];
            $approved_by = $row['approved_by'];
            
        } else {
            echo "<script>alert('Request Not Found');</script>";
        }
    } else {
        echo "<script>alert('Invalid Book ID');</script>";
    }
} else {
    echo "<script>alert('Book ID Not Set');</script>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo '<title>'. $firstname .' '. $lastname .' /Verify Return - MyLibro </title>'; ?>
    <!--Link for Tab ICON-->
    <link rel="icon" type="image/x-icon" href="/LibMS/resources/images/logov1.png">
    <!--Link for Bootstrap-->
    <link rel="stylesheet" type="text/css" href="/LibMS/resources/bootstrap/css/bootstrap.min.css"/>
    <script type="text/javascript" src="/LibMS/resources/bootstrap/js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!--Link for JQuery-->
    <script type="text/javascript" src="/LibMS/resources/jquery ui/jquery-ui.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/LibMS/resources/jquery ui/jquery-ui.min.css"/>
    <script type="text/javascript" src="/LibMS/resources/jquery/jquery-3.7.1.min.js"></script>
    <!--Link for CSS File-->
    <link rel="stylesheet" type="text/css" href="/LibMS/users/admin/requests/func/css/verify_return.css">
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

            <li class="nav-item">
            <a class="nav-link" aria-current="page" href="/LibMS/users/admin/requests/issue_requests.php"><i class="fa-solid fa-bookmark fa-xs"></i> Issue Requests</a>
            </li>

            <li class="nav-item">
            <a class="nav-link" aria-current="page" href="/LibMS/users/admin/requests/approved_requests.php"><i class="fa-solid fa-clock-rotate-left fa-xs"></i> Approved Requests</a>
            </li>

            <li class="nav-item">
            <a class="nav-link" aria-current="page" href="/LibMS/users/admin/requests/return_requests.php"><i class="fa-solid fa-rotate-left fa-xs"></i> Pending Return</a>
            </li>

            <li class="nav-item">
            <a class="nav-link" aria-current="page" href="/LibMS/users/admin/requests/renew_requests.php"><i class="fa-solid fa-clock-rotate-left fa-xs"></i> Renewal Requests</a>
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
                    <a href="#">
                        <i class="fa fa-solid fa-qrcode fa-sm"></i>
                        <span class="sidebar-name">
                            QR
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
                        <form class="form-box" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

                            <div class="form-group" style="margin-top:20px;">
                                <h2><i class="fa-solid fa-paper-plane"></i> Verify Book Return:</h2>
                            </div>

                            <div class="form-group" style="margin-top:10px;">
                                <label>Book Title:</label>
                                <span class="form-control-static"><?php echo $book_title; ?></span>
                            </div>

                            <div class="form-group">
                                <label >Borrower Username:</label>
                                <span class="form-control-static"><?php echo $borrower_username; ?></span>
                            </div>

                            <div class="form-group">
                                <label >Borrow Days:</label>
                                <span class="form-control-static"><?php echo $borrow_days; ?></span>
                            </div>

                            <div class="form-group">
                                <label >Reuest Approval Date:</label>
                                <span class="form-control-static"><?php echo $request_approval_date; ?></span>
                            </div>

                            <div class="form-group">
                                <label >Due Date:</label>
                                <span class="form-control-static"><?php echo $due_date; ?></span>
                            </div>

                            <div class="form-group">
                                <label >Approved By:</label>
                                <span class="form-control-static"><?php echo $approved_by; ?></span>
                            </div>

                            <div class="form-group">
                                <label>Book Condition When Returned:</label>
                                <span class="form-control-static">
                                    <select name="book-status" id="book-status" class="book-status" required="">
                                        <option selected disabled>**Select Book Condition When Returned**</option>
                                        <option value="GOOD">GOOD</option>
                                        <option value="DAMAGED">DAMAGED</option>
                                        <option value="DILAPITATED">DILAPITATED</option>
                                    </select>
                                </span>
                            </div>

                            <div class="form-group" style="margin-bottom:10px; margin-top:10px;">
                                <button type="button" class="btn btn-primary btn-md" style="width:80%;" onclick="sendVerifyReturn(<?php echo $borrow_id; ?>)"><i class="fa-solid fa-paper-plane"></i> Verify Book Return</button>
                            </div>


                            <!--
                            <div class="form-group">
                                <label for="book">:</label>
                                <span id="book" class="form-control-static"><?php //echo $; ?></span>
                            </div>
                            -->

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function sendVerifyReturn(borrow_id) {
    var bookstatus = document.getElementById("book-status").value;

    if (bookstatus === "") {
        alert("Please select the current Book condition when returned, for assessment purposes.");
        return;
    }

    // You can perform an AJAX request to the server to handle the database operations
    // For simplicity, let's assume there is a PHP script (borrow_request.php) to handle this

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/LibMS/users/admin/requests/func/return.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            alert(xhr.responseText); // You can customize this based on your response from the server
        }
    };

    // Send data to the server, including the book ID
    xhr.send("book-status=" + bookstatus + "&borrow_id=" + borrow_id);
}
</script>

</body>
</html>