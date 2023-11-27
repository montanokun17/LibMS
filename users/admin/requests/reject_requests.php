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
$request_date = "";
$request_timestamp = "";



if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['borrow_id'])) {
        $borrow_id = $_POST['borrow_id'];
    
        $RequestQuery = "SELECT * FROM approved_borrow_requests WHERE borrow_id = ?";
        $RequestStmt = $conn->prepare($RequestQuery);
    
        if (is_numeric($borrow_id)) {
            $RequestStmt->bind_param('i', $borrow_id);
            $RequestStmt->execute();
            $RequestResult = $RequestStmt->get_result();
    
            if ($RequestResult->num_rows === 1) {
                $row = $RequestResult->fetch_assoc();
    
                $borrower_user_id = $row['borrower_user_id'];
                $borrower_username = $row['borrower_username'];
                $book_id = $row['book_id'];
                $book_title = $row['book_title'];
                $borrow_days = $row['borrow_days'];
                $borrow_status = $row['borrow_status'];
                $request_approval_date = $row['request_approval_date'];
                $pickup_date = $row['pickup_date'];
                $approvedBy = $row['approved_by'];
                
            } else {
                echo "Request Not Found" . $RequestStmt->error;
            }
        } else {
            echo "Invalid Borrow ID" . $RequestStmt->error;
        }
    } else {
        echo "Borrow ID Not Set" . $RequestStmt->error;
    }
    

    $borrow_status = "Rejected";
    $RejectQuery = "UPDATE borrow_requests SET borrow_status = ? WHERE borrow_id = ?";
    $RejectQuery = $conn->prepare($RejectQuery);

    // Assuming $borrow_id is defined before this point
    $RejectQuery->bind_param("si", $borrow_status, $borrow_id);

    if ($RejectQuery->execute()) {

        $updateBookStatus = "Available";
        $updateBookStatusSql = "UPDATE books SET book_borrow_status = ? WHERE book_id = ?";
        $updateBookStatusSql = $conn->prepare($updateBookStatusSql);
        $updateBookStatusSql->bind_param("si", $updateBookStatus, $book_id);
        $updateBookStatusSql->execute();

        $logAction = "Request Rejected";
        $logSql = "INSERT INTO book_log_history (borrow_id, borrower_user_id, borrower_username, book_id, book_title, borrow_days, borrow_status, request_date, action_performed, action_performed_by)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $logStatement = $conn->prepare($logSql);

        // Assuming all other variables are defined before this point
        $logStatement->bind_param("iisississs", $borrow_id, $borrower_user_id, $borrower_username, $book_id, $book_title, $borrow_days, $borrow_status, $request_date, $logAction, $username);
        $logStatement->execute();

        echo 'The Request was Rejected.';
    } else {
        echo 'Error: ' . $RejectQuery->error;
    }


    $notificationMessage = "Dear User, Your Borrow Request for the book: " . $book_title . " was rejected by " . $acctype . " " . $username . ". Contact the Admin or Librarian about the other information.";
    $readStatus = "UNREAD";

    $sqlStudent = "SELECT * FROM users WHERE id_no = ?";
    $stmtStudent = $conn->prepare($sqlStudent);
    $stmtStudent->bind_param("s", $borrower_user_id);
    $stmtStudent->execute();
    $resultStudent = $stmtStudent->get_result();

    if ($resultStudent) {
        while ($row = mysqli_fetch_assoc($resultStudent)) {
            $student_userId = $row['id_no'];

            $sqlNotification = "INSERT INTO notifications (sender_user_id, receiver_user_id, notification_message, read_status)
                                VALUES (?, ?, ?, ?)";
            $stmtNotification = $conn->prepare($sqlNotification);
            $stmtNotification->bind_param("ssss", $idNo, $student_userId, $notificationMessage, $readStatus);
            $stmtNotification->execute();
        }
    } else {
        echo "Error: " . $sqlStudent . "<br>" . mysqli_error($conn) . "";
    }

    $stmtStudent->close();
    $stmtNotification->close();


}


?>