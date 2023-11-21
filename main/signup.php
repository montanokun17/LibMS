<?php

session_start();

$servername = "localhost"; // Replace with your server name if different
$user_name = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$database = "mylibro"; // Replace with your database name

// Create a connection
$conn = new mysqli($servername, $user_name, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $con_num = $_POST['con_num'];
    $password = $_POST['password'];
    $acctype = $_POST['acctype'];
    $schlvl = $_POST['schlvl'];
    
     // Check if the user is a Pasig resident or not
     if (isset($_POST['pasigresd'])) {
        // User is a Pasig resident, so set the $brgy variable based on the selected option
        $brgy = $_POST['brgy'];
    } elseif (isset($_POST['notpasigresd'])) {
        // User is not a Pasig resident, so set the $brgy variable based on the input field
        $brgy = $_POST['nonPasigbrgy'];
    } else {
        // Handle the case where neither option is selected (you can add error handling here)
    }

    // Prepare and bind the SQL query to check if email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND con_num = ?");
    $stmt->bind_param("ss", $email, $con_num);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email already exists in the database
        echo "<script>alert('Email or Contact Number is already in use. Please choose a different one.');</script>";
    } else {
        // Email does not exist, proceed with account creation

        // Hash the password using bcrypt
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Prepare and bind the SQL query with placeholders
        $stmt = $conn->prepare("INSERT INTO users (username, firstname, lastname, email, con_num, password, acctype, schlvl, brgy, status) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Active')");
        $stmt->bind_param("sssssssss", $username, $firstname, $lastname, $email, $con_num, $hashedPassword, $acctype, $schlvl, $brgy);

        // Execute the prepared statement
        if ($stmt->execute()) {
            // Data insertion successful
            echo "<script>alert('Account created successfully.');</script>";

            // Get the inserted user's ID from the database
            $idNo = $stmt->insert_id;

            // Set up session data for future login
            $_SESSION['acctype'] = $acctype;
            $_SESSION['id_no'] = $idNo;
            $_SESSION['username'] = $username;

            // Generate the QR code content
            $qrCodeContent = "ID Number: $idNo\nUsername: $username\nAccount Type: $acctype";

            // Generate the QR code image
            include_once('D:/xampp/htdocs/LibMS/resources/phpqrcode-master/phpqrcode-master/qrlib.php');

            // Generate the QR code
            ob_start(); // Start output buffering
            QRcode::png($qrCodeContent, null, QR_ECLEVEL_L, 10); // Output the image directly to the buffer
            $qrCodeImageData = ob_get_clean(); // Get the image data from the buffer

            // Determine the QR code image type
            $qrCodeImageType = "image/png";

            // Insert the QR code data into the database
            $insertQRCodeStmt = $conn->prepare("INSERT INTO qr_codes (user_id, username, qr_code_data, qr_code_type) VALUES (?, ?, ?, ?)");
            $insertQRCodeStmt->bind_param("isss", $idNo, $username, $qrCodeImageData, $qrCodeImageType);

            if ($insertQRCodeStmt->execute()) {
                // QR code insertion successful
            } else {
                // Error occurred while inserting QR code
                echo "<script>alert('Error inserting QR code: " . $insertQRCodeStmt->error . "');</script>";
            }

            $insertQRCodeStmt->close();
            
            //header('Location: /LibMS/func/acc_creation_success.php');

            // Redirect to appropriate page based on the user's account type
            if ($acctype === 'Student') {
                header('Location: /LibMS/users/student/index.php');
                exit();
            } elseif ($acctype === 'Guest') {
                // Redirect the user to the student portal after successful registration
                header('Location: guest-page.php');
                exit();
            }
        } else {
            // Error occurred while inserting data
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }

        // Close the prepared statement
        $stmt->close();
    }
}

//}

// Close the database connection
$conn->close();
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - MyLibro </title>
    <!--Link for Tab ICON-->
    <link rel="icon" type="image/x-icon" href="/LibMS/resources/images/logov1.png">
    <!--Link for Bootstrap-->
    <link rel="stylesheet" type="text/css" href="/LibMS/resources/bootstrap/css/bootstrap.min.css"/>
    <script type="text/javascript" src="/LibMS/resources/bootstrap/js/bootstrap.min.js"></script>
    <!--Link for CSS File-->
    <link rel="stylesheet" type="text/css" href="/LibMS/main/css/signup.css">
    <!--Link for Font Awesome Icons-->
    <link rel="stylesheet" href="/LibMS/resources/icons/fontawesome-free-6.4.0-web/css/all.css">
    <!--Link for Google Font-->
    <link rel="stylesheet" href="/LibMS/resources/fonts/fonts.css"/>

</head>

<body>

    <div class="signup-box">
        <div class="container-fluid">
            <div class="container">
                <div class="row">
                    <table class="signup-table">
                        <thead></thead>
                        <tbody>
                            <tr>
                                <td class="signup-td">
                                    <div class="container-fluid">
                                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="signup-form">
                                            <label for="firstname">Firstname:</label>
                                            <div class="form-group">
                                                <input type="text" name="firstname" id="firstname" required="">
                                            </div>

                                            <label for="lastname">Lastname:</label>
                                            <div class="form-group">
                                                <input type="text" name="lastname" id="lastname" required="">
                                            </div>

                                            <label for="username">Username:</label>
                                            <div class="form-group">
                                                <input type="text" name="username" id="username" required="">
                                            </div>
                                            
                                            <label for="email">Email Address:</label>
                                            <div class="form-group">
                                                <input type="text" name="email" id="email" required="">
                                            </div>

                                            <label for="con_num">Contact Number:</label>
                                            <div class="form-group">
                                                <input type="text" name="con_num" id="con_num" required="">
                                            </div>

                                            <label for="password">Password:</label>
                                            <div class="form-group">
                                                <input type="password" name="password" id="password" required="">
                                            </div>

                                        
                                    </div>
                                </td>

                                <td class="box-2">
                                    <div class="container">
                                        <div class="form-2-box">
                                            
                                                <label for="pasigresd" class="label-1"><b>Are you a Pasig Resident?</b></label>
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                            <ul class="check-box">
                                                                <li>
                                                                    <input type="checkbox" name="pasigresd" id="pasigresd" value="pasigresident"> Yes, I am a Pasig Resident.
                                                                </li>

                                                                <li>
                                                                    <input type="checkbox" name="notpasigresd" id="notpasigresd" value="notpasigresident"> No, I'm not a Pasig Resident.
                                                                </li>
                                                            </ul>
                                                        </label>
                                                    </div>

                                                <div id="barangayOptions" style="display: none;">
                                                    <label for="brgy">Barangay: </label>
                                                        <select name="brgy" class="form-select" id="brgy" style="margin-bottom:10px;">
                                                            <option selected disabled>Select a Barangay</option>
                                                                <option value="Bagong Ilog">Bagong Ilog</option>
                                                                <option value="Bagong Katipunan">Bagong Katipunan</option>
                                                                <option value="Bambang">Bambang</option>
                                                                <option value="Buting">Buting</option>
                                                                <option value="Caniogan">Caniogan</option>
                                                                <option value="Dela Paz">Dela Paz</option>
                                                                <option value="Kalawaan">Kalawaan</option>
                                                                <option value="Kapasigan">Kapasigan</option>
                                                                <option value="Kapitolyo">Kapitolyo</option>
                                                                <option value="Malinao">Malinao</option>
                                                                <option value="Manggahan">Manggahan</option>
                                                                <option value="Maybunga">Maybunga</option>
                                                                <option value="Orando">Orando</option>
                                                                <option value="Palatiw">Palatiw</option>
                                                                <option value="Pinagbuhatan">Pinagbuhatan</option>
                                                                <option value="Pineda">Pineda</option>
                                                                <option value="Rosario">Rosario</option>
                                                                <option value="Sagad">Sagad</option>
                                                                <option value="San Antonio">San Antonio</option>
                                                                <option value="San Joaquin">San Joaquin</option>
                                                                <option value="San Jose">San Jose</option>
                                                                <option value="San Miguel">San Miguel</option>
                                                                <option value="San Nicolas">San Nicolas</option>
                                                                <option value="Santa Cruz">Santa Cruz</option>
                                                                <option value="Santa Lucia">Santa Lucia</option>
                                                                <option value="Santa Rosa">Santa Rosa</option>
                                                                <option value="Santo Tomas">Santo Tomas</option>
                                                                <option value="Santolan">Santolan</option>
                                                                <option value="Sumilang">Sumilang</option>
                                                                <option value="Ugong">Ugong</option>
                                                        </select>
                                                </div>

                                                <div id="nonPasigResidentOptions" style="display: none;">
                                                    <label for="brgy-2">*Non-Pasig Residents* Enter your Barangay and City:</label>
                                                    <div class="form-group brgy-2">
                                                        <input type="text" name="nonPasigbrgy" id="nonPasigbrgy" placeholder="Follow this Format: Barangay, City">
                                                    </div>
                                                    <br>
                                                </div>

                                                
                                                    <label for="acctype">Account Type: </label>
                                                        <select name="acctype" class="form-select" id="acctype">
                                                            <option selected disabled>Select Account Type</option>
                                                            <option value="Student">Student</option>
                                                            <!--<option value="Guest">Guest</option>-->
                                                        </select>
                                                        <br>
                                                
                                                
                                                    <label for="schlvl">School Level: </label>
                                                        <select name="schlvl" class="form-select" id="schlvl">
                                                            <option selected disabled>Select School Level</option>
                                                            <option value="Elementary">Elementary</option>
                                                            <option value="Junior High School">Jr. High School</option>
                                                            <option value="Senior High School">Sr. High School</option>
                                                            <option value="College">College</option>
                                                            <option value="Graduate">Graduate</option>
                                                        </select>
                                                        <br>

                                                    <label for="termscondition" style="font-size:10px;">By Checking this, You Agree on our <a href="/LibMS/main/termsandconditions.php" style="text-decoration:none; font-style:italic;">Terms and Conditions/Policy Agreements.</a></label>
                                                    <div class="form-group" style='font-size:11px; font-weight:700; margin-top:5px;'>
                                                        <input type="checkbox" name="agree_terms" id="agree_terms" value="agree_terms" required=""> Yes, I Agree to the Terms and Conditions.
                                                    </div>
                                                    <br>
                                                


                                                    <div class="btn-1">
                                                        <button type="submit" class="btn btn-primary btn-sm">
                                                            <i class="fa-solid fa-right-to-bracket"></i> Submit
                                                        </button>
                                                    </div>
                                                    <br>

                                                    <div class="btn-2">
                                                        <a href="/LibMS/index.php">
                                                            <button type="button" class="btn btn-primary btn-sm">
                                                                <i class="fa-solid fa-rotate-left fa-sm"></i> Go Back
                                                            </button>
                                                    </div>
                                            </form><br>
                                        </div>

                                        <script>
                                            const pasigResidentCheckbox = document.getElementById('pasigresd');
                                            const notPasigResidentCheckbox = document.getElementById('notpasigresd');
                                            const barangayOptions = document.getElementById('barangayOptions');
                                            const nonPasigResidentOptions = document.getElementById('nonPasigResidentOptions');

                                            pasigResidentCheckbox.addEventListener('change', function () {
                                                if (pasigResidentCheckbox.checked) {
                                                    barangayOptions.style.display = 'block';
                                                    nonPasigResidentOptions.style.display = 'none';
                                                    notPasigResidentCheckbox.disabled = true;
                                                } else {
                                                    barangayOptions.style.display = 'none';
                                                    notPasigResidentCheckbox.disabled = false;
                                                }
                                            });

                                            notPasigResidentCheckbox.addEventListener('change', function () {
                                                if (notPasigResidentCheckbox.checked) {
                                                    nonPasigResidentOptions.style.display = 'block';
                                                    barangayOptions.style.display = 'none';
                                                    pasigResidentCheckbox.disabled = true;
                                                } else{
                                                    nonPasigResidentOptions.style.display = 'none';
                                                    notPasigResidentCheckbox.disabled = false;
                                                    pasigResidentCheckbox.disabled = false;
                                                }
                                            });


                                            /*

                                            const studentCheckbox = document.getElementById('student');
                                            const notStudentCheckbox = document.getElementById('guest');
                                            const schoolLevel = document.getElementById('schoolLevel');

                                            studentCheckbox.addEventListener('change', function () {
                                                if (studentCheckbox.checked) {
                                                    schoolLevel.style.display = 'block';
                                                    sql = 'INSERT INTO users(acctype) VALUES ('Student')';
                                                } else {
                                                    schoolLevel.style.display = 'none';
                                                }
                                            });

                                            notStudentCheckbox.addEventListener('change', function () {
                                                if (notStudentCheckbox.checked) {
                                                    schoolLevel.style.display = 'none';
                                                }
                                            });*/

                                            
                                    </script>

                                    <script>
                                        
                                        var acctype = document.getElementById("acctype");
                                                var schlvl = document.getElementById("schlvl");

                                                acctype.addEventListener("change", function() {
                                                if (acctype.value === "Guest") {
                                                    schlvl.disabled = true;
                                                    schlvl.selectedIndex = 0; // clears the selection of the schlvl dropdown
                                                } else {
                                                    schlvl.disabled = false;
                                                }
                                                });

                                    </script>

                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="container-fluid">
                                        <h3><b>REMINDER!</b></h3>
                                        <p>Please Always Remember your Account's ID Number.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>
</html>