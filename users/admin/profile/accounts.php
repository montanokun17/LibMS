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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['disable'])) {
    $userId = $_POST['user_id'];
    
    // Update the user's status to "Disabled"
    $stmt = $conn->prepare("UPDATE users SET status = 'Disabled' WHERE id_no = ?");
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        header ("Location: /LibMS/users/admin/profile/accounts.php");
    } else {
        // Error occurred while disabling the account
        echo "<script>alert('Error: Unable to enable account.');</script>";
    }

    // Close the prepared statement
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enable'])) {
    $userId = $_POST['user_id'];
    
    // Update the user's status to "Enable/Active"
    $stmt = $conn->prepare("UPDATE users SET status = 'Active' WHERE id_no = ?");
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        // Disable action successful
        header ("Location: /LibMS/users/admin/profile/accounts.php");
    } else {
        // Error occurred while disabling the account
        echo "<script>alert('Error: Unable to enable account.');</script>";
    }

    // Close the prepared statement
    $stmt->close();
}

// Check if the delete button is clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $userId = $_POST['user_id'];

    // Delete the user's account
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        // Account deletion successful
        echo "<script>alert('Account deleted successfully.');</script>";
    } else {
        // Error occurred while deleting the account
        echo "<script>alert('Error: Unable to delete account.');</script>";
    }

    // Close the prepared statement
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo '<title>'. $firstname .' '. $lastname .' /Accounts - MyLibro </title>'; ?>
    <!--Link for Tab ICON-->
    <link rel="icon" ="image/x-icon" href="/LibMS/resources/images/logov1.png">
    <!--Link for Bootstrap-->
    <link rel="stylesheet" type="text/css" href="/LibMS/resources/bootstrap/css/bootstrap.min.css"/>
    <script type="text/javascript" src="/LibMS/resources/bootstrap/js/bootstrap.min.js"></script>
    <!--Link for CSS File-->
    <link rel="stylesheet" type="text/css" href="/LibMS/users/admin/profile/css/accounts.css">
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
                    <a href="/LibMS/users/admin/notification/notification.php">
                        <i class="fa fa-bell fa-sm"></i>
                        <span class="sidebar-name">
                            Notifications
                        </span>
                    </a>
                </li>

                <li>
                    <a href="/LibMS/users/admin/requests/issue_return_requests.php">
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
                            Issued Request/Returned Books Log
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

<div class="table-box">
    <div class="container col-12 col-md-10">
        <div class="container">
            <div class="row">
                <div class="books-box">
                    <div class="container-fluid">

                    <div class="search-bar">
                            <form method ="GET">
                                <input type="text" class="search" placeholder ="Search for ID Number, Name, Username, Email..">
                                <button type="submit" name="search" class="btn btn-primary bg-dark btn-sm"><i class="fa-solid fa-search fa-sm"></i> Search</button>
                            </form>

                            <div class="dropdown-1">
                                    <select name="filter-acctype" id="filter-acctype">
                                        <option selected disabled>*Select Account Types*</option>
                                        <option value="admin">Admin</option>
                                        <option value="librarian">Librarian</option>
                                        <option value="staff">Staff</option>
                                        <option value="student">Student</option>
                                    </select>

                                    <select name="account-status" id="account-status">
                                        <option selected disabled>*Select Account Status*</option>
                                        <option value="admin">Active</option>
                                        <option value="librarian">Disabled</option>
                                    </select>
                                    
                            </div>

                        </div>

                    <?php
                
                     // Default query to fetch all books
                     $query = "SELECT * FROM users ORDER BY id_no DESC";
                     
                     function getUsersByPagination($conn, $query, $offset, $limit) {
                        $query .= " LIMIT $limit OFFSET $offset"; // Append the LIMIT and OFFSET to the query for pagination
                        $result = mysqli_query($conn, $query);
                    
                        return $result;
                    }

                    $totalUsersQuery = "SELECT COUNT(*) as total FROM users";
                    $totalUsersResult = mysqli_query($conn, $totalUsersQuery);
                    $totalUsers = mysqli_fetch_assoc($totalUsersResult)['total'];

                    // Number of users to display per page
                    $limit = 5;

                    // Get the current page number from the query parameter
                    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;

                    // Calculate the offset for the current page
                    $offset = ($page - 1) * $limit;

                    // Get the users for the current page
                    $result = getUsersByPagination($conn, $query, $offset, $limit);

                    $result = mysqli_query($conn, $query);

                    // Check if the query executed successfully
                    if ($result && mysqli_num_rows($result) > 0) {
                        echo '<div class="container">';
                        echo '<table>';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th>ID Number</th>';
                        echo '<th>Name</th>';
                        echo '<th>Username</th>';
                        echo '<th>Barangay/City</th>';
                        echo '<th>Account Type</th>';
                        echo '<th>School Level</th>';
                        echo '<th>Contact Number</th>';
                        echo '<th>Status</th>';
                        echo '<th>Action</th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';

                        while ($user = mysqli_fetch_assoc($result)) {
                            echo '<tr>';
                            echo '<td>' . $user['id_no'] . '</td>';
                            echo '<td>' . $user['firstname'] ." ". $user['lastname'] . '</td>';
                            echo '<td>' . $user['username'] . '</td>';
                            echo '<td>' . $user['brgy'] . '</td>';
                            echo '<td>' . $user['acctype'] . '</td>';
                            echo '<td>' . $user['schlvl'] . '</td>';
                            echo '<td>' . $user['con_num'] . '</td>';
                            if ($user['status'] == 'Active') {
                                echo '<td style="color: green; text-transform:uppercase;"><b><i>' . $user['status'] . '</i></b></td>';
                            }else {
                                echo '<td style="color: grey; text-transform:uppercase;"><b><i>' . $user['status'] . '</i></b></td>';
                            }
                            
                            echo "<td>";

                            if ($user['acctype'] === 'Admin' || $user['acctype'] === 'Librarian') {
                                if ($user['status'] == 'Disabled') {
                                    echo '
                                    <form method="POST" action="">
                                        <input type="hidden" name="user_id" value="' . $user['id_no'] . '">
                                        <button type="submit" class="btn btn-primary btn-sm" name="enable" style="font-size:12px; padding: 5px; color: white; margin-bottom: 3px;">
                                            <i class="fa-solid fa-shield"></i> Enable
                                        </button>
                                    </form>';
                                } else {
                                    echo '
                                    <form method="POST" action="">
                                        <input type="hidden" name="user_id" value="' . $user['id_no'] . '">
                                        <button type="submit" class="btn btn-secondary btn-sm" name="disable" style="font-size:12px; padding: 5px; background-color: grey; color: white; margin-bottom: 3px;">
                                            <i class="fa-solid fa-shield"></i> Disable
                                        </button>
                                    </form>';
                                }
                            } else {
                                if ($user['status'] == 'Disabled') {
                                    echo '
                                    <form method="POST" action="">
                                        <input type="hidden" name="user_id" value="' . $user['id_no'] . '">
                                        <button type="submit" class="btn btn-primary btn-sm" name="enable" style="font-size:12px; padding: 5px; color: white; margin-bottom: 3px;">
                                            <i class="fa-solid fa-shield"></i> Enable
                                        </button>
                                    </form>';
                            
                                    echo '
                                    <form method="POST" action="">
                                        <input type="hidden" name="user_id" value="' . $user['id_no'] . '">
                                        <button type="submit" class="btn btn-danger btn-sm" name="delete" style="font-size:12px; width:70%; padding: 5px; background-color: red; margin-bottom: 3px;">
                                            <i class="fa-solid fa-trash"></i> Delete
                                        </button>
                                    </form>';
                                } else {
                                    // Account is not disabled, display disable button
                                    echo '
                                    <form method="POST" action="">
                                        <input type="hidden" name="user_id" value="' . $user['id_no'] . '">
                                        <button type="submit" class="btn btn-secondary btn-sm" name="disable" style="font-size:12px; padding: 5px; background-color: grey; color: white; margin-bottom: 3px;">
                                            <i class="fa-solid fa-shield"></i> Disable
                                        </button>
                                    </form>';
                            
                                    echo '
                                    <form method="POST" action="">
                                        <input type="hidden" name="user_id" value="' . $user['id_no'] . '">
                                        <button type="submit" class="btn btn-danger btn-sm" name="delete" style="font-size:12px; width:70%; padding: 5px; background-color: red; margin-bottom: 3px;">
                                            <i class="fa-solid fa-trash"></i> Delete
                                        </button>
                                    </form>';
                                }
                            }
                            

                        }

                        echo "</td>";
                        echo "</tr>";

                    echo '</tbody>';
                    echo '</table>';

                    // Calculate the total number of pages
                    $totalPages = ceil($totalUsers / $limit);
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
                            echo '<a href="?page='.($page + 1).'" class="btn btn-primary btn-sm" id="next" style="padding: 10px; width:10%;"> '.($page + 1).' Next <i class="fa-solid fa-angle-right"></i></a>';
                        }
                
                        echo '
                        </div>
                        ';
                    }

                } else {
                    echo "<tr><td colspan='10'>No books found.</td></tr>";
                }


                // Close the database connection
                mysqli_close($conn);



                    ?>

                        
                        <script>
                            // JavaScript function for handling pagination buttons
                            document.addEventListener("DOMContentLoaded", function () {
                                const previousBtn = document.getElementById("previous");
                                const nextBtn = document.getElementById("next");

                                if (previousBtn) {
                                    previousBtn.addEventListener("click", function () {
                                        // Go to the previous page by decrementing the current page number
                                        let currentPage = parseInt("<?php echo $page; ?>");
                                        if (currentPage > 1) {
                                            currentPage--;
                                            window.location.href = "?page=" + currentPage;
                                        }
                                    });
                                }

                                if (nextBtn) {
                                    nextBtn.addEventListener("click", function () {
                                        // Go to the next page by incrementing the current page number
                                        let currentPage = parseInt("<?php echo $page; ?>");
                                        let totalPages = parseInt("<?php echo $totalPages; ?>");
                                        if (currentPage < totalPages) {
                                            currentPage++;
                                            window.location.href = "?page=" + currentPage;
                                        }
                                    });
                                }
                            });
                        </script>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
