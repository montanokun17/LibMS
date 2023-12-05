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

if (isset($_POST['query'])) {
    $searchQuery = $_POST['query'];

    // Construct your database query based on the $searchQuery
    $sql = "SELECT *
        FROM books
        WHERE book_title LIKE '%$searchQuery%'
           OR author LIKE '%$searchQuery%'
           OR year LIKE '%$searchQuery%'
           OR publisher LIKE '%$searchQuery%'
           OR section LIKE '%$searchQuery%' ORDER BY book_id DESC
           ";

    // Execute the query and fetch results
    $result = $conn->query($sql);

    // Create an array to store the search results
    $results = array();

    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }

    // Return the search results as JSON
    echo json_encode($results);
} else {
    echo 'No search query provided.';
}
?>