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

$borrower_user_id = "";
$borrower_username = "";
$book_id = "";
$book_title = "";
$borrow_days = "";
$borrow_status = "";
$request_date = "";
$request_timestamp = "";

        if (isset($_POST['borrow_id'])) {
        $borrow_id = $_POST['borrow_id'];

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
                echo "<script>alert('Request Not Found');</script>";
            }
        } else {
            echo "<script>alert('Invalid Borrow ID');</script>";
        }
    } else {
        echo "<script>alert('Borrow ID Not Set');</script>";
    }



if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $CancelQuery = "DELETE FROM borrow_requests WHERE borrow_id = $borrow_id";

    if (mysqli_query($conn, $CancelQuery)) {

        $updateBookStatusSql = "UPDATE books SET book_borrow_status = 'Available' WHERE book_id = '$book_id'";
        mysqli_query($conn, $updateBookStatusSql);

        $notificationSQL = "DELETE FROM notification WHERE notif_id = $borrow_id OR notif_timestamp = $request_timestamp";
        mysqli_query($conn, $notificationSQL);

        $logSQL = "DELETE FROM book_log_history WHERE borrow_id = $borrow_id";
        mysqli_query($conn, $logSQL);

        echo 'Your Request was Cancelled.';
    } else {
        echo 'Error: ' . $CancelQuery . '<br>' . mysqli_error($conn);
    }

}


?>