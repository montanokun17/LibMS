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

// Check if the content is set in the POST request
if (isset($_POST['content'])) {
    $qrCodeContent = $_POST['content'];

        // Assuming the data received is in JSON format, decode it
        $data = json_decode($qrCodeContent, true);

        // Insert data into the database
        $insertQuery = "INSERT INTO qr_attendance (user_id, username, attendance_time_in, acctype, status) VALUES ('{$data['user_id']}', '{$data['username']}', NOW(), '{$data['acctype']}', 0)";
        $conn->prepare($query);
        error_log("Decoded Data: " . print_r($data, true));

        if ($conn->query($insertQuery)) {
            echo "Data inserted successfully. Time IN";
        } else {
            echo "Error inserting data: " . $conn->error;
        }
    }
?>
