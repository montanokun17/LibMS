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



require 'D:\xampp\htdocs\LibMS\resources\PhPOffice\vendor\autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

// Fetch data from the database, excluding "deleted" books
$XLSXquery = "SELECT * FROM books WHERE deleted = 0";
$result = mysqli_query($conn, $XLSXquery);

// Create a new Excel spreadsheet
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();
$worksheet->setTitle('Books Database Report');

// Define the header for the report
$heading = 'Books Database Report';
$totalBooks = mysqli_num_rows($result);
$goodBooks = $damagedBooks = $dilapidatedBooks = $lostBooks = 0;
date_default_timezone_set('Asia/Manila');
$timestamp = time();
$date = date('Y-m-d H:i:s', $timestamp);

$worksheet->getCell('A1')->setValue('MyLibro - Virtual Library Management - Author: ' . $username . '/' . $idNo);
$worksheet->getCell('A2')->setValue('Report Generated as of: ' . $date);
$worksheet->mergeCells('A1:E1');
$worksheet->mergeCells('A2:E2');
$worksheet->getCell('A3')->setValue($heading);
$worksheet->getCell('A4')->setValue('Total Number of Current Books: ' . $totalBooks);

$worksheet->getStyle('A1:E4')->getFont()->setSize(8);
$worksheet->getStyle('A1:E2')->getFont()->setBold(true);
$worksheet->getStyle('A1:E2')->getAlignment()->setWrapText(true);

$worksheet->getCell('A6')->setValue('Dewey Decimal');
$worksheet->getCell('B6')->setValue('Section');
$worksheet->getCell('C6')->setValue('Title');
$worksheet->getCell('D6')->setValue('Author');
$worksheet->getCell('E6')->setValue('Publisher');
$worksheet->getCell('F6')->setValue('Year');
$worksheet->getCell('G6')->setValue('Volume');
$worksheet->getCell('H6')->setValue('Edition');
$worksheet->getCell('I6')->setValue('Status');

$rowNumber = 7;

while ($row = mysqli_fetch_assoc($result)) {
    $worksheet->getCell('A' . $rowNumber)->setValue($row['dewey']);
    $worksheet->getCell('B' . $rowNumber)->setValue($row['section']);
    $worksheet->getCell('C' . $rowNumber)->setValue($row['book_title']);
    $worksheet->getCell('D' . $rowNumber)->setValue($row['author']);
    $worksheet->getCell('E' . $rowNumber)->setValue($row['publisher']);
    $worksheet->getCell('F' . $rowNumber)->setValue($row['year']);
    $worksheet->getCell('G' . $rowNumber)->setValue($row['volume']);
    $worksheet->getCell('H' . $rowNumber)->setValue($row['edition']);
    $worksheet->getCell('I' . $rowNumber)->setValue($row['status']);

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

    $rowNumber++;
}

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$filename = 'books_report_' . str_replace(":", "-", $date) . '.xlsx';

// Save the file to a temporary location
$tempFilePath = sys_get_temp_dir() . '/' . $filename;
$writer->save($tempFilePath);

// Set the HTTP headers for downloading the file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Output the file to the browser
readfile($tempFilePath);

// Clean up the temporary file
unlink($tempFilePath);

mysqli_close($conn);


/*

// Fetch data from the database, excluding "deleted" books
$XLSXquery = "SELECT * FROM books WHERE deleted = 0";
$result = mysqli_query($conn, $XLSXquery);

// Create a new Excel spreadsheet
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();
$worksheet->setTitle('Books Database Report');

// Define the header for the report
$heading = 'Books Database Report';
$totalBooks = mysqli_num_rows($result);
$goodBooks = $damagedBooks = $dilapidatedBooks = $lostBooks = 0;
date_default_timezone_set('Asia/Manila');
$timestamp = time();
$date = date('Y-m-d H:i:s', $timestamp); // Formats the timestamp as "YYYY-MM-DD HH:MM:SS"

$worksheet->getCell('A1')->setValue('MyLibro - Virtual Library Management - Author: ' . $username . '/' . $idNo);
$worksheet->getCell('A2')->setValue('Report Generated as of: ' . $date);
$worksheet->mergeCells('A1:E1');
$worksheet->mergeCells('A2:E2');
$worksheet->getCell('A3')->setValue($heading);
$worksheet->getCell('A4')->setValue('Total Number of Current Books: ' . $totalBooks);

$worksheet->getStyle('A1:E4')->getFont()->setSize(8);
$worksheet->getStyle('A1:E2')->getFont()->setBold(true);
$worksheet->getStyle('A1:E2')->getAlignment()->setWrapText(true);

$worksheet->getCell('A6')->setValue('Dewey Decimal');
$worksheet->getCell('B6')->setValue('Section');
$worksheet->getCell('C6')->setValue('Title');
$worksheet->getCell('D6')->setValue('Author');
$worksheet->getCell('E6')->setValue('Publisher');
$worksheet->getCell('F6')->setValue('Year');
$worksheet->getCell('G6')->setValue('Volume');
$worksheet->getCell('H6')->setValue('Edition');
$worksheet->getCell('I6')->setValue('Status');

$rowNumber = 7;

while ($row = mysqli_fetch_assoc($result)) {
    $worksheet->getCell('A' . $rowNumber)->setValue($row['dewey']);
    $worksheet->getCell('B' . $rowNumber)->setValue($row['section']);
    $worksheet->getCell('C' . $rowNumber)->setValue($row['book_title']);
    $worksheet->getCell('D' . $rowNumber)->setValue($row['author']);
    $worksheet->getCell('E' . $rowNumber)->setValue($row['publisher']);
    $worksheet->getCell('F' . $rowNumber)->setValue($row['year']);
    $worksheet->getCell('G' . $rowNumber)->setValue($row['volume']);
    $worksheet->getCell('H' . $rowNumber)->setValue($row['edition']);
    $worksheet->getCell('I' . $rowNumber)->setValue($row['status']);

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

    $rowNumber++;
}

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$filename = 'books_report_' . $date . '.xlsx';
$writer->save('php://output'); // Send the Excel file to the user's browser

// Define the content-disposition header to trigger the file download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');

mysqli_close($conn);


require_once 'D:/xampp/htdocs/PHPExcel/PHPExcel.php'; // Include the PHPExcel library

// Create a new PHPExcel object
$objPHPExcel = new PHPExcel();

// Fetch data from the database, excluding "deleted" books
$PDFquery = "SELECT * FROM books WHERE deleted = 0";
$result = mysqli_query($conn, $PDFquery);

// Create a new PHPExcel worksheet
$objPHPExcel->setActiveSheetIndex(0);
$worksheet = $objPHPExcel->getActiveSheet();

// Define the header for the report
$heading = 'Books Database Report';
$totalBooks = mysqli_num_rows($result);
$goodBooks = $damagedBooks = $dilapidatedBooks = $lostBooks = 0;
date_default_timezone_set('Asia/Manila');
$timestamp = time();
$date = date('Y-m-d H:i:s', $timestamp); // Formats the timestamp as "YYYY-MM-DD HH:MM:SS"

$worksheet->setCellValue('A1', 'MyLibro - Virtual Library Management - Author: ' . $username . '/' . $idNo);
$worksheet->setCellValue('A2', 'Report Generated as of: ' . $date);
$worksheet->setCellValue('A3', 'Books Database Report');
$worksheet->setCellValue('A4', 'Total Number of Current Books: ' . $totalBooks);

// Set column headers
$worksheet->setCellValue('A5', 'Dewey Decimal');
$worksheet->setCellValue('B5', 'Section');
$worksheet->setCellValue('C5', 'Title');
$worksheet->setCellValue('D5', 'Author');
$worksheet->setCellValue('E5', 'Year');
$worksheet->setCellValue('F5', 'Volume');
$worksheet->setCellValue('G5', 'Stocks');
$worksheet->setCellValue('H5', 'Status');

$rowNumber = 6;
while ($row = mysqli_fetch_assoc($result)) {
    $worksheet->setCellValue('A' . $rowNumber, $row['dewey']);
    $worksheet->setCellValue('B' . $rowNumber, $row['section']);
    $worksheet->setCellValue('C' . $rowNumber, $row['book_title']);
    $worksheet->setCellValue('D' . $rowNumber, $row['author']);
    $worksheet->setCellValue('E' . $rowNumber, $row['year']);
    $worksheet->setCellValue('F' . $rowNumber, $row['volume']);
    $worksheet->setCellValue('G' . $rowNumber, $row['stocks']);
    $worksheet->setCellValue('H' . $rowNumber, $row['status']);

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

    $rowNumber++;
}

// Create a new Excel writer
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

// Save the Excel file
$objWriter->save('books_report.xlsx');

// Close the database connection
mysqli_close($conn);

*/
?>