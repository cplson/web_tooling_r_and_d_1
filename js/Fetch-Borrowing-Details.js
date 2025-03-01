
const borrowingDetailsElement = document.getElementById("borrowingDetails");
$(document).ready(function() {
    window.editBorrow = function(id) {
        // console.log('here: ', id)
        borrowingDetailsElement.innerHTML = "<p class=\"text-primary\">Loading...</p>";
 
        console.log("Fetching rental details for ID:", id) // Debugging
        $.ajax({
            url: "./borrowings/edit-borrowing.php",
            type: "GET",
            data: {
                id: id
            },
            success: function(response) {
                // console.log("server responded", response);
                borrowingDetailsElement.innerHTML = response;
            },
            error: function(xhr, status, error) {
                // console.log("server errored:", error);
                borrowingDetailsElement.innerHTML = "<p class=\"text-danger\">Error: see console...</p>";
            }
        });
    };
});