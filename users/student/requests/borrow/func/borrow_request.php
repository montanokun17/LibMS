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
    //$bookId = $_POST['bookId'];

        // Check if the book_id is set in the POST data
        if (isset( $_POST['bookId'])) {
            $bookId = $_POST['bookId'];
    
            // Use prepared statements to prevent SQL injection
            $bookQuery = "SELECT * FROM books WHERE book_id = ?";
            $bookStmt = $conn->prepare($bookQuery);
    
            // Validate bookId before binding
            if (is_numeric($bookId)) {
                $bookStmt->bind_param('i', $bookId);
                $bookStmt->execute();
                $bookResult = $bookStmt->get_result();
    
                if ($bookResult->num_rows === 1) {
                    $row = $bookResult->fetch_assoc();
    
                    $book_title = $row['book_title'];
                    $section = $row['section'];
                    $volume = $row['volume'];
                    $edition = $row['edition'];
                    $author = $row['author'];
                    $year = $row['year'];
                    $publisher = $row['publisher'];
                    $isbn = $row['isbn'];
                    $status = $row['status'];
    
                    // Now you have the book information, you can display it or use it as needed
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



    $borrowerUserId = $idNo;
    $borrowerUsername = $username;
    $bookTitle = $book_title;
    $borrowStatus = "Pending";
    $requestDate = date("Y-m-d");

    // Insert into borrow_requests table
    $sql = "INSERT INTO borrow_requests (borrower_user_id, borrower_username, book_id, book_title, borrow_days, borrow_status, request_date) 
            VALUES ('$borrowerUserId', '$borrowerUsername', '$bookId', '$bookTitle', '$borrowDays', '$borrowStatus', '$requestDate')";

    if (mysqli_query($conn, $sql)) {
        // Update book_borrow_status to 'Request Pending'
        $updateBookStatusSql = "UPDATE books SET book_borrow_status = 'Request Pending' WHERE book_id = '$bookId'";
        mysqli_query($conn, $updateBookStatusSql);

         // Insert into book_log_history table
        $logAction = "Borrow Request Sent";
        $logSql = "INSERT INTO book_log_history (borrower_user_id, borrower_username, book_id, book_title, borrow_days, borrow_status, request_date, action_performed, action_performed_by) 
                VALUES ('$borrowerUserId', '$borrowerUsername', '$bookId', '$bookTitle', '$borrowDays', '$borrowStatus', '$requestDate', '$logAction', '$borrowerUsername')";

        mysqli_query($conn, $logSql);

        echo 'Borrow request sent successfully. Please Wait for the Admin/Librarian to Accept Your Request.';
    } else {
        echo "Error: ' . $sql . '<br>' . 'mysqli_error($conn)";
    }

    $notificationMessage = "A new borrow request from user: $borrowerUsername for the book: " . $bookTitle . ", for $borrowDays days, was sent.";
    $readStatus = "UNREAD";

    // Query users table to find admins and librarians
    $sqlAdminsLibrarians = "SELECT id_no FROM users WHERE acctype IN ('admin', 'librarian')";
    $resultAdminsLibrarians = mysqli_query($conn, $sqlAdminsLibrarians);

    if ($resultAdminsLibrarians) {
        while ($row = mysqli_fetch_assoc($resultAdminsLibrarians)) {
            $adminUserId = $row['id_no'];

            // Insert notification for each admin/librarian
            $sqlNotification = "INSERT INTO notifications (sender_user_id, receiver_user_id, notification_message, read_status) 
                                VALUES ('$borrowerUserId', '$adminUserId', '$notificationMessage', '$readStatus')";

            mysqli_query($conn, $sqlNotification);
        }
    } else {
        echo "Error: " . $sqlAdminsLibrarians . "<br>" . mysqli_error($conn);
    }
}

// Close the database connection if needed
mysqli_close($conn);

?>
