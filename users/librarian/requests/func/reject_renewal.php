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
$borrow_id = "";
$book_title = "";
$borrow_days = "";
$borrow_status = "";
$renew_request_date = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['borrow_id'])) {
        $borrow_id = $_POST['borrow_id'];
    
        // Renew Request
        $RequestQuery = "SELECT * FROM renew_requests WHERE borrow_id = ?";
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
                $renew_stat = $row['renew_status'];
                $renew_request_date = $row['renew_request_date'];
                $renew_timestamp = $row['renew_timestamp'];
    
            } else {
                echo "Renew Request Not Found: " . $RequestStmt->error;
            }
    
            $RequestStmt->close(); // Close the statement to free up resources
    
            // Approved Borrow Request
            $BorrowQuery = "SELECT * FROM approved_borrow_requests WHERE borrow_id = ?";
            $BorrowStmt = $conn->prepare($BorrowQuery);
    
            if (is_numeric($borrow_id)) {
                $BorrowStmt->bind_param('i', $borrow_id);
                $BorrowStmt->execute();
                $BorrowResult = $BorrowStmt->get_result();
    
                if ($BorrowResult->num_rows === 1) {
                    $row = $BorrowResult->fetch_assoc();
    
                    $approved_borrower_user_id = $row['borrower_user_id'];
                    $approved_borrower_username = $row['borrower_username'];
                    $approved_book_id = $row['book_id'];
                    $approved_book_title = $row['book_title'];
                    $approved_borrow_days = $row['borrow_days'];
                    $approved_borrow_status = $row['borrow_status'];
                    $request_approval_date = $row['request_approval_date'];
                    $due_date = $row['due_date'];
                    $approved_pickup_date = $row['pickup_date'];
                    $approvedBy = $row['approved_by'];
    
                } else {
                    echo "Borrow Request Not Found: " . $BorrowStmt->error;
                }
    
                $BorrowStmt->close(); // Close the statement to free up resources
    
            } else {
                echo "Invalid Borrow ID for Approved Borrow Request: " . $BorrowStmt->error;
            }
        } else {
            echo "Invalid Borrow ID for Renew Request: " . $RequestStmt->error;
        }
    } else {
        echo "Borrow ID Not Set";
    }

    $Renewal_Status = "Rejected";
    $RenewSql = "UPDATE renew_requests SET renew_status = ? WHERE borrow_id = ?";
    $Book_Status = "Borrowed";
    $logAction = "Renewal Rejected";

    $RenewStmt = $conn->prepare($RenewSql);
    $RenewStmt->bind_param('si', $Renewal_Status, $borrow_id);

    if ($RenewStmt->execute()) {

        $BookQuery = "UPDATE books SET book_borrow_status = ? WHERE book_id = ?";
        $BookStmt = $conn->prepare($BookQuery);
        $BookStmt->bind_param('si', $Book_Status, $book_id);
        $BookStmt->execute();

        $logSql = "INSERT INTO book_log_history (borrow_id, borrower_user_id, borrower_username, book_id, book_title, borrow_days, borrow_status, request_date, action_performed, action_performed_by)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $logStatement = $conn->prepare($logSql);
        // Assuming all other variables are defined before this point
        $logStatement->bind_param("iisisissss", $borrow_id, $borrower_user_id, $borrower_username, $book_id, $book_title, $borrow_days, $approved_borrow_status, $renew_request_date, $logAction, $username);
        $logStatement->execute();

        echo "The Renewal Request was Rejected.";

        $notificationMessage = "Dear User, Your Renewal Request for the book: " . $book_title . " was rejected by " . $acctype . " " . $username . ". Contact the Admin or Librarian about the other information.";
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


    } else {
        echo "Error: " . $RenewSql . "<br>" . mysqli_error($conn) . "";
    }
   
}

?>