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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo '<title>'. $firstname .' '. $lastname .' /Student: Books - MyLibro </title>'; ?>
    <!--Link for Tab ICON-->
    <link rel="icon" type="image/x-icon" href="/LibMS/resources/images/logov1.png">
    <!--Link for Bootstrap-->
    <link rel="stylesheet" type="text/css" href="/LibMS/resources/bootstrap/css/bootstrap.min.css"/>
    <script type="text/javascript" src="/LibMS/resources/bootstrap/js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!--Link for JQuery-->
    <script type="text/javascript" src="/LibMS/resources/jquery ui/jquery-ui.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/LibMS/resources/jquery ui/jquery-ui.min.css"/>
    <script type="text/javascript" src="/LibMS/resources/jquery/jquery-3.7.1.min.js"></script>
    <!--Link for CSS File-->
    <link rel="stylesheet" type="text/css" href="/LibMS/users/student/books/css/books.css">
    <!--Link for NAVBAR and SIDEBAR styling-->
    <link rel="stylesheet" type="text/css" href="/LibMS/users/student/css/navbar-sidebar.css">
    <!--Link for Font Awesome Icons-->
    <link rel="stylesheet" href="/LibMS/resources/icons/fontawesome-free-6.4.0-web/css/all.css">
    <!--Link for Google Font-->
    <link rel="stylesheet" href="/LibMS/resources/fonts/fonts.css"/>
    <!--Link For JQuery AJAX-->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="/LibMS/resources/jquery/jquery-3.7.1.min.js"></script>
    <script src="/LibMS/users/student/books/js/filter.js"></script>

</head>

<body>

<!--NAVBAR-->
<nav class="navbar navbar-expand-lg" id="navbar">
  <div class="container-fluid">
    <a class="navbar-brand" href="#"><img src="/LibMS/resources/images/logov1.png" width="30px" height="30px"></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="#"><i class="fa-solid fa-user fa-xs"></i> Dashboard</a>
        </li>
      </ul>

      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="?logout=true"><i class="fa-solid fa-right-from-bracket fa-xs"></i> Logout</a>
        </li>
      </ul>

      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="#">
            <?php
                    if (isset($_SESSION['id_no']) && isset($_SESSION['username'])) {
                        $idNo = $_SESSION['id_no'];
                        $username = $_SESSION['username'];
                                                    
                        // Query to retrieve the necessary columns from the database
                        $UserPicPath = "SELECT user_pic_data, user_pic_type FROM user_pics WHERE user_id = ? AND username = ?";
                        $statement = $conn->prepare($UserPicPath);
                        $statement->bind_param("is", $idNo, $username);
                                                    
                            if ($statement->execute()) {
                                $result = $statement->get_result();
                                                    
                                if ($row = $result->fetch_assoc()) {

                                    //echo '<div class="container col-sm-6 center">';
                                    // Use the "width" and "height" attributes to resize the image
                                    echo '<img src="data:image/png;base64,' . base64_encode($row["user_pic_data"]) . '" width="40" height="40" class="rounded-circle"/>';
                                    //echo '</div>';
                                } else {
                                    // If not found in the database, display the default image
                                    echo '<img src="/LibMS/resources/images/user.png" width=40" height="40" class="rounded-circle" style="margin-top: 10px; margin-bottom: 10px;">';
                                }
                            } else {
                                // Error in executing the SQL query
                                echo '<img src="/LibMS/resources/images/user.png" width="200" height="200" class="rounded-circle" style="margin-top: 10px; margin-bottom: 10px;">';
                                                        }
                        }
                                                        
                ?>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<!--NAVBAR-->

<!--SIDEBAR-->
<div id="sidebar">
            <ul>
                <li></li>
                <li>
                    <a href="/LibMS/users/student/index.php">
                        <i class="fa fa-user fa-sm"></i>
                        <span class="sidebar-name">
                            Dashboard
                        </span>
                    </a>
                </li>

                <li>
                    <a href="/LibMS/users/student/profile/user_settings.php">
                        <i class="fa fa-cogs fa-sm"></i>
                        <span class="sidebar-name">
                            User Options
                        </span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="fa fa-comments fa-sm"></i>
                        <span class="sidebar-name">
                            Messages
                        </span>
                    </a>
                </li>

                <li>
                    <a href="/LibMS/users/student/books/books.php">
                        <i class="fa fa-book fa-sm"></i>
                        <span class="sidebar-name">
                            Books
                        </span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="fa fa-bookmark fa-sm"></i>
                        <span class="sidebar-name">
                            Pending Borrow Requests
                        </span>
                    </a>
                </li>

                <li>
                    <a href="/LibMS/users/student/history/history.php">
                        <i class="fa fa-clock-rotate-left fa-sm"></i>
                        <span class="sidebar-name">
                            History
                        </span>
                    </a>
                </li>

                <li>
                    <a href="/LibMS/users/student/notification/notification.php">
                        <i class="fa fa-bell fa-sm"></i>
                        <span class="sidebar-name">
                            Notifications
                        </span>
                    </a>
                </li>

            </ul>

            <ul>
                <li>
                    <a href="?logout=true">
                        <i class="fa fa-right-from-bracket fa-sm"></i>
                        <span class="sidebar-name">
                            Logout
                        </span>
                    </a>
                </li>
            </ul>
    </div>
<!--SIDEBAR-->

<div class="table-box">
    <div class="container col-12 col-md-10">
        <div class="container">
            <div class="row">
                <div class="books-box">
                    <div class="container-fluid">

                        <div class="search-bar" id="searchForm">
                            <form method ="GET">
                                <input type="text" class="search" id="searchInput" placeholder ="Enter Book Section, Book Name, or Book's Status..">
                                <button type="submit" value="search" name="search" class="btn btn-primary btn-sm"><i class="fa-solid fa-search fa-sm"></i> Search</button>
                            </form>

                            <div class="dropdown-1">
                                    <select name="section" id="section-dd" onchange="filterBooks()">
                                        <option selected disabled>*Select Section*</option>
                                        <option onClick="filterSection('Fiction')">Fiction</option>
                                        <option onClick="filterSection('Mystery Thriller')">Mystery/Thriller</option>
                                        <option onClick="filterSection('Romance')">Romance</option>
                                        <option onClick="filterSection('Science Fiction')">Science Fiction/Fantasy</option>
                                        <option onClick="filterSection('Horror')">Horror</option>
                                        <option onClick="filterSection('Historical Fiction')">Historical Fiction</option>
                                        <option onClick="filterSection('Biography/Autobiography')">Biography/Autobiography</option>
                                        <option onClick="filterSection('Memoir')">Memoir</option>
                                        <option onClick="filterSection('History')">History</option>
                                        <option onClick="filterSection('Politics')">Politics and Current Events</option>
                                        <option onClick="filterSection('Science and Technology')">Science and Technology</option>
                                        <option onClick="filterSection('Business and Finance')">Business and Finance</option>
                                        <option onClick="filterSection('Self-Help and Personal Development')">Self-Help and Personal Development</option>
                                        <option onClick="filterSection('Art and Architecture')">Art and Architecture</option>
                                        <option onClick="filterSection('Travel and Adventure')">Travel and Adventure</option>
                                        <option onClick="filterSection('Cookbooks and Food Writing')">Cookbooks and Food Writing</option>
                                        <option onClick="filterSection('Young Adult Fiction')">Young Adult Fiction</option>
                                        <option onClick="filterSection('Graphic Novels and Comics')">Graphic Novels and Comics</option>
                                        <option onClick="filterSection('Poetry')">Poetry</option>
                                        <option onClick="filterSection('Religion and Spiritually')">Religion and Spiritually</option>
                                        <option onClick="filterSection('Philosophy')">Philosophy</option>
                                        <option onClick="filterSection('Reference and Dictionary')">Reference and Dictionary</option>
                                        <option onClick="filterSection('Foreign Languages')">Foreign Languages</option>
                                        <option onClick="filterSection('Others')">Others</option>
                                    </select>

                                    <select name="status" id="book-status" onchange="filterBooks()">
                                        <option selected disabled>*Select Book Status*</option>
                                        <option onClick="('GOOD')">GOOD</option>
                                        <option onClick="('DAMAGED')">DAMAGED</option>
                                        <option onClick="('DILAPITATED')">DILAPITATED</option>
                                        <option onClick="('LOST')">LOST</option>
                                    </select>

                                    <select name="dewey" id="dewey-classification" onchange="filterBooks()">
                                        <option selected disabled>*Select Dewey Classification*</option>
                                        <option class="option-title" onClick="filterDewey('000')" >000 - General Works</option>
                                        <option onClick="filterDewey('020')">020 - Library and Information Science</option>
                                        <option onClick="filterDewey('030')">030 - General Encylopedias</option>
                                        <option onClick="filterDewey('050')">050 - General Periodicals</option>
                                        <option onClick="filterDewey('060')">060 - General Organizations</option>
                                        <option class="option-title" onClick="filterDewey('100')">100 - Philosophy</option>
                                        <option onClick="filterDewey('110')">110 - Metaphysics</option>
                                        <option onClick="filterDewey('120')">120 - Speculative Philosophy</option>
                                        <option onClick="filterDewey('130')">130 - Psychology and Occultism</option>
                                        <option onClick="filterDewey('140')">140 - Philosophy (Gen.)</option>
                                        <option onClick="filterDewey('150')">150 - Psychology (Gen.)</option>
                                        <option onClick="filterDewey('160')">160 - Logic</option>
                                        <option class="option-title" onClick="filterDewey('200')">200 - Religion</option>
                                        <option onClick="filterDewey('220')">220 - The Bible</option>
                                        <option onClick="filterDewey('230')">230 - Christian Doctrine</option>
                                        <option onClick="filterDewey('290')">290 - Comparative and Other Religions</option>
                                        <option class="option-title" onClick="filterDewey('300')">300 - Social Sciences</option>
                                        <option onClick="filterDewey('310')">310 - Statistics</option>
                                        <option onClick="filterDewey('320')">320 - Political Science</option>
                                        <option onClick="filterDewey('330')">330 - Economics</option>
                                        <option onClick="filterDewey('340')">340 - Law</option>
                                        <option onClick="filterDewey('350')">350 - Public Administration</option>
                                        <option onClick="filterDewey('360')">360 - Social Welfare</option>
                                        <option onClick="filterDewey('370')">370 - Education</option>
                                        <option onClick="filterDewey('380')">380 - Public Service</option>
                                        <option onClick="filterDewey('390')">390 - Customs and Folklores</option>
                                        <option class="option-title" onClick="filterDewey('400')">400 - Language</option>
                                        <option onClick="filterDewey('410')">410 - Comparative Lingustics</option>
                                        <option onClick="filterDewey('420')">420 - English and Anglo Saxon</option>
                                        <option onClick="filterDewey('430')">430 - German Language</option>
                                        <option onClick="filterDewey('440')">440 - French</option>
                                        <option onClick="filterDewey('450')">450 - Italian, Romanian</option>
                                        <option onClick="filterDewey('460')">460 - Spanish, Portuguese</option>
                                        <option onClick="filterDewey('470')">470 - Latin and Other Italic Languages</option>
                                        <option onClick="filterDewey('480')">480 - Classical and Modern Greek</option>
                                        <option onClick="filterDewey('490')">490 - Other Langauges</option>
                                        <option class="option-title" onClick="filterDewey('500')">500 - Science</option>
                                        <option onClick="filterDewey('510')">510 - Mathematics</option>
                                        <option onClick="filterDewey('520')">520 - Astronomy</option>
                                        <option onClick="filterDewey('530')">530 - Physics</option>
                                        <option onClick="filterDewey('540')">540 - Chemistry</option>
                                        <option onClick="filterDewey('550')">550 - Earth Sciences</option>
                                        <option onClick="filterDewey('560')">560 - Paleontology</option>
                                        <option onClick="filterDewey('570')">570 - Life Sciences</option>
                                        <option onClick="filterDewey('580')">580 - Botanical Sciences</option>
                                        <option onClick="filterDewey('590')">590 - Zoological Sciences</option>
                                        <option class="option-title" onClick="filterDewey('600')">600 - Technology</option>
                                        <option onClick="filterDewey('610')">610 - Medical Services</option>
                                        <option onClick="filterDewey('620')">620 - Engineering</option>
                                        <option onClick="filterDewey('630')">630 - Agriculture</option>
                                        <option onClick="filterDewey('640')">640 - Domestic Sciences</option>
                                        <option onClick="filterDewey('650')">650 - Business and Management</option>
                                        <option onClick="filterDewey('660')">660 - Chemical Technology</option>
                                        <option onClick="filterDewey('670')">670 - Manufacturers</option>
                                        <option onClick="filterDewey('690')">690 - Buidling Construction</option>
                                        <option class="option-title" onClick="filterDewey('700')">700 - The Arts</option>
                                        <option onClick="filterDewey('710')">710 - Landscape and Civic Art</option>
                                        <option onClick="filterDewey('720')">720 - Architecture</option>
                                        <option onClick="filterDewey('730')">730 - Sculpture, Plastics</option>
                                        <option onClick="filterDewey('740')">740 - Drawing, Decorative Arts</option>
                                        <option onClick="filterDewey('750')">750 - Painting</option>
                                        <option onClick="filterDewey('760')">760 - Prints and Print Making </option>
                                        <option onClick="filterDewey('770')">770 - Photography</option>
                                        <option onClick="filterDewey('780')">780 - Music</option>
                                        <option onClick="filterDewey('790')">790 - Recreation, Performing Arts</option>
                                        <option class="option-title" onClick="filterDewey('800')">800 - Literature</option>
                                        <option onClick="filterDewey('810')">810 -  American Literature</option>
                                        <option onClick="filterDewey('820')">820 - English Literature</option>
                                        <option onClick="filterDewey('830')">830 - German Literature</option>
                                        <option onClick="filterDewey('840')">840 - French Literature</option>
                                        <option onClick="filterDewey('850')">850 - Italian Literature</option>
                                        <option onClick="filterDewey('860')">860 - Spanish, Portuguese Literature</option>
                                        <option onClick="filterDewey('870')">870 - Latin and Other Italic Literature</option>
                                        <option onClick="filterDewey('880')">880 - Classical and Modern Greek Literature</option>
                                        <option onClick="filterDewey('890')">890 - Other Literature</option>
                                        <option class="option-title" onClick="filterDewey('900')">900 - History and Geography</option>
                                        <option onClick="filterDewey('910')">910 - Geography Travel</option>
                                        <option onClick="filterDewey('920')">920 - Genealogy</option>
                                        <option onClick="filterDewey('930')">930 - Ancient History</option>
                                        <option onClick="filterDewey('940')">940 - Europe</option>
                                        <option onClick="filterDewey('950')">950 - Asia</option>
                                        <option onClick="filterDewey('960')">960 - Africa</option>
                                        <option onClick="filterDewey('970')">970 - North America</option>
                                        <option onClick="filterDewey('980')">980 - South America</option>
                                        <option onClick="filterDewey('990')">990 - Pacific Ocean Islands</option>
                                        <option onClick="filterDewey('991')">991 - Indonesia</option>
                                        <option onClick="filterDewey('993')">993 - New Zealand and Melanesia</option>
                                        <option onClick="filterDewey('994')">994 - Australia</option>
                                        <option onClick="filterDewey('995')">995 - New Guinea (Papua)</option>
                                        <option onClick="filterDewey('996')">996 - Polynesia</option>
                                        <option onClick="filterDewey('997')">997 - Atlantic Ocean Islands</option>
                                        <option onClick="filterDewey('998')">998 - Arctic Region</option>
                                        <option onClick="filterDewey('999')">999 - Antarctic Region</option>
                                        <option class="option-title" onClick="filterDewey('920')">920 - Biography and Collective Biography</option>
                                        <option value="filterDewey('920')">920 - Biography and Collective Biography</option>
                                    </select>

                                    <select name="book_borrow_status" id="book-avail" onchange="filterBooks()">
                                        <option selected disabled>*Select Availability*</option>
                                        <option onClick="filterBorrowStatus('Available')">Available</option>
                                        <option onClick="filterBorrowStatus('Pending Requests')">Pending Requests</option>
                                    </select>


                            </div>
            
                        <?php

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
                            $query = "SELECT * FROM books WHERE 'deleted' = 0 AND status = 'GOOD' AND book_borrow_status = 'Available' ORDER BY 'book_title' ASC";
                        
                            // Execute the query without parameters
                            $result = $conn->query($query);
                        
                            // Fetch the results as needed
                            $rows = $result->fetch_all(MYSQLI_ASSOC);
                        }
                        

                       /*// Add the following code at the beginning to handle AJAX requests
                        if (isset($_POST['section']) && isset($_POST['status']) && isset($_POST['dewey']) && isset($_POST['book_borrow_status'])) {
                            $section = $_POST['section'];
                            $status = $_POST['status'];
                            $dewey = $_POST['dewey'];
                            $bookBorrowStatus = $_POST['book_borrow_status'];

                            $query = "SELECT * FROM books WHERE `deleted` = 0 AND `status` = '$status'";

                            // Append additional conditions based on the selected filters
                            if ($section !== "*Select Section*") {
                                $query .= " AND `section` = '$section'";
                            }

                            if ($dewey !== "*Select Dewey Classification*") {
                                $query .= " AND `dewey` = '$dewey'";
                            }

                            if ($bookBorrowStatus !== "*Select Availability*") {
                                $query .= " AND `book_borrow_status` = '$bookBorrowStatus'";
                            }

                            $query .= " ORDER BY `book_title` ASC";
                        } else {
                            $query = "SELECT * FROM books WHERE 'deleted' = 0 AND status = 'GOOD' ORDER BY 'book_title' ASC";
                        }*/
                    

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
                                echo '<div>';
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
                
                <script>
                    
                    function filterBooks() {
                    var section = $("#section-dd").val();
                    var status = $("#book-status").val();
                    var dewey = $("#dewey-classification").val();
                    var bookBorrowStatus = $("#book-avail").val();

                    $.ajax({
                        type: "POST",
                        url: "/LibMS/users/student/books/books.php",
                        data: {
                            section: section,
                            status: status,
                            dewey: dewey,
                            book_borrow_status: bookBorrowStatus
                        },
                        success: function(response) {
                            $("#book-list-container").html(response);
                        },
                        error: function(xhr, status, error) {
                            console.error("Error filtering books:", error);
                        }
                    });
                }

                $(document).ready(function() {
                    // Intercept the form submission
                    $('#searchForm').submit(function(event) {
                        // Prevent the default form submission
                        event.preventDefault();
                        
                        // Get the search query from the input field
                        var searchQuery = $('#searchInput').val();
                        
                        // Make an AJAX request to the server
                        $.ajax({
                            type: 'GET',
                            url: '/LibMS/users/student/books/books.php',
                            data: { search: searchQuery },
                            success: function(response) {
                                // Display the search results in the #searchResults div
                                $('#book-list-container').html(response);
                            }
                            error: function(xhr, status, error) {
                            console.error("Error filtering books:", error);
                        }
                        });
                    });
                });


                </script>

            </div>

                <script>
                    // JavaScript function for handling pagination buttons
                    document.addEventListener("DOMContentLoaded", function () {
                        const previousBtn = document.getElementById("previous");
                        const nextBtn = document.getElementById("next");

                        if (previousBtn) {
                            previousBtn.addEventListener("click", function () {
                                // Go to the previous page by decrementing the current page number
                                let currentPage = parseInt("<?php echo $page; ?>");
                                if (currentPage > 1) {
                                    currentPage--;
                                    window.location.href = "?page=" + currentPage;
                                }
                            });
                        }

                        if (nextBtn) {
                            nextBtn.addEventListener("click", function () {
                                // Go to the next page by incrementing the current page number
                                let currentPage = parseInt("<?php echo $page; ?>");
                                let totalPages = parseInt("<?php echo $totalPages; ?>");
                                if (currentPage < totalPages) {
                                    currentPage++;
                                    window.location.href = "?page=" + currentPage;
                                }
                            });
                        }
                    });
                </script>

        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>

</body>
</html>
