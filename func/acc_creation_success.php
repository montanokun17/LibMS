
<?php
session_start(); // Start the session

$servername = "localhost";
$user_name = "root";
$password = "";
$database = "mylibro";

// Create a connection
$conn = new mysqli($servername, $user_name, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_SESSION['acctype']) && $_SESSION['acctype'] === 'Student') {
    // User logged in or just registered as a student

    if (isset($_SESSION['id_no']) && isset($_SESSION['username'])) {
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
            $acctype = $row['acctype'];
            $email = $row['email'];
            $brgy = $row['brgy'];
            $con_num = $row['con_num'];

            // Update the session variables with fetched data (optional, in case there are changes in the database)
            $_SESSION['firstname'] = $firstname;
            $_SESSION['lastname'] = $lastname;
            $_SESSION['acctype'] = $acctype;
            $_SESSION['email'] = $email;
            $_SESSION['brgy'] = $brgy;
            $_SESSION['con_num'] = $con_num;

        } else {
            // Handle case when user is not found
            // For example, redirect to an error page or display an error message
            echo "User not found!";
        }
    } else {
        // Handle case when session data is missing
        // For example, redirect to a login page or display an error message
        echo "Session data missing or user not logged in!";
    }

} else {
    // User is not a student or not logged in
    // Redirect to a login page or display an error message
    echo "User is not logged in as a student!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success! Account Created - MyLibro </title>
    <!--Link for Tab ICON-->
    <link rel="icon" type="image/x-icon" href="/LibMS/resources/images/logov1.png">
    <!--Link for Bootstrap-->
    <link rel="stylesheet" type="text/css" href="/LibMS/resources/bootstrap/css/bootstrap.min.css"/>
    <script type="text/javascript" src="/LibMS/resources/bootstrap/js/bootstrap.min.js"></script>
    <!--Link for CSS File-->
    <link rel="stylesheet" type="text/css" href="/LibMS/func/css/acc_creation_success.css">
    <!--Link for Font Awesome Icons-->
    <link rel="stylesheet" href="/LibMS/resources/icons/fontawesome-free-6.4.0-web/css/all.css">
    <!--Link for Google Font-->
    <link rel="stylesheet" href="/LibMS/resources/fonts/fonts.css"/>

</head>

<body>

<div class="container box-1">
    <div class="container">
        <div class="row">
            <div class="message-box">
                <h1 class="title"><i class="fa-solid fa-badge-check"></i> Account Created Successfully!</h1>
                <p class="text-1">Your Account has been created! Welcome to the MyLibro Community!</p>

                <div class="button">
                    <a href=<?php if($acctype === 'Student') {
                        header('Location: /LibMS/users/student/index.php');
                    } elseif ($acctype === 'Guest') {
                        header('Location: /LibMS/users/guest/index.php');
                    } else {
                        header('Location: /LibMS/func/error_message.php');
                    }
                    
                    ?>>
                        <button class="btn bg-dark btn-primary btn-sm"><i class="fa fa-solid fa-arrow-to-right fa-sm"></i> Redirect to your Dashboard</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>