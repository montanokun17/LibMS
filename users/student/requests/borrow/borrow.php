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

if ($_SESSION['acctype'] === 'Student') {

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

// Validate and process the form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookId = isset($_POST['book_id']) ? $_POST['book_id'] : null;
    $borrowDays = isset($_POST['borrow_days']) ? $_POST['borrow_days'] : null;

    
    if (!is_numeric($bookId)) {
        // Handle the error, e.g., redirect with an error message
        header("Location: /LibMS/users/student/requests/borrow/borrow.php?error=InvalidBookId");
        exit();
    }

    // Add your logic to store the borrow request, e.g., in a database

    // Redirect back to the page with a success message
    header("Location: /LibMS/users/student/requests/borrow/borrow.php?success=1");
    exit();
}

// Fetch book information
$book_title = "";
$section = "";
$volume = "";
$edition = "";
$author = "";
$year = "";
$publisher = "";
$isbn = "";
$status = "";

// Check if the book_id is set in the query parameters
if (isset($_GET['book_id'])) {
    $bookId = $_GET['book_id'];

    // Use prepared statements to prevent SQL injection
    $bookQuery = "SELECT * FROM books WHERE book_id = ?";
    $bookStmt = $conn->prepare($bookQuery);

    // Validate bookId before binding
    if (is_numeric($bookId)) {
        $bookStmt->bind_param('i', $bookId);
        $bookStmt->execute();
        $bookResult = $bookStmt->get_result();

        if ($bookResult->num_rows === 1) {
            $row = $bookResult->fetch_assoc();

            $book_title = $row['book_title'];
            $section = $row['section'];
            $volume = $row['volume'];
            $edition = $row['edition'];
            $author = $row['author'];
            $year = $row['year'];
            $publisher = $row['publisher'];
            $isbn = $row['isbn'];
            $status = $row['status'];
        } else {
            // Handle the case when the book is not found
            header("Location: /LibMS/users/student/requests/borrow/borrow.php?error=BookNotFound");
            exit();
        }
    } else {
        // Handle the case when the book_id is not a valid number
        header("Location: /LibMS/users/student/requests/borrow/borrow.php?error=InvalidBookId");
        exit();
    }
} else {
    // Handle the case when book_id is not set in the query parameters
    header("Location: /LibMS/users/student/requests/borrow/borrow.php?error=BookIdNotSet");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo '<title>'. $firstname .' '. $lastname .' /Books - MyLibro </title>'; ?>
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
    <link rel="stylesheet" type="text/css" href="/LibMS/users/student/requests/borrow/css/borrow.css">
    <!--Link for NAVBAR and SIDEBAR styling-->
    <link rel="stylesheet" type="text/css" href="/LibMS/users/student/css/navbar-sidebar.css">
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
                    <a href="/LibMS/users/student/index.php">
                        <i class="fa fa-user fa-sm"></i>
                        <span class="sidebar-name">
                            Dashboard
                        </span>
                    </a>
                </li>

                <li>
                    <a href="/LibMS/users/student/profile/user_settings.php">
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
                    <a href="/LibMS/users/student/books/books.php">
                        <i class="fa fa-book fa-sm"></i>
                        <span class="sidebar-name">
                            Books
                        </span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="fa fa-bookmark fa-sm"></i>
                        <span class="sidebar-name">
                            Pending Borrow Requests
                        </span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="fa fa-clock-rotate-left fa-sm"></i>
                        <span class="sidebar-name">
                            Books Borrow Issuances/Return History
                        </span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="fa fa-bell fa-sm"></i>
                        <span class="sidebar-name">
                            Notifications
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
                        <form class="form-box">

                            <div class="form-group" style="margin-top:20px;">
                                <h2><i class="fa-solid fa-paper-plane"></i> Send Book Borrow Request:</h2>
                            </div>

                            <div class="form-group" style="margin-top:10px;">
                                <label for="bookTitle">Book Title:</label>
                                <span id="bookTitle" class="form-control-static"><?php echo $book_title; ?></span>
                            </div>

                            <div class="form-group">
                                <label for="bookYear">Year:</label>
                                <span id="bookYear" class="form-control-static"><?php echo $year; ?></span>
                            </div>

                            <div class="form-group">
                                <label for="bookAuthor">Author:</label>
                                <span id="bookAuthor" class="form-control-static"><?php echo $author; ?></span>
                            </div>

                            <div class="form-group">
                                <label for="bookPublisher">Publisher:</label>
                                <span id="bookPublisher" class="form-control-static"><?php echo $publisher; ?></span>
                            </div>

                            <div class="form-group">
                                <label for="bookSection">Section:</label>
                                <span id="bookSection" class="form-control-static"><?php echo $section; ?></span>
                            </div>

                            <div class="form-group">
                                <label for="bookEditionVolume">Edition and Volume:</label>
                                <span id="bookEditionVolume" class="form-control-static"><?php echo $edition; ?></span>,
                                <span id="bookEditionVolume" class="form-control-static"><?php echo $volume; ?></span>
                            </div>

                            <div class="form-group">
                                <label for="bookBorrow">How Many Days Do You Want to Borrow the Book?</label>
                                <span id="bookBorrow" class="form-control-static">
                                    <select name="borrowDays" id="borrowDays" class="borrowDD">
                                        <option value=""></option>
                                        <option selected disabled>**Select No. of Days of Book Loan**</option>
                                        <option value="1">1 Day</option>
                                        <option value="2">2 Days</option>
                                        <option value="3">3 Days</option>
                                        <option value="4">4 Days</option>
                                        <option value="5">5 Days</option>
                                        <option value="6">6 Days</option>
                                    </select>
                                </span>
                            </div>

                            <div class="form-group" style="margin-bottom:10px; margin-top:10px;">
                                <button class="btn btn-primary btn-md" style="width:80%;"><i class="fa-solid fa-paper-plane"></i> Send Borrow Request</button>
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


</body>
</html>