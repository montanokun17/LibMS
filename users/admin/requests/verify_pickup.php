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
$book_id = "";
$book_title = "";
$borrow_days = "";
$borrow_status = "";
$request_approval_date = "";
$pickup_date = "";
$approvedBy = "";


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

    // Calculate due date
    $due_date = date('Y-m-d', strtotime($pickup_date . ' + ' . $borrow_days . ' days'));
    $borrow_status = "Borrowed";
    $RequestStatus = "Approved";

    // Fix syntax error in the UPDATE query
    $VerifyPickupQuery = "UPDATE approved_borrow_requests SET borrow_status = ?, due_date = ? WHERE borrow_id = ?";
    $VerifyPickupStmt = $conn->prepare($VerifyPickupQuery);
    $VerifyPickupStmt->bind_param("ssi", $borrow_status, $due_date, $borrow_id);

    if ($VerifyPickupStmt->execute()) {

        echo 'Book Loan/Borrow Pickup Verified.';
    } else {
        echo 'Error: ' . $VerifyPickupQuery . '<br>' . $VerifyPickupStmt->error;
    }

    $VerifyBorrowQuery = "UPDATE borrow_requests SET borrow_status = ? WHERE borrow_id = ?";
    $VerifyBorrowStmt = $conn->prepare($VerifyBorrowQuery);
    $VerifyBorrowStmt->bind_param("si", $RequestStatus, $borrow_id);

    if ($VerifyBorrowStmt->execute()) {

    } else {
        echo 'Error: ' . $VerifyBorrowQuery . '<br>' . $VerifyBorrowStmt->error;
    }

    $VerifyBookQuery = "UPDATE books SET book_borrow_status = ? WHERE book_id = ?";
    $VerifyBookStmt = $conn->prepare($VerifyBookQuery);
    $VerifyBookStmt->bind_param("si", $borrow_status, $book_id);

    if ($VerifyBookStmt->execute()) {

    } else {
        echo 'Error: ' . $VerifyBookQuery . '<br>' . $VerifyBookStmt->error;
    }


    $notificationMessage = "You Have Successfully Picked up and Borrowed A Book from the Library. Remember to Return the Book Before it's Due Date " . $due_date . " to avoid penalties from the Library. Have Fun Reading!";
    $readStatus = "UNREAD";

    $sqlStudent = "SELECT * FROM users WHERE id_no = $borrower_user_id";
    $resultStudent = mysqli_query($conn, $sqlStudent);

    if ($resultStudent) {
        while ($row = mysqli_fetch_assoc($resultStudent)) {
            $student_userId = $row['id_no'];

            $sqlNotification = "INSERT INTO notifications (sender_user_id, receiver_user_id, notification_message, read_status)
                                VALUES ('$idNo', '$student_userId', '$notificationMessage', '$readStatus')";
            mysqli_query($conn, $sqlNotification);
        }
    } else {
        echo "Error: " . $sqlStudent . "<br>" . mysqli_error($conn). "";
    }
    
}

?>