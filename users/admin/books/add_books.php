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

$alert = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the POST values are set
    if (isset($_POST["section"]) && isset($_POST["dewey"]) && isset($_POST["status"]) &&
        isset($_POST["book_title"]) && isset($_POST["volume"]) && isset($_POST["edition"]) &&
        isset($_POST["year"]) && isset($_POST["author"]) && isset($_POST["publisher"]) && isset($_POST["isbn"])) {

        // Assign POST values to variables
        $section = $_POST["section"];
        $dewey = $_POST["dewey"];
        $status = $_POST["status"];
        $book_title = $_POST["book_title"];
        $volume = $_POST["volume"];
        $edition = $_POST["edition"];
        $year = $_POST["year"];
        $author = $_POST["author"];
        $publisher = $_POST["publisher"];
        $isbn = $_POST["isbn"];

        // Prepare and bind the SQL statement
        $stmt = $conn->prepare("INSERT INTO books (section, dewey, status, book_title, volume, edition, year, author, publisher, isbn, book_borrow_status, deleted) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Available', 0)");
        
        if ($stmt) {
            $stmt->bind_param("ssssssssss", $section, $dewey, $status, $book_title, $volume, $edition, $year, $author, $publisher, $isbn);

            // Execute the statement
            if ($stmt->execute()) {
                $alert = '<p class="alert-box center" style="margin-top:10px; margin-left:100px; padding:10px; border:2px solid green; border-radius:10px; width:60%; font-size:12px;">
                    <i class="fa-solid fa-check fa-md" style="color:green;"></i> Book was Successfully Added.
                    </p>';
            } else {
                $alert = '<p class="alert-box center" style="margin-top:10px; margin-left:100px; padding:10px; border:2px solid red; border-radius:10px; width:60%; font-size:12px;">
                    <i class="fa-solid fa-triangle-exclamation fa-md" style="color:red;"></i> An Error Occurred, Try Again.
                    </p>';
            }

            $stmt->close(); // Close the prepared statement
        } else {
            // Handle a failed prepared statement
            $alert = '<p class="alert-box center" style=" margin-top:10px; margin-left:100px; padding:10px; border:2px solid red; border-radius:10px; width:60%; font-size:12px;">
                <i class="fa-solid fa-triangle-exclamation fa-md" style="color:red;"></i> An Error Occurred.
                </p>';
        }

        // Close the database connection (if not already closed)
        $conn->close();
    } else {
        $alert = '<p class="alert-box center" style="margin-top:10px; margin-left:100px; padding:10px; border:2px solid #F1C232; border-radius:10px; width:60%; font-size:12px;">
            <i class="fa-solid fa-triangle-exclamation fa-md" style="color:#F1C232;"></i> Please Fill out All the Required Fields.
            </p>';
    }
}


/*

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the POST values are set
    $required_fields = ["section", "dewey", "status", "book_title", "volume", "edition", "year", "author", "publisher", "isbn"];
    $missing_fields = [];

    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }

    if (empty($missing_fields)) {
        // Assign POST values to variables
        $section = $_POST["section"];
        $dewey = $_POST["dewey"];
        $status = $_POST["status"];
        $book_title = $_POST["book_title"];
        $volume = $_POST["volume"];
        $edition = $_POST["edition"];
        $year = $_POST["year"];
        $author = $_POST["author"];
        $publisher = $_POST["publisher"];
        $isbn = $_POST["isbn"];

        // Assuming $conn is your database connection object
        $stmt = $conn->prepare("INSERT INTO books (section, dewey, status, book_title, volume, edition, year, author, publisher, isbn, deleted) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");

        if ($stmt) {
            $stmt->bind_param("ssssssssss", $section, $dewey, $status, $book_title, $volume, $edition, $year, $author, $publisher, $isbn);

            // Execute the statement
            if ($stmt->execute()) {
                $alert = '<p class="alert-box center" style="margin-top:10px; margin-left:100px; padding:10px; border:2px solid green; border-radius:10px; width:60%; font-size:12px;">
                    <i class="fa-solid fa-check fa-md" style="color:green;"></i> Book was Successfully Added.
                    </p>';
            } else {
                $alert = '<p class="alert-box center" style="margin-top:10px; margin-left:100px; padding:10px; border:2px solid red; border-radius:10px; width:60%; font-size:12px;">
                    <i class="fa-solid fa-triangle-exclamation fa-md" style="color:red;"></i> An Error Occurred, Try Again.
                    </p>';
            }

            $stmt->close(); // Close the prepared statement
        } else {
            // Handle a failed prepared statement
            $alert = '<p class="alert-box center" style=" margin-top:10px; margin-left:100px; padding:10px; border:2px solid red; border-radius:10px; width:60%; font-size:12px;">
                <i class="fa-solid fa-triangle-exclamation fa-md" style="color:red;"></i> An Error Occurred.
                </p>';
        }
    } else {
        $alert = '<p class="alert-box center" style="margin-top:10px; margin-left:100px; padding:10px; border:2px solid #F1C232; border-radius:10px; width:60%; font-size:12px;">
                <i class="fa-solid fa-triangle-exclamation fa-md" style="color:#F1C232;"></i> Please Fill out All the Required Fields: ' . implode(", ", $missing_fields) . '.
                </p>';
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the POST values are set
    if (isset($_POST["section"]) && isset($_POST["dewey"]) && isset($_POST["status"]) &&
        isset($_POST["book_title"]) && isset($_POST["volume"]) && isset($_POST["edition"]) &&
        isset($_POST["year"]) && isset($_POST["author"]) && isset($_POST["publisher"]) && isset($_POST["isbn"])) {

        // Assign POST values to variables
        $section = $_POST["section"];
        $dewey = $_POST["dewey"];
        $status = $_POST["status"];
        $book_title = $_POST["book_title"];
        $volume = $_POST["volume"];
        $edition = $_POST["edition"]; // Fixed variable name
        $year = $_POST["year"];
        $author = $_POST["author"];
        $publisher = $_POST["publisher"];
        $isbn = $_POST["isbn"];

        // Prepare and bind the SQL statement
        $stmt = $conn->prepare("INSERT INTO books (section, dewey, status, book_title, volume, edition, year, author, publisher, isbn, deleted) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
        
        if ($stmt) {
            $stmt->bind_param("sssssssss", $section, $dewey, $status, $book_title, $volume, $edition, $year, $author, $publisher, $isbn);

            // Execute the statement
            if ($stmt->execute()) {
                $alert = '<p class="alert-box" style="padding:10px; border:2px solid green; border-radius:10px; width:60%; font-size:12px;">
                    <i class="fa-solid fa-check fa-md" style="color:green;"></i> Book was Successfully Added.
                    </p>';
            } else {
                $alert = '<p class="alert-box" style="padding:10px; border:2px solid red; border-radius:10px; width:60%; font-size:12px;">
                    <i class="fa-solid fa-triangle-exclamation fa-md" style="color:red;"></i> An Error Occurred.
                    </p>';
            }

            $stmt->close(); // Close the prepared statement
        } else {
            // Handle a failed prepared statement
            $alert = '<p class="alert-box center" style=" margin-top:10px; margin-left:100px; padding:10px; border:2px solid red; border-radius:10px; width:60%; font-size:12px;">
                <i class="fa-solid fa-triangle-exclamation fa-md" style="color:red;"></i> An Error Occurred.
                </p>';
        }

        $conn->close();
    } else {
       $alert = '<p class="alert-box center" style="margin-top:10px; margin-left:100px; padding:10px; border:2px solid #F1C232; border-radius:10px; width:60%; font-size:12px;">
                <i class="fa-solid fa-triangle-exclamation fa-md" style="color:#F1C232;"></i> Please Fill out All the Required Fields: ' . implode(", ", $missing_fields) . '.
                </p>';
    }
}

*/

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
    <link rel="stylesheet" type="text/css" href="/LibMS/users/admin/books/css/add_books.css">
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

<div class="book-box">
    <div class="container">
        <div class="row">
            <div class="form-box">
                <div class="col-md-12">
                    <form class="add-book-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">

                        <div class="dropdown-1">
                            <label for="booksection">Select Section: </label>
                            &nbsp;&nbsp;                       
                            <select name="section" id="section" class="bsection">
                                <option selected disabled>*Select Book Section*</option>
                                <option value="Art and Architecture">Art and Architecture</option>
                                <option value="Biography/Autobiography">Biography/Autobiography</option>
                                <option value="Business and Finance">Business and Finance</option>
                                <option value="Cookbooks and Food Writing">Cookbooks and Food Writing</option>
                                <option value="Fiction">Fiction</option>
                                <option value="Foreign Languages">Foreign Languages</option>
                                <option value="Graphic Novels and Comics">Graphic Novels and Comics</option>
                                <option value="History">History</option>
                                <option value="Historical Fiction">Historical Fiction</option>
                                <option value="Horror">Horror</option>
                                <option value="Memoir">Memoir</option>
                                <option value="Mystery Thriller">Mystery/Thriller</option>
                                <option value="Others">Others</option>
                                <option value="Philosophy">Philosophy</option>
                                <option value="Politics and Current Events">Politics and Current Events</option>
                                <option value="Poetry">Poetry</option>
                                <option value="Reference and Dictionary">Reference and Dictionary</option>
                                <option value="Religion and Spiritually">Religion and Spiritually</option>
                                <option value="Romance">Romance</option>
                                <option value="Science and Technology">Science and Technology</option>
                                <option value="Science Fiction/Fantasy">Science Fiction/Fantasy</option>
                                <option value="Self-Help and Personal Development">Self-Help and Personal Development</option>
                                <option value="Travel and Adventure">Travel and Adventure</option>
                                <option value="Young Adult Fiction">Young Adult Fiction</option>
                            </select>
                        </div>

                        <div class="dropdown-group-1">
                            <label>Select Dewey Decimal Classification: </label>
                            &nbsp;&nbsp;
                            <select name="dewey" id="dewey" class="dewey_dd">
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
                                    <option class="option-title" value="920">930 - Others</option>
                                    <option value="930">930 - Others</option>
                            </select>

                            <label for="bookstatus" id="dropdown2">Select Book Status: </label>
                            &nbsp;&nbsp;
                            <select name="status" id="status" class="bstatus">
                                <option selected disabled>*Select Book Status*</option>
                                <option value="GOOD">GOOD</option>
                                <option value="DAMAGED">DAMAGED</option>
                                <option value="DILAPITATED">DILAPITATED</option>
                                <option value="LOST">LOST</option>
                            </select>
                        </div>
                        

                        <div class="form-group"></div>

                        <div class="form-group">
                            <label class="form-label">Title of the Book:</label>
                            <input type="text" class="form-control" id="book_title" name="book_title" placeholder="">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Volume:</label>
                            <input type="text" class="form-control" id="volume" name="volume" placeholder="">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Edition:</label>
                            <input type="text" class="form-control" id="edition" name="edition" placeholder="">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Year:</label>
                            <input type="text" class="form-control" id="year" name="year" placeholder="">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Author:</label>
                            <input type="text" class="form-control" id="author" name="author" placeholder="">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Publisher:</label>
                            <input type="text" class="form-control" id="publisher" name="publisher" placeholder="">
                        </div>

                        <div class="form-group">
                            <label class="form-label">ISBN:</label>
                            <input type="text" class="form-control" id="isbn" name="isbn" placeholder="">
                        </div>

                        <div class="form-button">
                            <button type="submit" class="btn btn-primary btn-sm" id="submit-btn"><i class="fa fa-solid fa-plus fa-sm"></i> Add Book</button>
                        </div>

                        <?php echo $alert; ?>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



<script>

    function successNotify() {
<div class="modal" tabindex="-1" role="dialog" id="success-modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-solid fa-check" style="color: green;"></i> Book Successfully Added!</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Book was Successfully Added in the Library Database</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
};

    function errorNotify() {
<div class="modal" tabindex="-1" role="dialog" id="error-modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-solid fa-x" style="color: red;"></i> An Error Occurred!</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Book was Unsuccessfully Added.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
};

    function warningNotify() {
<div class="modal" tabindex="-1" role="dialog" id="warning-modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-solid fa-exclamation" style="color: yellow;"></i> An Error Occurred!</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Please Fill out All the Required Fields.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
};


</script>

<body>
</html>