$(document).ready(function () {
    const searchInput = $('#searchInput');
    const searchButton = $('#searchButton');
    const searchResultsTable = $('#dataTable tbody');
    const loadingSpinner = $('#loadingSpinner'); // Add a loading spinner element in your HTML

    searchButton.on('click', function () {
        const searchQuery = searchInput.val();
        performSearch(searchQuery);
    });

    function performSearch(query) {
        // Show loading spinner
        loadingSpinner.show();

        $.ajax({
            url: '/LibMS/users/student/books/search.php',
            method: 'POST',
            data: { query: query },
            success: function (data) {
                // Hide loading spinner
                loadingSpinner.hide();

                displaySearchResults(data);
            },
            error: function () {
                // Hide loading spinner in case of error
                loadingSpinner.hide();
                alert('An error occurred while fetching search results.');
            }
        });
    }

    function displaySearchResults(data) {
        searchResultsTable.empty();

        const results = JSON.parse(data);

        if (results.length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'No Results',
                text: 'No matching results were found.',
            });
            console.log('error');
        } else {
            results.forEach(function (result) {
                const newRow = `
                    <tr>
                        <td>${result.book_title}</td>
                        <td>${result.author}</td>
                        <td>${result.publisher}</td>
                        <td>${result.year}</td>
                        <td>${result.volume}</td>
                        <td>${result.edition}</td>
                        <td>${result.section}</td>
                        <td>
                            <a href="/LibMS/users/student/books/details.php?book_id=${result.book_id}">    
                                <button type="button" class="btn btn-success btn-sm"><i class="fa-solid fa-circle-info fa-sm"></i> Details</button>
                            </a>
                            <a href="/LibMS/users/student/requests/borrow/borrow.php?book_id=${result.book_id}">
                                <button type="button" class="btn btn-success btn-sm" style="margin-left:5px;"><i class="fa-solid fa-bookmark fa-sm"></i> Borrow</button>
                            </a>
                        </td>
                    </tr>
                `;
                searchResultsTable.append(newRow);
                console.log('success');
            });
        }
    }
});