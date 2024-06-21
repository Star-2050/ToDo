function loadRequests() {
    $.get('PHP/get_share_requests.php', function (data) {
        $('#requests-container').empty();
        $('#requests-container').html(data);
    }).fail(function (xhr, status, error) {
        console.error("Error loading requests:", xhr.responseText);
        alert("An error occurred while loading requests.");
    });
}

// Handle request actions
$(document).on('click', '.accept-request', function () {
    var requestID = $(this).data('id');
    $.post('PHP/handle_share_request.php', { requestID: requestID, action: 'accept' }, function (response) {
        alert(response);
        loadRequests();
    }).fail(function (xhr, status, error) {
        console.error("Error handling request:", xhr.responseText);
        alert("An error occurred while handling the request.");
    });
});

$(document).on('click', '.reject-request', function () {
    var requestID = $(this).data('id');
    $.post('PHP/handle_share_request.php', { requestID: requestID, action: 'reject' }, function (response) {
        alert(response);
        loadRequests();
    }).fail(function (xhr, status, error) {
        console.error("Error handling request:", xhr.responseText);
        alert("An error occurred while handling the request.");
    });
});

// Initial load
loadRequests();



// Load lists into the share form dropdown
function loadLists() {
    $.get('PHP/get_lists.php', function (data) {
        $('#shareListID').empty();
        $('#shareListID').html(data);
    }).fail(function (xhr, status, error) {
        console.error("Error loading lists:", xhr.responseText);
        alert("An error occurred while loading lists.");
    });
}

// Show the share list form
$('#shareListButton').click(function () {
    $('#newListForm').hide();
    $('#shareListForm').show();
    loadLists();
});

// Cancel sharing
$('#cancelShareButton').click(function () {
    $('#shareListForm').hide();
});
