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


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $borrow_days = $_POST["borrowDays"];

    $borrower_user_id = "";
    $borrower_username = "";
    $book_id = "";
    $book_title = "";
    $borrow_status = "";
    $request_approval_date = "";
    $due_date = "";
    $pickup_date = "";
    
    if (isset($_POST['borrow_id'])) {
        $borrow_id = $_POST['borrow_id'];
    
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
                echo "Request Not Found";
                exit; // Stop execution if the request is not found
            }
        } else {
            echo "Invalid Borrow ID";
            exit; // Stop execution if the borrow_id is not numeric
        }
    } else {
        echo "Borrow ID Not Set";
        exit; // Stop execution if the borrow_id is not set
    }

    $BorrowStatus = "Renewal Pending";
    $RenewRequestDate = date("Y-m-d");
    $logAction = "Renew Request Sent";

    $RenewSql = "INSERT INTO renew_requests (borrow_id, borrower_user_id, borrower_username, book_id, book_title, borrow_days, renew_status, renew_request_date)
                VALUES ( ?, ?, ?, ?, ?, ?, ?, ?)";
    $RenewStmt = $conn->prepare($RenewSql);
    $RenewStmt->bind_param('ississss', $borrow_id ,$borrower_user_id, $borrower_username, $book_id, $book_title, $borrow_days, $BorrowStatus, $RenewRequestDate);

    if ($RenewStmt->execute()) {

        $updateBookStatusSql = "UPDATE books SET book_borrow_status = 'Request Pending' WHERE book_id = ?";
        $updateStmt = $conn->prepare($updateBookStatusSql);
        $updateStmt->bind_param('i', $book_id);
        $updateStmt->execute();

        // Insert into book_log_history table
        $logSql = "INSERT INTO book_log_history (borrow_id, borrower_user_id, borrower_username, book_id, book_title, borrow_days, borrow_status, request_date, action_performed, action_performed_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $logStmt = $conn->prepare($logSql);
        $logStmt->bind_param('ississssss', $borrow_id ,$borrower_user_id, $borrower_username, $book_id, $book_title, $borrow_days, $BorrowStatus, $RenewRequestDate, $logAction, $username);
        $logStmt->execute();

        echo 'Renewal request was sent successfully. Please wait for the Admin/Librarian to grant your renewal. You will be notified soon.';
        
    } else {
        echo "Error: " . $RenewStmt->error;
        exit; // Stop execution if the renewal request fails
    }

    $notificationMessage = "A new Renewal for a borrow request from user: $borrower_username for the book: " . $book_title . ", for $borrow_days days, was sent.";
    $readStatus = "UNREAD";

    // Query users table to find admins and librarians
    $sqlAdminsLibrarians = "SELECT id_no FROM users WHERE acctype IN ('admin', 'librarian')";
    $resultAdminsLibrarians = $conn->query($sqlAdminsLibrarians);

    if ($resultAdminsLibrarians) {
        while ($row = $resultAdminsLibrarians->fetch_assoc()) {
            $adminUserId = $row['id_no'];

            $sqlNotification = "INSERT INTO notifications (sender_user_id, receiver_user_id, notification_message, read_status) 
                    VALUES (?, ?, ?, ?)";
            $notificationStmt = $conn->prepare($sqlNotification);
            $notificationStmt->bind_param('ssss', $borrower_user_id, $adminUserId, $notificationMessage, $readStatus);
            $notificationStmt->execute();
            if ($notificationStmt->error) {
                echo "Error inserting notification: " . $notificationStmt->error;
                exit; // Stop execution if the notification insertion fails
            }
        }
    } else {
        echo "Error: " . $conn->error;
        exit; // Stop execution if the query to fetch admins and librarians fails
    }

    // Close the database connection
    $conn->close();
}

?>
