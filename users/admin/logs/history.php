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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo '<title>'. $firstname .' '. $lastname .' / History - MyLibro </title>'; ?>
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
    <link rel="stylesheet" type="text/css" href="/LibMS/users/admin/logs/css/history.css">
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
          <a class="nav-link" aria-current="page" href="/LibMS/users/admin/logs/history.php"><i class="fa-solid fa-clock-rotate-left fa-xs"></i> History</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="/LibMS/users/admin/logs/pickup-logs.php"><i class="fa-solid fa-book fa-xs"></i> Pickup Logs</a>
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


<div class="table-box">
    <div class="container col-12 col-md-10">
        <div class="container">
            <div class="row">
                <div class="inner-box">
                    <div class="container-fluid">

                    <?php

                    $query = "SELECT * FROM book_log_history ORDER BY request_timestamp DESC";

                    function getHistoryByPagination($conn, $query, $offset, $limit) {
                    $query .= " LIMIT $limit OFFSET $offset"; // Append the LIMIT and OFFSET to the query for pagination
                    $result = mysqli_query($conn, $query);

                    return $result;
                    }

                    $totalHistoryQuery = "SELECT COUNT(*) as total FROM book_log_history";
                    $totalHistoryResult = mysqli_query($conn, $totalHistoryQuery);
                    $totalHistory = mysqli_fetch_assoc($totalHistoryResult)['total'];


                    // Number of books to display per page
                    $limit = 7;

                    // Get the current page number from the query parameter
                    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;

                    // Calculate the offset for the current page
                    $offset = ($page - 1) * $limit;

                    // Get the books for the current page
                    $result = getHistoryByPagination($conn, $query, $offset, $limit);

                    // Check if the query executed successfully
                    if ($result && mysqli_num_rows($result) > 0) {
                        echo '<div id="notifs-list-container">';
                        echo '<table id="dataTable">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th>User ID</th>';
                        echo '<th>Username</th>';
                        echo '<th>Book Title</th>';
                        echo '<th>Borrow Days</th>';
                        echo '<th>Availability Status</th>';
                        echo '<th>Request Date</th>';
                        echo '<th>Action Performed</th>';
                        echo '<th>Action Performed By</th>';
                        echo '<th>Timestamp</th>';
                        echo '<th>Action</th>';
                        //echo '<th style="width:18%;">Action</th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';

                        while ($history = mysqli_fetch_assoc($result)) {
                            echo '<tr>';
                            echo '<td>' . $history['borrower_user_id'] . '</td>';
                            echo '<td>' . $history['borrower_username'] . '</td>';
                            echo '<td>' . $history['book_title'] . '</td>';
                            echo '<td>' . $history['borrow_days'] . '</td>';
                            echo '<td>' . $history['borrow_status'] . '</td>';
                            echo '<td>' . $history['request_date'] . '</td>';
                            echo '<td>' . $history['action_performed'] . '</td>';
                            echo '<td>' . $history['action_performed_by'] . '</td>';
                            echo '<td>' . $history['action_timestamp'] . '</td>';
                            echo '<td>
                                <a href="">
                                <button class="btn btn-danger btn-sm"><i class="fa fa-solid fa-trash fa-sm"></i> Delete</button>
                                </a>
                                </td>';
                            echo '</tr>';
                        }

                        echo '</tbody>';
                        echo '</table>';


                        // Calculate the total number of pages
                        $totalPages = ceil($totalHistory / $limit);
                        if ($totalPages > 1) {
                            echo '
                            <div class="pagination-buttons" style="margin-top: 10px;
                            margin-left: 70px;
                            ">
                                ';

                            if ($page > 1) {
                                echo '<a href="?page='.($page - 1).'" class="btn btn-primary btn-sm" id="previous" style="padding: 10px; width:10%;"><i class="fa-solid fa-angle-left"></i>'.($page - 1).' Previous</a>';
                            }

                            if ($page < $totalPages) {
                                echo '<a href="?page='.($page + 1).'" class="btn btn-primary btn-sm" id="next" style="padding: 10px; width:10%; margin-left:5px;"> '.($page + 1).' Next <i class="fa-solid fa-angle-right"></i></a>';
                            }

                            echo '
                            </div>
                            ';
                        }

                    } else {
                        echo "<tr><td colspan='10'><p class='container' style='margin-left:90px; margin-top:50px; font-size: 20px; font-weight:700;'>Unavailable. History is Empty.</p></td></tr>";
                    }


                    // Close the database connection
                    mysqli_close($conn);


                    ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>