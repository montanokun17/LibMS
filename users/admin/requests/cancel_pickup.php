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
$borrow_id = "";
$book_title = "";
$borrow_days = "";
$borrow_status = "";
$request_date = "";
$request_timestamp = "";
$pickup_date = "";


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
                $approved_borrow_status = $row['borrow_status'];
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

    $CancelStatus = "Cancelled Pickup";
    $CancelQuery = "UPDATE approved_borrow_requests SET borrow_status = ? WHERE borrow_id = ?";
    $CancelStmt = $conn->prepare($CancelQuery);
    $CancelStmt->bind_param('si', $CancelStatus, $borrow_id);

    if ($CancelStmt->execute()) {

        $BookStatus = "Available";
        $UpdateBookQuery = "UPDATE books SET book_borrow_status = ? WHERE book_id = ?";
        $UpdateBookStmt = $conn->prepare($UpdateBookQuery);
        $UpdateBookStmt->bind_param('si', $BookStatus, $book_id);
        $UpdateBookStmt->execute();

        $UpdateRequestQuery = "DELETE FROM borrow_request WHERE borrow_id = ?";
        $UpdateRequestStmt = $conn->prepare($UpdateBookQuery);
        $UpdateRequestStmt->bind_param('i', $borrow_id);
        $UpdateRequestStmt->execute();

        $logSql = "INSERT INTO book_log_history (borrow_id, borrower_user_id, borrower_username, book_id, book_title, borrow_days, borrow_status, request_date, action_performed, action_performed_by)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $logStatement = $conn->prepare($logSql);
        $logStatement->bind_param("iisisissss", $borrow_id, $borrower_user_id, $borrower_username, $book_id, $book_title, $borrow_days, $CancelStatus, $request_date, $CancelStatus, $username);
        $logStatement->execute();


    } else {
        echo "Error: " . $CancelQuery . "<br>" . mysqli_error($conn). "";
    }


    $notificationMessage = "Dear User, The Pickup Process for the book: " . $book_title . ", with the pickup date: " . $pickup_date . " ,that you've want to borrow was Cancelled by the " . $acctype . ". 
    Please Send another Borrow Request if you want to borrow the book again.";
    $readStatus = "UNREAD";

    $sqlStudent = "SELECT * FROM users WHERE id_no = $borrower_user_id";
    $resultStudent = mysqli_query($conn, $sqlStudent);

    if ($resultStudent) {

        while ($row = mysqli_fetch_assoc($resultStudent)) {
            $student_userId = $row['id_no'];

            $sqlNotification = "INSERT INTO notifications (sender_user_id, receiver_user_id, notification_message, read_status)
                                VALUES (?,?,?,?)";
            $notificationStmt = $conn->prepare($sqlNotification);
            $notificationStmt->bind_param('iiss', $idNo, $student_userId, $notificationMessage, $readStatus);
            $notificationStmt->execute();
        }

    } else {
        echo "Error: " . $sqlStudent . "<br>" . mysqli_error($conn). "";
    }

   
}

?>