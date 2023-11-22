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
$alert="";
$userEmail="";

$data = $_SESSION['data'];
$fetchEmail = $data['email'];


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the entered PIN from the form
    $enteredPin = $_POST["pin"];

    // Retrieve the user's email address
    // Replace $email, $firstname, $lastname with the actual variables containing the email and user details
    //$userEmail = $email;
    $fetchEmail = $userEmail;

    // Retrieve the saved token PIN and timestamp from the database
    // Assume you have columns named 'token_pin' and 'pin_timestamp' in the 'users' table
    $stmt = $conn->prepare("SELECT token_pin, pin_timestamp FROM users WHERE email = ?");
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $stmt->bind_result($tokenPin, $pinTimestamp);
    $stmt->fetch();
    $stmt->close();

    // Compare the entered PIN with the saved token PIN
    if ($enteredPin == $tokenPin) {
        // Verify the timestamp to ensure it is within the allowed timeframe (5 minutes)
        $currentTime = time();
        $tokenExpirationTime = strtotime($pinTimestamp) + (5 * 60); // Add 5 minutes to the saved timestamp

        if ($currentTime <= $tokenExpirationTime) {
            // PIN is correct and within the allowed timeframe
            // Redirect the user to the designated page
            header("Location: /LibMS/func/change_password.php");
            exit();
        } else {
            // PIN is correct but has expired
            $alert = '<p class="alert-box" style="padding:10px; border:2px solid red; border-radius:10px; width:100%; font-size:12px;">
                      <i class="fa-solid fa-triangle-exclamation fa-md" style="color:#F1C232;"></i> The PIN has expired. Please request a new PIN.
                      </p>';
        }
    } else {
        // PIN is incorrect
        $alert = '<p class="alert-box" style="padding:10px; border:2px solid red; border-radius:10px; width:100%; font-size:12px;">
                  <i class="fa-solid fa-x fa-md" style="color:red;"></i> Invalid PIN. Please try again.
                  </p>';
    }
}


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Recovery - Token/PIN Enter - MyLibro</title>
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

                            <?php echo $alert; ?>

                                <h2>Email Token PIN</h2>
                                <br>
                                <label for="pin">Enter Token PIN Sent on the Email:</label>
                                    <div class="form-group">
                                        <input type="text" name="pin" id="pin" required="">
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

<?php

     /*//PHPMailer library
     require 'D:/xampp/htdocs/LibMSv1/resources/mail/phpmailer/PHPMailerAutoload.php';

     // Generate random 6-digit token PIN
     $tokenPin = rand(100000, 999999);

     // User's email address (you can retrieve it from the database)
     $userEmail = $fetchEmail;

     // Save the token PIN and timestamp in the database for verification
     $stmt = $conn->prepare("UPDATE users SET token_pin = ?, pin_timestamp = NOW() WHERE email = ?");
     $stmt->bind_param("ss", $tokenPin, $userEmail);
     $stmt->execute();

     // Configure PHPMailer
     $mail = new PHPMailer();
     $mail->isSMTP();
     $mail->Host = 'smtp.office365.com';  // Set your SMTP server
     $mail->SMTPAuth = true;
     $mail->Username = 'mylibrolibrarymanagementsystem@outlook.com';
     $mail->Password = 'mylibro01';
     $mail->SMTPSecure = 'tls';
     $mail->Port = 587;

     $mail->setFrom('mylibrolibrarymanagementsystem@outlook.com', 'MyLibro - Virtual Library Management System');  // Set the sender's email address and name
     $mail->addAddress($userEmail);  // Add recipient email address
     //$mail->addEmbeddedImage('/LibMS/resources/images/logov1.png', 'my-image');

     $mail->isHTML(true);
     $mail->Subject = 'Token PIN Verification';  // Set the email subject
     $mail->Body = '
         <!DOCTYPE html>
         <html lang="en">
         <head>
             <meta charset="UTF-8">
             <meta name="viewport" content="width=device-width, initial-scale=1.0">
             <title>PIN Verification Email</title>
         </head>
         <body style="font-family: Roboto, Arial;">

             <table align="center" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse;">
                 <tr>
                     <td align="center" bgcolor="#4ca847" style="padding: 40px 0 30px 0;">
                         <img src="https://i.ibb.co/7tP7sN1/logov1.png" alt="MyLibro - Virtual LMS" width="200" height="200" style="display: block;">
                         <h2 style="font-size: 24px; color: #333333; margin-top: 10px;">MyLibro - Library Management System</h2>
                         <h2 style="font-size: 32px; color: #333333; margin-top: 30px;">Password PIN Verification</h2>
                     </td>
                 </tr>
                 <tr>
                     <td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;">
                         <p style="font-size: 16px; color: #333333;">Good Day! Dear User,</p>
                         <p style="font-size: 16px; color: #333333;">Your verification PIN is: <strong>' . $tokenPin . '</strong></p>
                         <p style="font-size: 16px; color: #333333;">Please enter this PIN to proceed with the verification process. This PIN is 
                         only available for 5 minutes.</p>
                         <p style="font-size: 10px; color: #333333;">Note: If you do not recognize this activity, disregard this email.</p>
                     </td>
                 </tr>
                 <tr>
                     <td bgcolor="#4ca847" style="padding: 10px 30px; color: #ffffff; font-size: 12px; text-align: center;">
                         <b>MyLibro - Virtual Library Management System &copy; 2023</b>
                     </td>
                 </tr>
             </table>

         </body>
         </html>
     ';  // Set the email body

     // Send the email
     if ($mail->send()) {
        $alert = '<p class="alert-box" style="padding:10px; border:2px solid red; border-radius:10px; width:100%; font-size:12px;">
        <i class="fa-solid fa-check fa-md" style="color:green;"></i> Email Sent Successfully
        </p>';
         
     } else {
         //echo '<div class="card"><div class="card-body"><p>Error Sending Email. ' . $mail->ErrorInfo . '</p></div></div>';
         $alert = '<p class="alert-box" style="padding:10px; border:2px solid red; border-radius:10px; width:100%; font-size:12px;">
                   Error Sending Email. ' .$mail->ErrorInfo . '
                  </p>';
     }*/

?>