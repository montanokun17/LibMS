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

// Fetch book information
$book_title = "";
$section = "";
$volume = "";
$edition = "";
$author = "";
$year = "";
$publisher = "";
$isbn = "";
$status = "";



/*// Check if the book_id is set in the query parameters
if (isset($_GET['book_id'], $_GET['title'], $_GET['section'], $_GET['volume'], $_GET['edition'], $_GET['author'], $_GET['year'], $_GET['publisher'], $_GET['isbn'], $_GET['status'])) {
    // Retrieve the parameters
    $bookId = $_GET['book_id'];
    $bookTitle = $_GET['title'];
    $section = $_GET['section'];
    $volume = $_GET['volume'];
    $edition = $_GET['edition'];
    $author = $_GET['author'];
    $year = $_GET['year'];
    $publisher = $_GET['publisher'];
    $isbn = $_GET['isbn'];
    $status = $_GET['status'];
}*/

// Your code to display or use the book information can go here



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $borrowDays = $_POST["borrowDays"];

    if (isset($_POST['bookId'])) {
        $bookId = $_POST['bookId'];

        if (is_numeric($bookId)) {
            $bookQuery = "SELECT * FROM books WHERE book_id = ?";
            $bookStmt = $conn->prepare($bookQuery);

            $bookStmt->bind_param('i', $bookId);
            $bookStmt->execute();
            $bookResult = $bookStmt->get_result();

            if ($bookResult->num_rows === 1) {
                $row = $bookResult->fetch_assoc();

                $book_title = $row['book_title'];
                // ... (other book details)

                $borrowerUserId = $idNo;
                $borrowerUsername = $username;
                $bookTitle = $book_title;
                $borrowStatus = "Pending";
                $requestDate = date("Y-m-d");

                // Use prepared statement for insert query
                $sql = "INSERT INTO borrow_requests (borrower_user_id, borrower_username, book_id, book_title, borrow_days, borrow_status, request_date) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('isissss', $borrowerUserId, $borrowerUsername, $bookId, $bookTitle, $borrowDays, $borrowStatus, $requestDate);

                if ($stmt->execute()) {
                    // Update book_borrow_status to 'Request Pending'
                    $updateBookStatusSql = "UPDATE books SET book_borrow_status = 'Request Pending' WHERE book_id = ?";
                    $updateStmt = $conn->prepare($updateBookStatusSql);
                    $updateStmt->bind_param('i', $bookId);
                    $updateStmt->execute();

                    // Insert into book_log_history table
                    $logAction = "Borrow Request Sent";
                    $logSql = "INSERT INTO book_log_history (borrower_user_id, borrower_username, book_id, book_title, borrow_days, borrow_status, request_date, action_performed, action_performed_by) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $logStmt = $conn->prepare($logSql);
                    $logStmt->bind_param('isissssss', $borrowerUserId, $borrowerUsername, $bookId, $bookTitle, $borrowDays, $borrowStatus, $requestDate, $logAction, $borrowerUsername);
                    $logStmt->execute();

                    echo 'Borrow request sent successfully. Please Wait for the Admin/Librarian to Accept Your Request.';

                    $notificationMessage = "A new borrow request from user: $username for the book: " . $book_title . ", for $borrowDays days, was sent.";
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


                } else {
                    echo "Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                // Handle the case when the book is not found
                echo "<script>alert('Book Not Found');</script>";
            }
        } else {
            // Handle the case when the book_id is not a valid number
            echo "<script>alert('Invalid Book ID');</script>";
        }
    } else {
        // Handle the case when book_id is not set in the POST data
        echo "<script>alert('Book ID Not Set');</script>";
    }
}

// Close the database connection if needed
mysqli_close($conn);

?>
