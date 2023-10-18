<?php
session_start(); // Start the session

$servername = "localhost";
$username = "root";
$password = "";
$database = "mylibro";

// Create a connection
$conn = new mysqli($servername, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $idNo = $_POST['id_no'];
    $pass_word = $_POST['password'];

    // Perform database query to check if the user exists
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];
        $acctype = $row['acctype'];
        $status = $row['status']; // Assuming you have a 'status' column for user status

        if ($status === 'Disabled') {
            echo '<script>alert("Login Failed, User Account is Disabled. Please Contact the Admin or the Librarian.");</script>';
        } else {
            // Check if the input password matches the hashed password
            if (password_verify($pass_word, $hashedPassword) || $pass_word === $hashedPassword) {
                // Password matches

                // Store user data in the session
                $_SESSION['username'] = $username;
                $_SESSION['acctype'] = $acctype;
                $_SESSION['id_no'] = $idNo;

                // Redirect based on the user's account type
                if ($acctype === 'Admin') {
                    header('Location: /LibMS/users/admin/index.php');
                } elseif ($acctype === 'Student') {
                    header('Location: /LibMS/users/student/index.php');
                } elseif ($acctype === 'Librarian') {
                    header('Location: /LibMS/users/librarian/index.php');
                } elseif ($acctype === 'Guest') {
                    header('Location: guest-page.php');
                }
                exit();
            } else {
                // Incorrect password
                echo '<script>alert("Invalid Password!");</script>';
            }
        }
    } else {
        // Invalid input, account does not exist
        echo '<script>alert("Invalid Input, Account does not exist!");</script>';
    }

    // Close the prepared statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>



<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MyLibro</title>
    <!--Link for Tab ICON-->
    <link rel="icon" type="image/x-icon" href="/LibMS/resources/images/logov1.png">
    <!--Link for Bootstrap-->
    <link rel="stylesheet" type="text/css" href="/LibMS/resources/bootstrap/css/bootstrap.min.css"/>
    <script type="text/javascript" src="/LibMS/resources/bootstrap/js/bootstrap.min.js"></script>
    <!--Link for CSS File-->
    <link rel="stylesheet" type="text/css" href="/LibMS/main/css/login.css">
    <!--Link for Font Awesome Icons-->
    <link rel="stylesheet" href="/LibMS/resources/icons/fontawesome-free-6.4.0-web/css/all.css">
    <!--Link for Google Font-->
    <link rel="stylesheet" href="/LibMS/resources/fonts/fonts.css"/>
</head>
<body>

    <div class="login-form">
        <div class="container-fluid">
            <div class="container">
                <div class="row">
                    <div class="card-title">Log In:</div>
                    <table>
                        <thead></thead>
                        <tbody>
                            <tr>
                                <td class="form-box">
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                    
                                    <label for="id_no">Account's ID Number:</label>
                                    <div class="form-group">
                                        <input type="text" name="id_no" id="id_no"  required="">
                                    </div>

                                    <label for="username">Username:</label>
                                    <div class="form-group">
                                        <input type="text" name="username" id="username"  required="">
                                    </div>

                                    <label for="password">Password:</label>
                                    <div class="form-group">
                                        <input type="password" name="password" id="password"  required="">
                                    </div>
                                    <br>

                                    <div class="container">
                                        <p class="forgot"><i>Can't remember your Password?</i>&nbsp <a class="forgot-link" href="/LibMS/func/enter_email.php"> Forgot Password</a></p>
                                    </div>

                                    <div class="btn-1">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa-solid fa-right-to-bracket"></i> Log In
                                        </button>
                                    </div>
                                    <br>

                                    <div class="btn-2">
                                        <a href="/LibMS/index.php">
                                            <button type="button" class="btn btn-primary">
                                                <i class="fa-solid fa-rotate-left fa-sm"></i> Go Back
                                            </button>
                                    </div>

                                </form>
                            </td>

                            <td class="box-2">
                                <div class="container">
                                    <p class="box-title">ANNOUNCEMENTS/NOTICE: </p>
                                    <p class="text-box">
                                        MyLibro: Online Virtual Library Management for Pasig City Library, 
                                        will be available to use soon. Stay tuned for the updates! 
                                    </p>
                                
                                </div>
                            </td>

                            </tr>
                        </tbody>
            </div>
        </div>
    </div>

</body>
</html>