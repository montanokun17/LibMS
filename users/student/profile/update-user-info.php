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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $firstname = $conn->real_escape_string($_POST["firstname"]);
    $lastname = $conn->real_escape_string($_POST["lastname"]);
    $username = $conn->real_escape_string($_POST["username"]);
    $email = $conn->real_escape_string($_POST["email"]);
    $con_num = $conn->real_escape_string($_POST["con_num"]);
    $brgy = $conn->real_escape_string($_POST["brgy"]);

    // Assuming you have a user_id for the user you want to update
    $user_id = 1; // Replace with the actual user_id

    // Update the user data in the database
    $sql = "UPDATE users SET firstname='$firstname', lastname='$lastname', username='$username', email='$email', con_num='$con_num', brgy='$brgy' WHERE id_no= $idNo";

    if ($conn->query($sql) === TRUE) {
        // Send a success response
        $response = "User data updated successfully!";
        echo $response;
    } else {
        // Send an error response
        http_response_code(500); // Internal Server Error
        echo "Error updating user data: " . $conn->error;
    }
} else {
    // Invalid request
    http_response_code(400);
    echo "Bad Request";
}

// Close the database connection
$conn->close();


?>