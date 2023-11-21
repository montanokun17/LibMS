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

?>

            
<?php


/*//$query = "SELECT * FROM books WHERE 'deleted' = 0 AND status = 'GOOD' ORDER BY 'book_title' ASC";

// Add the following code at the beginning to handle AJAX requests
if (isset($_POST['section']) && isset($_POST['status']) && isset($_POST['dewey']) && isset($_POST['book_borrow_status'])) {
    $section = $_POST['section'];
    $status = $_POST['status'];
    $dewey = $_POST['dewey'];
    $bookBorrowStatus = $_POST['book_borrow_status'];

    // Use prepared statements to prevent SQL injection
    $query = "SELECT * FROM books WHERE `deleted` = 0 AND `status` = ?";

    // Prepare the statement
    $stmt = $conn->prepare($query);

    // Check if the statement is prepared successfully
    if ($stmt === false) {
        die('Error preparing statement');
    }

    // Bind parameters
    $stmt->bind_param('s', $status);

    // Append additional conditions based on the selected filters
    if ($section !== "*Select Section*") {
        $query .= " AND `section` = ?";
        $stmt->bind_param('s', $section);
    }

    if ($dewey !== "*Select Dewey Classification*") {
        $query .= " AND `dewey` = ?";
        $stmt->bind_param('s', $dewey);
    }

    if ($bookBorrowStatus !== "*Select Availability*") {
        $query .= " AND `book_borrow_status` = ?";
        $stmt->bind_param('s', $bookBorrowStatus);
    }

    $query .= " ORDER BY `book_title` ASC";

    // Execute the statement
    $stmt->execute();

    // Get the results
    $result = $stmt->get_result();

    // Fetch the results as needed
    $results = $result->fetch_all(MYSQLI_ASSOC);

    // Close the statement
    $stmt->close();
} else {
    $query = "SELECT * FROM books WHERE `deleted` = 0 AND `status` = 'GOOD' ORDER BY `book_title` ASC";
    
    // Execute the query without parameters
    $result = $conn->query($query);

    // Fetch the results as needed
    $results = $result->fetch_all(MYSQLI_ASSOC);
}

*/

if (isset($_GET['search'])) {
    // Get the search query from the input field
    $searchQuery = $_GET['search'];

    // Modify the query to include the search condition
    $query = "SELECT * FROM books WHERE isbn LIKE '%$searchQuery%'
            OR book_title LIKE '%$searchQuery%'
            OR author LIKE '%$searchQuery%'
            OR year LIKE '%$searchQuery%'
            OR subject LIKE '%$searchQuery%'
            OR section LIKE '%$searchQuery%'
            OR publisher LIKE '%$searchQuery%'
            OR volume LIKE '%$searchQuery%'
            OR edition LIKE '%$searchQuery%'
            OR book_borrow_status LIKE '%$searchQuery%'
            OR status LIKE '%$searchQuery%'
            ";
}

$Array="";
// Add the following code at the beginning to handle AJAX requests
if (isset($_POST['section']) && isset($_POST['status']) && isset($_POST['dewey']) && isset($_POST['book_borrow_status'])) {
    $section = $_POST['section'];
    $status = $_POST['status'];
    $dewey = $_POST['dewey'];
    $bookBorrowStatus = $_POST['book_borrow_status'];

    // Use prepared statements to prevent SQL injection
    $query = "SELECT * FROM books WHERE `deleted` = 0 AND `status` = ?";
    $params = array("s", $status);

    $conditions = array(
        "*Select Section*" => array("field" => "section", "paramType" => "s"),
        "*Select Dewey Classification*" => array("field" => "dewey", "paramType" => "s"),
        "*Select Availability*" => array("field" => "book_borrow_status", "paramType" => "s")
    );

    // Append additional conditions based on the selected filters
    foreach ($conditions as $value => $condition) {
        if ($$condition['field'] !== $value) {
            $query .= " AND `" . $condition['field'] . "` = ?";
            $params[0] .= $condition['paramType']; // Append the type for string
            $params[] = $$condition['field'];
        }
    }

    $query .= " ORDER BY `book_title` ASC";

    // Assuming you are using mysqli
    $stmt = $conn->prepare($query);

    // Check for errors in preparing the statement
    if (!$stmt) {
        die("Error in preparing the statement: " . $conn->error);
    }

    // Bind the parameters using the splat operator
    $stmt->bind_param(...$params);

    // Execute the query
    $stmt->execute();

    // Fetch the results
    $result = $stmt->get_result();

    // Fetch data as an associative array
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    // Close the statement
    $stmt->close();
} else {
    $query = "SELECT * FROM books WHERE `deleted` = 0 AND `status` = 'GOOD' ORDER BY `book_title` ASC";

    // Execute the query without parameters
    $result = $conn->query($query);

    // Fetch the results as needed
    $rows = $result->fetch_all(MYSQLI_ASSOC);
}

// $rows now contains the results you fetched, and you can use it as needed.




function getBooksByPagination($conn, $query, $offset, $limit) {
    $query .= " LIMIT $limit OFFSET $offset"; // Append the LIMIT and OFFSET to the query for pagination
    $result = mysqli_query($conn, $query);
    
    return $result;
    }
    
    $totalBooksQuery = "SELECT COUNT(*) as total FROM books";
    $totalBooksResult = mysqli_query($conn, $totalBooksQuery);
    $totalBooks = mysqli_fetch_assoc($totalBooksResult)['total'];
    
    
    // Number of books to display per page
    $limit = 7;
    
    // Get the current page number from the query parameter
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    
    // Calculate the offset for the current page
    $offset = ($page - 1) * $limit;
    
    // Get the books for the current page
    $result = getBooksByPagination($conn, $query, $offset, $limit);
    
    // Check if the query executed successfully
    if ($result && mysqli_num_rows($result) > 0) {
        echo '<div class="container" id="book-list-container">';
        echo '<table id="dataTable">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Book Name</th>';
        echo '<th>Author</th>';
        echo '<th>Pubisher</th>';
        echo '<th>Year</th>';
        echo '<th>Volume</th>';
        echo '<th>Edition</th>';
        echo '<th>Section</th>';
        echo '<th>Availability</th>';
        echo '<th>Status</th>';
        echo '<th style="width:18%;">Action</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
    
        while ($book = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . $book['book_title'] . '</td>';
            echo '<td>' . $book['author'] . '</td>';
            echo '<td>' . $book['publisher'] . '</td>';
            echo '<td>' . $book['year'] . '</td>';
            echo '<td>' . $book['volume'] . '</td>';
            echo '<td>' . $book['edition'] . '</td>';
            echo '<td>' . $book['section'] . '</td>';
            if($book['book_borrow_status'] === 'Available') {
                echo '<td style="color:green; text-transform:uppercase;"><b>' . $book['book_borrow_status'] . '</b></td>';
            } else {
                echo '<td style="color:#FFBD33; text-transform:uppercase;"><b>' . $book['book_borrow_status'] . '</b></td>';
            }
    
            if ($book['status'] == 'GOOD') {
                echo '<td style="color: green;"><b><i>' . $book['status'] . '</i></b></td>';
            }
            echo '<td>';
            echo '<button type="button" class="btn btn-primary btn-sm"><i class="fa-solid fa-circle-info fa-sm"></i> Details</button>';
            echo '
            <a href="/LibMS/users/student/requests/borrow/borrow.php?book_id=' .$book['book_id']. '">
                <button type="button" class="btn btn-success btn-sm" style="margin-left:5px;"><i class="fa-solid fa-bookmark fa-sm"></i> Borrow</button>
            </a>
                ';
            echo '</td>';
            echo '</tr>';
        }
    
        echo '</tbody>';
        echo '</table>';
    
    
        // Calculate the total number of pages
        $totalPages = ceil($totalBooks / $limit);
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
                echo '<a href="?page='.($page + 1).'" class="btn btn-primary btn-sm" id="next" style="padding: 10px; width:10%; margin-left:5px;"> '.($page + 1).' Next <i class="fa-solid fa-angle-right"></i></a>';
            }
    
            echo '
            </div>
            ';
        }
    
    } else {
        echo "<tr><td colspan='10'><p class='container' style='margin-left:90px; margin-top:50px; font-size: 20px; font-weight:700;'>No Books Found.</p></td></tr>";
    }
    
    
// Close the database connection
mysqli_close($conn);
    
    
?>

