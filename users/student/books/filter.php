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

if (isset($_GET['status'])) {
  $status = $_GET['status'];
  
  // Query the database based on the status
  $sql = "SELECT * FROM books WHERE section = '$status' ORDER BY book_id DESC";
  $result = mysqli_query($conn, $sql);
  
  if (mysqli_num_rows($result) > 0) {

    $output = "";

    while ($row = mysqli_fetch_assoc($result)) {
        $output .= '
        
        <tr>
        <td>' .$row['book_title'].'</td>
        <td>' .$row['author'].'</td>
        <td>' .$row['publisher'].'</td>
        <td>' .$row['year'].'</td>
        <td>' .$row['volume'].'</td>
        <td>' .$row['edition'].'</td>
        <td>' .$row['section'].'</td>
        <td>
            <a href="/LibMS/users/student/books/details.php?book_id=' .$row['book_id'].'">    
                <button type="button" class="btn btn-success btn-sm"><i class="fa-solid fa-circle-info fa-sm"></i> Details</button>
            </a>
            <a href="/LibMS/users/student/requests/borrow/borrow.php?book_id=' .$row['book_id'].'">
                <button type="button" class="btn btn-success btn-sm" style="margin-left:5px;"><i class="fa-solid fa-bookmark fa-sm"></i> Borrow</button>
            </a>
        </td>
    </tr>';

    }
    echo $output; // Output generated HTML
  } else {
    echo "No Sections available for $status";
  }
} else {
  echo "Section not provided";
}
?>