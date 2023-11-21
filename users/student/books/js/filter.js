
/*
$(document).ready(function () {
    // Fetch and display the initial book list
    filterBooks();

    // Add event listeners to the dropdowns
    $('#section-dd, #book-status, #dewey-classification, #book-avail').change(filterBooks);

    function filterBooks() {
        // Get selected values
        var selectedSection = $('#section-dd').val();
        var selectedStatus = $('#book-status').val();
        var selectedDewey = $('#dewey-classification').val();
        var selectedAvailability = $('#book-avail').val();

        // Send AJAX request to fetch filtered books
        $.ajax({
            url: '/LibMS/users/student/books/books_table.php',
            method: 'POST',
            data: {
                section: selectedSection,
                status: selectedStatus,
                dewey: selectedDewey,
                availability: selectedAvailability
            },
            success: function (response) {
                // Update the book list container with the filtered books
                $('#book-list-container').html(response);
            }
        });
    }
});


$(document).ready(function () {
    // Function to handle AJAX requests
    function updateBookList() {
        var selectedSection = $("#section-dd").val();
        var selectedStatus = $("#book-status").val();
        var selectedDewey = $("#dewey-classification").val();
        var selectedAvailability = $("#book-avail").val();

        // Make an AJAX request to update the book list
        $.ajax({
            type: "POST",
            url: "/LibMS/users/student/books/books_table.php", // Replace with the actual PHP file path
            data: {
                section: selectedSection,
                status: selectedStatus,
                dewey: selectedDewey,
                availability: selectedAvailability,
            },
            success: function (response) {
                // Update the book list container with the new content
                $("#book-list-container").html(response);
            },
            error: function () {
                console.log("Error in AJAX request");
            },
        });
    }

    // Call the updateBookList function when a dropdown value changes
    $("#section-dd, #book-status, #dewey-classification, #book-avail").on("change", function () {
        updateBookList();
    });

    // Call the updateBookList function on page load
    updateBookList();
});
*/

