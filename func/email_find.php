<?php 

session_start();

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

$fetchEmail = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email-input'];

    $stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $fetchEmail = $data['email'];

        $_SESSION['data'] = $data;

        header("Location: /LibMS/func/enter_token.php");
        exit;

    } else {
        // Use JavaScript to trigger SweetAlert
        echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "No Accounts were matched with your given Email Address."
                });
              </script>';
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Recovery - Email - MyLibro</title>
    <!--Link for Tab ICON-->
    <link rel="icon" type="image/x-icon" href="/LibMS/resources/images/logov1.png">
    <!--Link for Bootstrap-->
    <link rel="stylesheet" type="text/css" href="/LibMS/resources/bootstrap/css/bootstrap.min.css"/>
    <script type="text/javascript" src="/LibMS/resources/bootstrap/js/bootstrap.min.js"></script>
    <!--Link for CSS File-->
    <link rel="stylesheet" type="text/css" href="/LibMS/func/css/email_find.css">
    <!--Link for Font Awesome Icons-->
    <link rel="stylesheet" href="/LibMS/resources/icons/fontawesome-free-6.4.0-web/css/all.css">
    <!--Link for Google Font-->
    <link rel="stylesheet" href="/LibMS/resources/fonts/fonts.css"/>
    <!--SweetAlert Links-->
    <script src="/LibMS/resources/SweetAlert/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="/LibMS/resources/SweetAlert/sweetalert2.min.css">

</head>

<body>

<div class="main-box">
    <div class="container-fluid center">
        <div class="row justify-content-md-center">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="form-box">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                <h2>Enter Account's Email</h2>
                                <br>
                                <label for="email-input">Enter the Registered Account's Email:</label>
                                    <div class="form-group">
                                        <input type="text" name="email-input" id="email-input" required="">
                                    </div>
                                <br>

                               <div class="form-group">
                                    <a href="/LibMS/main/login.php"><i class="fa fa-solid fa-arrow-left fa-lg"></i> Back</a>
                               </div>

                                <button class="btn btn-primary" type="submit"><i class="fa fa-solid fa-arrow-right"></i> Next</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>