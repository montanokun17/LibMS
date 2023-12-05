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
    <link rel="stylesheet" type="text/css" href="/LibMS/users/admin/qrpage/css/qr-landing-page.css">
    <!--Link for NAVBAR and SIDEBAR styling-->
    <link rel="stylesheet" type="text/css" href="/LibMS/users/admin/css/navbar-sidebar.css">
    <!--Link for Font Awesome Icons-->
    <link rel="stylesheet" href="/LibMS/resources/icons/fontawesome-free-6.4.0-web/css/all.css">
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>

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
            <a class="nav-link" aria-current="page" href="/LibMS/users/admin/index.php"><i class="fa-solid fa-house fa-xs"></i> Home</a>
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



<div class="main-box">
    <div class="container">
        <div class="row">
              <table style="margin-top:30px;">
                <tr>
                  <td class="box-1">
                    <!--BOX 1-->

                    <video id="preview" style="width:400px; height:400px;"></video>


                  </td>

                  <td class="box-2 container">

                    <table style="width:30pc; width:; margin-top:-170px;">

                    <?php

                      $QRQuery = "SELECT * FROM qr_attendance ORDER BY qr_log_id DESC";


                      function getReturnByPagination($conn, $query, $offset, $limit) {
                        $query .= " LIMIT $limit OFFSET $offset"; // Append the LIMIT and OFFSET to the query for pagination
                        $result = mysqli_query($conn, $query);

                        return $result;
                      }

                      $totalQRQuery = "SELECT COUNT(*) as total FROM qr_attendance";
                      $totalQRResult = mysqli_query($conn, $totalQRQuery);
                      $totalReturn = mysqli_fetch_assoc($totalQRResult)['total'];


                      // Number of books to display per page
                      $limit = 7;

                      // Get the current page number from the query parameter
                      $page = isset($_GET['page']) ? intval($_GET['page']) : 1;

                      // Calculate the offset for the current page
                      $offset = ($page - 1) * $limit;

                      // Get the books for the current page
                      $result = getReturnByPagination($conn, $query, $offset, $limit);

                        // Check if the query executed successfully
                        if ($result && mysqli_num_rows($result) > 0) {
                            echo '<div class="container" id="result">';
                            echo '<table>';
                            echo '<thead>';
                            echo '<tr>';
                            echo '<th>User ID</th>';
                            echo '<th>Username</th>';
                            echo '<th>Account Type</th>';
                            echo '<th>Time In</th>';
                            echo '<th>Time Out</th>';
                            echo '</tr>';
                            echo '</thead>';
                            echo '<tbody>';

                            while ($return = mysqli_fetch_assoc($result)) {
                                echo '<tr>';
                                echo '<td>' . $return['user_id'] . '</td>';
                                echo '<td>' . $return['username'] . '</td>';
                                echo '<td>' . $return['acctype'] . '</td>';
                                echo '<td>' . $return['attendance_time_in'] . '</td>';
                                echo '<td>' . $return['attendance_time_out'] . '</td>';

                                echo '</tr>';
                            }

                            echo '</tbody>';
                            echo '</table>';


                            // Calculate the total number of pages
                            $totalPages = ceil($totalReturn / $limit);
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
                            echo '<div class="container">';
                            echo '<table style="width:;">';
                            echo '<thead>';
                            echo '<tr>';
                            echo '<th>User ID</th>';
                            echo '<th>Username</th>';
                            echo '<th>Account Type</th>';
                            echo '<th>Time In</th>';
                            echo '<th>Time Out</th>';
                            echo '</tr>';
                            echo '</thead>';

                            echo "<tr><td colspan='10'><p class='container' style='margin-left:90px; margin-top:50px; font-size: 20px; font-weight:700;'>No Records.</p></td></tr>";
                        }


                        // Close the database connection
                        mysqli_close($conn);

                      ?>
                    </table>

                  </td>
                </tr>
              </table>
        </div>
    </div>
</div>

<script>
  function getUserIdFromDatabase($conn, $idNo) {
    $idNo = $conn->real_escape_string($idNo); // Escape input to prevent SQL injection

    $sql = "SELECT id_no FROM users WHERE id_no = '$idNo'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['id_no'];
    } else {
        // Handle the case when the user with the provided ID is not found
        return null;
    }
}

</script>

<script>

// Create a new Instascan scanner instance
let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });

    // Set up a callback for when a QR code is scanned successfully
    scanner.addListener('scan', function (content) {
    document.getElementById('text').innerText = content;

    // Send the scanned data to the server using AJAX
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/LibMS/users/admin/qrpage/qr-scan.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            alert(xhr.responseText);
        } else {
            alert('Error: ' + xhr.statusText);
        }
    };
    xhr.send('content=' + encodeURIComponent(content));

});

// Handle errors
scanner.addListener('error', function (error) {
    console.log(error);
});

// Start the scanner
Instascan.Camera.getCameras().then(function (cameras) {
    if (cameras.length > 0) {
        scanner.start(cameras[0]);
    } else {
        console.error('No cameras found.');
    }
}).catch(function (e) {
    console.error(e);
});

</script>

</body>
</html>