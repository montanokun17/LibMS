<?php
session_start();

$servername = "localhost"; // Replace with your server name if different
$user_name = "root"; // Replace with your database username
$Password = ""; // Replace with your database password
$database = "mylibro"; // Replace with your database name

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
    <!--Link for CSS File-->
    <link rel="stylesheet" type="text/css" href="/LibMS/users/admin/books/css/books.css">
    <!--Link for NAVBAR and SIDEBAR styling-->
    <link rel="stylesheet" type="text/css" href="/LibMS/users/admin/css/navbar-sidebar.css">
    <!--Link for Font Awesome Icons-->
    <link rel="stylesheet" href="/LibMS/resources/icons/fontawesome-free-6.4.0-web/css/all.css">
    <!--Link for Google Font-->
    <link rel="stylesheet" href="/LibMS/resources/fonts/fonts.css"/>

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
          <a class="nav-link" aria-current="page" href="#"><i class="fa-solid fa-cogs fa-xs"></i> Homepage Settings</a>
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
            <img src="/LibMSv1/resources/images/user.png" 
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
                    <a href="/LibMS/users/admin/index.php">
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
                        <i class="fa fa-users fa-sm"></i>
                        <span class="sidebar-name">
                            Accounts
                        </span>
                    </a>
                </li>
                
                <li>
                    <a href="#">
                        <i class="fa fa-solid fa-qrcode fa-sm"></i>
                        <span class="sidebar-name">
                            QR Code and ID Card
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
                    <a href="/LibMS/users/admin/books/books.php">
                        <i class="fa fa-book fa-sm"></i>
                        <span class="sidebar-name">
                            Books
                        </span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="fa fa-plus fa-sm"></i>
                        <span class="sidebar-name">
                            Add a Book
                        </span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="fa fa-bars fa-sm"></i>
                        <span class="sidebar-name">
                            Issue/Return Requests
                        </span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="fa fa-book fa-sm"></i>
                        <span class="sidebar-name">
                            Issued/Returned Books Log
                        </span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="fa fa-trash fa-sm"></i>
                        <span class="sidebar-name">
                            Recent Deletion Books
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
                                <button type="submit" name="search" class="btn btn-primary bg-dark btn-sm"><i class="fa-solid fa-search fa-sm"></i> Search</button>
                            </form>

                            <div class="dropdown-1">
                                    <select name="section" id="section-dd">
                                        <option></option>
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

                                    <select name="dewey" id="book-status">
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
                            </div>

                        </div>
            
                <?php

                    // Default query to fetch all books
                    $query = "SELECT * FROM books WHERE 'deleted' = 0 ORDER BY book_id DESC";

                function getBooksByPagination($conn, $query, $offset, $limit) {
                    $query .= " LIMIT $limit OFFSET $offset"; // Append the LIMIT and OFFSET to the query for pagination
                    $result = mysqli_query($conn, $query);

                    return $result;
                }

                $totalBooksQuery = "SELECT COUNT(*) as total FROM books";
                $totalBooksResult = mysqli_query($conn, $totalBooksQuery);
                $totalBooks = mysqli_fetch_assoc($totalBooksResult)['total'];


                // Number of books to display per page
                $limit = 8;

                // Get the current page number from the query parameter
                $page = isset($_GET['page']) ? intval($_GET['page']) : 1;

                // Calculate the offset for the current page
                $offset = ($page - 1) * $limit;

                // Get the books for the current page
                $result = getBooksByPagination($conn, $query, $offset, $limit);

                    // Check if the query executed successfully
                    if ($result && mysqli_num_rows($result) > 0) {
                        echo '<div class="container">';
                        echo '<table>';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th>ISBN</th>';
                        echo '<th>Book Name</th>';
                        echo '<th>Author</th>';
                        echo '<th>Year</th>';
                        echo '<th>Volume</th>';
                        echo '<th>Section</th>';
                        echo '<th>Stocks</th>';
                        echo '<th>Status</th>';
                        echo '<th>Action</th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';

                        while ($book = mysqli_fetch_assoc($result)) {
                            echo '<tr>';
                            echo '<td>' . $book['isbn'] . '</td>';
                            echo '<td>' . $book['book_title'] . '</td>';
                            echo '<td>' . $book['author'] . '</td>';
                            echo '<td>' . $book['year'] . '</td>';
                            echo '<td>' . $book['volume'] . '</td>';
                            echo '<td>' . $book['section'] . '</td>';
                            echo '<td>' . $book['stocks'] . '</td>';
                            if ($book['status'] == 'GOOD') {
                                echo '<td style="color: green;"><b><i>' . $book['status'] . '</i></b></td>';
                            } else if ($book['status'] == 'DAMAGED') {
                                echo '<td style="color: orange;"><b><i>' . $book['status'] . '</i></b></td>';
                            } else if ($book['status'] == 'DILAPITATED') {
                                echo '<td style="color: red;"><b><i>' . $book['status'] . '</i></b></td>';
                            } else {
                                echo '<td style="color: grey;"><b><i>' . $book['status'] . '</i></b></td>';
                            }
                            echo '<td>';
                            echo '<button type="button" class="btn btn-success btn-sm"><i class="fa-solid fa-circle-info fa-sm"></i> Details</button>';
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
                                echo '<a href="?page='.($page + 1).'" class="btn btn-primary btn-sm" id="next" style="padding: 10px; width:10%;"> '.($page + 1).' Next <i class="fa-solid fa-angle-right"></i></a>';
                            }
                    
                            echo '
                            </div>
                            ';
                        }

                    } else {
                        echo "<tr><td colspan='10'>No books found.</td></tr>";
                    }


                    // Close the database connection
                    mysqli_close($conn);

                ?>

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

                        <?php

                        /*$query = "SELECT * FROM books ORDER BY book_id DESC";

                            // Check if the query executed successfully
                        if ($result && mysqli_num_rows($result) > 0) {
                            
                            echo '<thead>';
                            echo '<tr>';
                            echo '<th>Dewey Decimal</th>';
                            echo '<th>ISBN</th>';
                            echo '<th>Book Title</th>';
                            echo '<th>Author</th>';
                            echo '<th>Section</th>';
                            echo '<th>Volume</th>';
                            echo '<th>Year</th>';
                            echo '<th>Stocks</th>';
                            echo '<th>Book Status</th>';
                            echo '</tr>';
                            echo '</thead>';

                            while ($book = mysqli_fetch_assoc($result)) {
                                echo '<tbody>';
                                echo '<tr>';
                                echo '<td>' . $book['dewey']. '</td>';
                                echo '<td>' . $book['isbn']. '</td>';
                                echo '<td>' . $book['book_title']. '</td>';
                                echo '<td>' . $book['author']. '</td>';
                                echo '<td>' . $book['section']. '</td>';
                                echo '<td>' . $book['volume']. '</td>';
                                echo '<td>' . $book['year']. '</td>';
                                echo '<td>' . $book['stocks ']. '</td>';
                                echo '</tr>';
                                
                                if ($book['status'] == 'GOOD') {
                                    echo '<td style="color: green;"><b><i>' . $book['status'] . '</i></b></td>';
                                } else if ($book['status'] == 'DAMAGED') {
                                    echo '<td style="color: orange;"><b><i>' . $book['status'] . '</i></b></td>';
                                } else if ($book['status'] == 'DILAPITATED') {
                                    echo '<td style="color: red;"><b><i>' . $book['status'] . '</i></b></td>';
                                } else {
                                    echo '<td style="color: grey;"><b><i>' . $book['status'] . '</i></b></td>';
                                }
                                echo '<td>';
                                echo '<button type="button" class="btn btn-success btn-sm"><i class="fa-solid fa-circle-info fa-sm"></i> Details</button>';
                                echo '</td>';
                                echo '</tr>';
                            }

                            echo '</tbody>';
                            echo '</table>';
                            
                        }   */

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


</body>
</html>

