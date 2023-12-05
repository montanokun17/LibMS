function filterBooks(section) {
    // Make an AJAX request based on the status clicked
    $.ajax({
      url: "/LibMS/users/student/books/filter.php", // Replace with your backend file to handle fetching books by section
      type: "GET",
      data: { status: section }, // Send the section to the backend
      success: function(data) {
        // Update the book table container with the fetched data
        document.getElementById("dataTable").innerHTML = data;
      },
      error: function() {
        document.getElementById("dataTable").innerHTML = "Failed to retrieve books";
      }
    });
}