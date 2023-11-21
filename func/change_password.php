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

//$fetchEmail = "";
$alert="";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $newPassword = $_POST['new-password'];
    $confirmPassword = $_POST['conf-password'];

    // Verify if the new password and confirm password match
    if ($newPassword !== $confirmPassword) {
        $alert = '<p class="alert-box" style="padding:10px; border:2px solid red; border-radius:10px; width:100%; font-size:12px;">
        <i class="fa-solid fa-x fa-md" style="color:red;"></i> New password and confirm password do not match.
        </p>';
    } else {
        // Check if the current password matches the one in the database
        $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $storedPassword = $row['password'];

            // Verify the current password against the stored hash
            if (password_verify($newPassword, $storedPassword)) {
                $alert = '<p class="alert-box" style="padding:10px; border:2px solid red; border-radius:10px; width:100%; font-size:12px;">
                <i class="fa-solid fa-triangle-exclamation fa-md" style="color:#F1C232;"></i> New password must be different from the old password.
                </p>';
            } else {
                // Update the password in the database
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
                $updateStmt->bind_param("ss", $hashedPassword, $email);

                if ($updateStmt->execute()) {
                    echo "<script>alert('Password changed successfully.');</script>";
                    header("Location: /LibMSv1/main/login.php");
                } else {
                    echo "<script>alert('Error updating password: " . $conn->error . "');</script>";
                }
            }
        } else {
            $alert = '<p class="alert-box" style="padding:10px; border:2px solid red; border-radius:10px; width:100%; font-size:12px;">
            <i class="fa-solid fa-x fa-md" style="color:red;"></i> User not found.
            </p>';
        }
        $stmt->close();
        $updateStmt->close();
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
    <link rel="stylesheet" type="text/css" href="/LibMS/func/css/change_password.css">
    <!--Link for Font Awesome Icons-->
    <link rel="stylesheet" href="/LibMS/resources/icons/fontawesome-free-6.4.0-web/css/all.css">
    <!--Link for Google Font-->
    <link rel="stylesheet" href="/LibMS/resources/fonts/fonts.css"/>
    <!--SweetAlert Links-->
    <script src="/LibMS/resources/SweetAlert/sweetalert2.all.min.js"></script>

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
                                <?php echo $alert;?>
                                <h2>Enter Account's New Password:</h2>
                                <br>

                                <label for="email-input">Enter the New Password:</label>
                                    <div class="form-group">
                                        <input type="password" name="new-password" id="new-password" required="">
                                    </div>
                                <br>

                                <label for="email-input">Enter Confirm Password:</label>
                                    <div class="form-group">
                                        <input type="password" name="conf-password" id="conf-password" required="">
                                    </div>
                                <br>

                               <div class="form-group">
                                    <a href="/LibMS/main/login.php"><i class="fa fa-solid fa-arrow-left fa-lg"></i> Back</a>
                               </div>

                                <button class="btn btn-primary" type="submit"><i class="fa fa-solid fa-floppy-disk"></i> Save</button>
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