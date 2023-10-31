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



                                    require_once('D:/xampp/htdocs/LibMS/resources/TCPDF-main/tcpdf.php'); // Include the TCPDF library

                                    
                                    // Fetch data from the database, excluding "deleted" books
                                    $PDFquery = "SELECT * FROM books WHERE deleted = 0";
                                    $result = mysqli_query($conn, $PDFquery);

                                    // Create a new PDF document
                                    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                                    $pdf->SetCreator($username);
                                    $pdf->SetAuthor($firstname . ' ' . $lastname);
                                    $pdf->SetTitle('Books Database Report');
                                    $pdf->SetMargins(10, 10, 10);
                                    $pdf->AddPage();

                                    // Define the header for the report
                                    $heading = 'Books Database Report';
                                    $totalBooks = mysqli_num_rows($result);
                                    $goodBooks = $damagedBooks = $dilapidatedBooks = $lostBooks = 0;
                                    date_default_timezone_set('Asia/Manila');
                                    $timestamp = time();
                                    $date = date('Y-m-d H:i:s', $timestamp); // Formats the timestamp as "YYYY-MM-DD HH:MM:SS"


                                    // Loop through the database results and generate a table
                                    $html = '<p style="font-size:6px; margin-top:5px;"><b>MyLibro - Virtual Library Management</b> - Author: <i>' . $username . '/'. $idNo . '</i></p>';
                                    $html .= '<p style="font-size:5px;"><b><i>Report Generated as of: ' . $date . '</i></b></p>';
                                    $html .= '<hr>';
                                    $html .= '<h2>' . $heading . '</h2>';
                                    $html .= '<p style="font-size:8px;">Total Number of Current Books: ' . $totalBooks . '</p>';
                                    $html .= '<table border="1" style="font-size:8px;">
                                    <tr style="background-color:#B8FF69;">
                                    <th>Dewey Decimal</th>
                                    <th>Section</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Year</th>
                                    <th>Volume</th>
                                    <th>Stocks</th>
                                    <th>Status</th>
                                    </tr>';

                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $html .= '<tr>
                                        <td>' . $row['dewey'] . '</td>
                                        <td>' . $row['section'] . '</td>
                                        <td>' . $row['book_title'] . '</td>
                                        <td>' . $row['author'] . '</td>
                                        <td>' . $row['year'] . '</td>
                                        <td>' . $row['volume'] . '</td>
                                        <td>' . $row['stocks'] . '</td>
                                        <td>' . $row['status'] . '</td>
                                        </tr>';
                                        
                                        // Count books by status
                                        switch ($row['status']) {
                                            case 'Good':
                                                $goodBooks++;
                                                break;
                                            case 'Damaged':
                                                $damagedBooks++;
                                                break;
                                            case 'Dilapidated':
                                                $dilapidatedBooks++;
                                                break;
                                            case 'Lost':
                                                $lostBooks++;
                                                break;
                                        }
                                    }

                                    $html .= '</table>';
                                    $pdf->writeHTML($html, true, false, true, false, '');

                                    $pdf->Output('books_report.pdf', 'D'); // Output the PDF as a download

                                    // Close the database connection
                                    mysqli_close($connection);
                                   
?>