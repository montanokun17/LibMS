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
    <?php echo '<title>'. $firstname .' '. $lastname .' /Books - MyLibro </title>'; ?>
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
            <img src="/LibMS/resources/images/user.png" 
            width="40" height="40" style="border:1px solid #000000;" class="rounded-circle">
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
                    <a href="#">
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
                            Pending Book Requests
                        </span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="fa fa-clock-rotate-left fa-sm"></i>
                        <span class="sidebar-name">
                            Books' Issuance/Return History
                        </span>
                    </a>
                </li>

                <li>
                    <a href="#">
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

                        <div class="search-bar">
                            <form method ="GET">
                                <input type="text" class="search" placeholder ="Enter Book Section, Book Name, or Book's Status..">
                                <button type="submit" name="search" class="btn btn-primary btn-sm"><i class="fa-solid fa-search fa-sm"></i> Search</button>
                            </form>

                            <div class="dropdown-1">
                                    <select name="section" id="section-dd">
                                        <option selected disabled>*Select Section*</option>
                                        <option value="Fiction">Fiction</option>
                                        <option value="Mystery Thriller">Mystery/Thriller</option>
                                        <option value="Romance">Romance</option>
                                        <option value="Science Fiction">Science Fiction/Fantasy</option>
                                        <option value="Horror">Horror</option>
                                        <option value="Historical Fiction">Historical Fiction</option>
                                        <option value="Biography/Autobiography">Biography/Autobiography</option>
                                        <option value="Memoir">Memoir</option>
                                        <option value="History">History</option>
                                        <option value="Politics">Politics and Current Events</option>
                                        <option value="Science and Technology">Science and Technology</option>
                                        <option value="Business and Finance">Business and Finance</option>
                                        <option value="Self-Help and Personal Development">Self-Help and Personal Development</option>
                                        <option value="Art and Architecture">Art and Architecture</option>
                                        <option value="Travel and Adventure">Travel and Adventure</option>
                                        <option value="Cookbooks and Food Writing">Cookbooks and Food Writing</option>
                                        <option value="Young Adult Fiction">Young Adult Fiction</option>
                                        <option value="Graphic Novels and Comics">Graphic Novels and Comics</option>
                                        <option value="Poetry">Poetry</option>
                                        <option value="Religion and Spiritually">Religion and Spiritually</option>
                                        <option value="Philosophy">Philosophy</option>
                                        <option value="Reference and Dictionary">Reference and Dictionary</option>
                                        <option value="Foreign Languages">Foreign Languages</option>
                                        <option value="Others">Others</option>
                                    </select>

                                    <select name="status" id="book-status">
                                        <option selected disabled>*Select Book Status*</option>
                                        <option value="GOOD">GOOD</option>
                                        <option value="DAMAGED">DAMAGED</option>
                                        <option value="DILAPITATED">DILAPITATED</option>
                                        <option value="LOST">LOST</option>
                                    </select>

                                    <select name="dewey" id="dewey-classification">
                                        <option selected disabled>*Select Dewey Classification*</option>
                                        <option class="option-title" value="000" >000 - General Works</option>
                                        <option value="020">020 - Library and Information Science</option>
                                        <option value="030">030 - General Encylopedias</option>
                                        <option value="050">050 - General Periodicals</option>
                                        <option value="060">060 - General Organizations</option>
                                        <option class="option-title" value="100">100 - Philosophy</option>
                                        <option value="110">110 - Metaphysics</option>
                                        <option value="120">120 - Speculative Philosophy</option>
                                        <option value="130">130 - Psychology and Occultism</option>
                                        <option value="140">140 - Philosophy (Gen.)</option>
                                        <option value="150">150 - Psychology (Gen.)</option>
                                        <option value="160">160 - Logic</option>
                                        <option class="option-title" value="200">200 - Religion</option>
                                        <option value="220">220 - The Bible</option>
                                        <option value="230">230 - Christian Doctrine</option>
                                        <option value="290">290 - Comparative and Other Religions</option>
                                        <option class="option-title" value="300">300 - Social Sciences</option>
                                        <option value="310">310 - Statistics</option>
                                        <option value="320">320 - Political Science</option>
                                        <option value="330">330 - Economics</option>
                                        <option value="340">340 - Law</option>
                                        <option value="350">350 - Public Administration</option>
                                        <option value="360">360 - Social Welfare</option>
                                        <option value="370">370 - Education</option>
                                        <option value="380">380 - Public Service</option>
                                        <option value="390">390 - Customs and Folklores</option>
                                        <option class="option-title" value="400">400 - Language</option>
                                        <option value="410">410 - Comparative Lingustics</option>
                                        <option value="420">420 - English and Anglo Saxon</option>
                                        <option value="430">430 - German Language</option>
                                        <option value="440">440 - French</option>
                                        <option value="450">450 - Italian, Romanian</option>
                                        <option value="460">460 - Spanish, Portuguese</option>
                                        <option value="470">470 - Latin and Other Italic Languages</option>
                                        <option value="480">480 - Classical and Modern Greek</option>
                                        <option value="490">490 - Other Langauges</option>
                                        <option class="option-title" value="500">500 - Science</option>
                                        <option value="510">510 - Mathematics</option>
                                        <option value="520">520 - Astronomy</option>
                                        <option value="530">530 - Physics</option>
                                        <option value="540">540 - Chemistry</option>
                                        <option value="550">550 - Earth Sciences</option>
                                        <option value="560">560 - Paleontology</option>
                                        <option value="570">570 - Life Sciences</option>
                                        <option value="580">580 - Botanical Sciences</option>
                                        <option value="590">590 - Zoological Sciences</option>
                                        <option class="option-title" value="600">600 - Technology</option>
                                        <option value="610">610 - Medical Services</option>
                                        <option value="620">620 - Engineering</option>
                                        <option value="630">630 - Agriculture</option>
                                        <option value="640">640 - Domestic Sciences</option>
                                        <option value="650">650 - Business and Management</option>
                                        <option value="660">660 - Chemical Technology</option>
                                        <option value="670">670 - Manufacturers</option>
                                        <option value="690">690 - Buidling Construction</option>
                                        <option class="option-title" value="700">700 - The Arts</option>
                                        <option value="710">710 - Landscape and Civic Art</option>
                                        <option value="720">720 - Architecture</option>
                                        <option value="730">730 - Sculpture, Plastics</option>
                                        <option value="740">740 - Drawing, Decorative Arts</option>
                                        <option value="750">750 - Painting</option>
                                        <option value="760">760 - Prints and Print Making </option>
                                        <option value="770">770 - Photography</option>
                                        <option value="780">780 - Music</option>
                                        <option value="790">790 - Recreation, Performing Arts</option>
                                        <option class="option-title" value="800">800 - Literature</option>
                                        <option value="810">810 -  American Literature</option>
                                        <option value="820">820 - English Literature</option>
                                        <option value="830">830 - German Literature</option>
                                        <option value="840">840 - French Literature</option>
                                        <option value="850">850 - Italian Literature</option>
                                        <option value="860">860 - Spanish, Portuguese Literature</option>
                                        <option value="870">870 - Latin and Other Italic Literature</option>
                                        <option value="880">880 - Classical and Modern Greek Literature</option>
                                        <option value="890">890 - Other Literature</option>
                                        <option class="option-title" value="900">900 - History and Geography</option>
                                        <option value="910">910 - Geography Travel</option>
                                        <option value="920">920 - Genealogy</option>
                                        <option value="930">930 - Ancient History</option>
                                        <option value="940">940 - Europe</option>
                                        <option value="950">950 - Asia</option>
                                        <option value="960">960 - Africa</option>
                                        <option value="970">970 - North America</option>
                                        <option value="980">980 - South America</option>
                                        <option value="990">990 - Pacific Ocean Islands</option>
                                        <option value="991">991 - Indonesia</option>
                                        <option value="993">993 - New Zealand and Melanesia</option>
                                        <option value="994">994 - Australia</option>
                                        <option value="995">995 - New Guinea (Papua)</option>
                                        <option value="996">996 - Polynesia</option>
                                        <option value="997">997 - Atlantic Ocean Islands</option>
                                        <option value="998">998 - Arctic Region</option>
                                        <option value="999">999 - Antarctic Region</option>
                                        <option class="option-title" value="920">920 - Biography and Collective Biography</option>
                                        <option value="920">920 - Biography and Collective Biography</option>
                                    </select>

                                    <select name="book_borrow_status" id="book-avail">
                                        <option selected disabled>*Select Availability*</option>
                                        <option value="Available">Available</option>
                                        <option value="Pending Requests">Pending Requests</option>
                                    </select>


                            </div>


                        </div>
            
                        <?php

                        if (isset($_GET['search'])) {
                            // Get the search query from the input field /onsubmit="searchBooks(); return false;
                            $searchQuery = $_GET['search'];

                            // Modify the query to include the search condition
                            $query = "SELECT * FROM books WHERE isbn LIKE '%$searchQuery%'
                                    OR book_title LIKE '%$searchQuery%'
                                    OR author LIKE '%$searchQuery%'
                                    OR year LIKE '%$searchQuery%'
                                    OR subject LIKE '%$searchQuery%'
                                    OR section LIKE '%$searchQuery%'
                                    OR stocks LIKE '%$searchQuery%'
                                    OR author LIKE '%$searchQuery%'
                                    OR volume LIKE '%$searchQuery%'
                                    OR status LIKE '%$searchQuery%'
                                    ";
                        } else {
                            // Default query to fetch all books
                            $query = "SELECT * FROM books WHERE 'deleted' = 0 AND status = 'GOOD' AND book_borrow_status = 'Available' ORDER BY 'book_title' ASC";
                        }

                        // Default query to fetch all books
                        //$query = "SELECT * FROM books WHERE 'deleted' = 0 AND status = 'GOOD' AND book_borrow_status = 'Available' ORDER BY 'book_title' ASC";


                        /*// Get selected values from AJAX request
                        $selectedSection = $_POST['section'];
                        $selectedStatus = $_POST['status'];
                        $selectedDewey = $_POST['dewey'];
                        $selectedAvailability = $_POST['availability'];

                        // Modify your query based on selected values
                        $query = "SELECT * FROM books WHERE 
                                ('deleted' = 0) AND 
                                ('status' = '$selectedStatus' OR '$selectedStatus' = '*Select Book Status*') AND 
                                ('book_borrow_status' = '$selectedAvailability' OR '$selectedAvailability' = '*Select Availability*') AND 
                                ('section' = '$selectedSection' OR '$selectedSection' = '*Select Section*') AND 
                                ('dewey' = '$selectedDewey' OR '$selectedDewey' = '*Select Dewey Classification*') 
                                ORDER BY 'book_title' ASC";
                        */   
                                

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
                                echo '<div class="container">';
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
                                        '; //view_entry.php?id={$row['id']} /LibMS/users/student/requests/borrow/borrow.php
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

                    function searchBooks() {
                            // Get the input value
                            var searchQuery = document.getElementById('searchInput').value.toLowerCase();

                            // Get all table rows
                            var rows = document.getElementById('dataTable').getElementsByTagName('tr');

                            // Loop through each row and hide/show based on the search query
                            for (var i = 0; i < rows.length; i++) {
                                var rowText = rows[i].textContent.toLowerCase();

                                // If the row text contains the search query, display the row, otherwise hide it
                                if (rowText.includes(searchQuery)) {
                                    rows[i].style.display = '';
                                } else {
                                    rows[i].style.display = 'none';
                                }
                            }
                        }

                    /*
                    // Get references to the dropdowns
                    var sectionDropdown = document.getElementById('section-dd');
                    var statusDropdown = document.getElementById('book-status');
                    var deweyDropdown = document.getElementById('dewey-dd'); // Change to a unique ID
                    var availabilityDropdown = document.getElementById('book-avail');

                    // Add event listeners to the dropdowns
                    sectionDropdown.addEventListener('change', filterBooks);
                    statusDropdown.addEventListener('change', filterBooks);
                    deweyDropdown.addEventListener('change', filterBooks);
                    availabilityDropdown.addEventListener('change', filterBooks);

                    // Example: Replace this with your actual array of books
                    var yourBooksArray = [
                        // Sample book objects
                        { section: 'Fiction', status: 'GOOD', dewey: '100', availability: 'Available' },
                        // Add more books as needed
                    ];

                    // Function to filter books based on selected values
                    function filterBooks() {
                        // Get selected values
                        var selectedSection = sectionDropdown.value;
                        var selectedStatus = statusDropdown.value;
                        var selectedDewey = deweyDropdown.value;
                        var selectedAvailability = availabilityDropdown.value;

                        // Perform filtering based on selected values
                        var filteredBooks = yourBooksArray.filter(function (book) {
                            return (
                                (selectedSection === '*Select Section*' || book.section === selectedSection) &&
                                (selectedStatus === '*Select Book Status*' || book.status === selectedStatus) &&
                                (selectedDewey === '*Select Dewey Classification*' || book.dewey === selectedDewey) &&
                                (selectedAvailability === '*Select Availability*' || book.availability === selectedAvailability)
                            );
                        });

                        // Use the filteredBooks array to update your UI or perform other actions
                        console.log(filteredBooks);
                    }*/
                </script>



                <div class="modal fade" id="BorrowModal" tabindex="-1" role="dialog" aria-labelledby="borrowModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modal-title">Modal title</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>

                                    <div class="modal-body">
                                        <form>
                                            <label>Title:</label>
                                            <p>Title</p>

                                            <label>Year:</label>
                                            <p>Year</p>

                                            <label>Author:</label>
                                            <p>Author</p>

                                            <label>Volume:</label>
                                            <p>Volume</p>

                                            <label>Edition:</label>
                                            <p>Edition</p>

                                            <label>Section:</label>
                                            <p>Section</p>
                                            
                                            <label></label>
                                            <button type="button" class="btn btn-primary btn-sm" id="borrow-btn"><i class="fa-solid fa-share-from-square"></i> Send Borrow Request</button>
                                        </form>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>

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
