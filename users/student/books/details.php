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


if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];

    $BookQuery = "SELECT * FROM books WHERE book_id = ?";
    $BookStmt = $conn->prepare($BookQuery);

    if (is_numeric($book_id)) {
        $BookStmt->bind_param('i', $book_id);
        $BookStmt->execute();
        $BookResult = $BookStmt->get_result();

        if ($BookResult->num_rows === 1) {
            $row = $BookResult->fetch_assoc();

            $book_title = $row['book_title'];
            $year = $row['year'];
            $author = $row['author'];
            $publisher = $row['publisher'];
            $section = $row['section'];
            $edition = $row['edition'];
            $volume = $row['volume'];
            $book_borrow_status = $row['book_borrow_status'];
            
        } else {
            echo "Book Not Found" . $BookStmt->error;
        }
    } else {
        echo "Invalid Book ID" . $BookStmt->error;
    }
} else {
    echo "Book ID Not Set" . $BookStmt->error;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo '<title>'. $firstname .' '. $lastname .' / QR - MyLibro </title>'; ?>
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
    <link rel="stylesheet" type="text/css" href="/LibMS/users/student/books/css/details.css">
    <!--Link for NAVBAR and SIDEBAR styling-->
    <link rel="stylesheet" type="text/css" href="/LibMS/users/student/css/navbar-sidebar.css">
    <!--Link for Font Awesome Icons-->
    <link rel="stylesheet" href="/LibMS/resources/icons/fontawesome-free-6.4.0-web/css/all.css">

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
          <a class="nav-link" aria-current="page" href="/LibMS/users/student/index.php"><i class="fa fa-house fa-sm"></i> Home</a>
        </li>
      </ul>

      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="?logout=true"><i class="fa-solid fa-right-from-bracket fa-xs"></i> Logout</a>
        </li>
      </ul>

      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="/LibMS/users/student/index.php">
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
                    <a href="/LibMS/users/student/index.php">
                        <i class="fa fa-house fa-sm"></i>
                        <span class="sidebar-name">
                            Home
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
                    <a href="/LibMS/users/student/books/books.php">
                        <i class="fa fa-book fa-sm"></i>
                        <span class="sidebar-name">
                            Books
                        </span>
                    </a>
                </li>

                <li>
                    <a href="/LibMS/users/student/requests/pending-borrow-requests.php">
                        <i class="fa fa-bookmark fa-sm"></i>
                        <span class="sidebar-name">
                            Pending Borrow Requests
                        </span>
                    </a>
                </li>

                <li>
                    <a href="/LibMS/users/student/history/history.php">
                        <i class="fa fa-clock-rotate-left fa-sm"></i>
                        <span class="sidebar-name">
                            History
                        </span>
                    </a>
                </li>

                <li>
                    <a href="/LibMS/users/student/notification/notification.php">
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
                        <form class="form-box" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

                            <div class="form-group" style="margin-top:20px;">
                                <h2><i class="fa-solid fa-book"></i> Book Details:</h2>
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
                                <label for="bookStatus">Borrow Status:</label>
                                <span id="bookStatus" class="form-control-static"><?php echo $book_borrow_status; ?></span>
                            </div>

                            <div class="form-group">
                                <label for="bookEditionVolume">Edition and Volume:</label>
                                <span id="bookEditionVolume" class="form-control-static"><?php echo $edition; ?></span>,
                                <span id="bookEditionVolume" class="form-control-static"><?php echo $volume; ?></span>
                            </div>


                            <div class="form-group" style="margin-bottom:10px; margin-top:10px;">
                                <button class="btn btn-danger btn-md" style="width:80%;" onclick="deleteBook(<?php echo $book_id; ?>)"><i class="fa-solid fa-trash"></i> Delete</button>
                            </div>

                            <!--<div class="form-group" style="margin-bottom:10px; margin-top:10px;">
                                <a href="/LibMS/users/admin/books/books.php">
                                    <button class="btn btn-primary btn-md" style="width:80%;"><i class="fa-solid fa-arrow-left"></i> Go Back</button>
                                </a>
                            </div>-->


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

</script>

</body>
</html>