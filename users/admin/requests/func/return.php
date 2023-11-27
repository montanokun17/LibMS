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
$due_date = "";
$pickup_date = "";
$bookStatus = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['borrow_id'])) {
        $borrow_id = $_POST['borrow_id'];

        $borrowQuery = "SELECT * FROM approved_borrow_requests WHERE borrow_id = ?";
        $borrowStmt = $conn->prepare($borrowQuery);

        if (is_numeric($borrow_id)) {
            $borrowStmt->bind_param('i', $borrow_id);
            $borrowStmt->execute();
            $borrowResult = $borrowStmt->get_result();

            if ($borrowResult->num_rows === 1) {
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
                echo "<script>alert('Request Not Found');</script>";
            }
        } else {
            echo "<script>alert('Invalid Borrow ID');</script>";
        }
    } else {
        echo "<script>alert('Borrow ID Not Set');</script>";
    }

    $bookStatus = $_POST['bookstatus'];
    $book_borrow_status = "Available";
    $return_date = date('Y-m-d');

    $BookUpdateQuery = "UPDATE books SET book_borrow_status = ?, status = ? WHERE book_id = ?";
    $BookUpdateStmt = $conn->prepare($BookUpdateQuery);
    $BookUpdateStmt->bind_param('ssi', $book_borrow_status, $bookStatus, $book_id);

    if ($BookUpdateStmt->execute()) {

        $UpdateBookBorrowStat = "Returned";
        $UpdateRequestQuery = "UPDATE approved_borrow_requests SET borrow_status = ? WHERE borrow_id = ?";
        $UpdateRequestStmt = $conn->prepare($UpdateRequestQuery);
        $UpdateRequestStmt->bind_param('si', $UpdateBookBorrowStat, $borrow_id);
        $UpdateRequestStmt->execute();

        $LogAction = "Returned";
        $LogReturnQuery = "INSERT INTO book_log_history (borrow_id, borrower_user_id, borrower_username, book_id, book_title, borrow_days, borrow_status, action_performed, action_performed_by)
                            VALUES (?,?,?,?,?,?,?,?,?)";
        $LogReturnStmt = $conn->prepare($LogReturnQuery);
        $LogReturnStmt->bind_param('iisisisss', $borrow_id, $borrower_user_id, $borrower_username, $book_id, $book_title, $borrow_days, $LogAction, $LogAction, $username);
        $LogReturnStmt->execute();

        $BookReturnQuery = "INSERT INTO returned_books (borrow_id, borrower_user_id, borrower_username, book_id, book_title, borrow_days, book_status, return_date, verified_by) 
                            VALUES(?,?,?,?,?,?,?,?,?)";
        $BookReturnStmt = $conn->prepare($BookReturnQuery);
        $BookReturnStmt->bind_param('iisisisss', $borrow_id, $borrower_user_id, $borrower_username, $book_id, $book_title, $borrow_days, $bookStatus, $return_date, $username);
        $BookReturnStmt->execute();

        echo 'The Book was Successfully Returned!';

    } else {
        echo '<p class="error">Error: The request could not be processed.</p>';
    }
}

?>
