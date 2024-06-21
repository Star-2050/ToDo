$(document).ready(function () {
    var currentListID = 3; // Default list ID

    function loadTodoLists() {
        $.ajax({
            url: "PHP/getToDoLists.php",
            method: "POST",
            success: function (data) {
                console.log("Todo lists loaded:", data);
                $("#todoList-container").html(data);
            },
            error: function (xhr, status, error) {
                console.error("Error fetching todo lists:", xhr.responseText);
                alert("An error occurred while fetching the to-dos.");
            }
        });
    }

    function setFilter(filter) {
        $.ajax({
            url: "PHP/setFilter.php",
            method: "POST",
            data: { filter: filter },
            success: function (data) {
                console.log("Filter set response:", data);
                loadTodos(currentListID);
            },
            error: function (xhr, status, error) {
                console.error("Error setting filter:", xhr.responseText);
                alert("An error occurred while setting the filter.");
            }
        });
    }

    function setListID(listID) {
        $.ajax({
            url: "PHP/setListID.php",
            method: "POST",
            data: { listID: listID },
            success: function (data) {
                console.log("ListID set response:", data);
                currentListID = listID; // Update the current list ID
                loadTodos(currentListID);
            },
            error: function (xhr, status, error) {
                console.error("Error setting listID:", xhr.responseText);
                alert("An error occurred while setting the listID.");
            }
        });
    }

    function loadTodos(listID = currentListID) {
        $.ajax({
            url: "PHP/get_todos.php",
            method: "POST",
            data: { listID: listID },
            success: function (data) {
                console.log("Todos loaded:", data);
                $("#todo-container").html(data);
            },
            error: function (xhr, status, error) {
                console.error("Error fetching todos:", xhr.responseText);
                alert("An error occurred while fetching the to-dos.");
            }
        });
    }

    // Event listener for delete icon
    $(document).on('click', '.delete-icon', function () {
        var task = $(this).data('task');
        deleteTodo(task);
    });

    function deleteTodo(task) {
        $.ajax({
            url: "PHP/delete_todo.php",
            method: "POST",
            data: { task: task },
            success: function (data) {
                console.log("Todo deleted:", data);
                loadTodos(currentListID);
            },
            error: function (xhr, status, error) {
                console.error("Error deleting todo:", xhr.responseText);
                alert("An error occurred while deleting the to-do.");
            }
        });
    }

    // Load todos for "Heute" on page load
    loadTodos();

    // Event listeners for filter buttons
    $('#btnToday').click(function () {
        setFilter(1);
    });

    $('#btnUpcoming').click(function () {
        setFilter(2);
        console.log("Demnächst");
    });

    $('#btnAll').click(function () {
        setFilter(3);
    });

    // Event listener for project buttons
    $(document).on('click', '.project-button', function () {
        var listID = $(this).data('listid');
        setListID(listID); // Set the list ID in the session
    });

    // Load the todo lists
    loadTodoLists();

    // Set listID in the add todo form when showing the form
    $('#addButton').click(function () {
        $('#newTodoForm input[name="listID"]').val(currentListID);
        $('#newTodoForm').show();
    });

    $('#cancelButton').click(function () {
        $('#newTodoForm').hide();
    });

    // Show new list form
    $('#addListButton').click(function () {
        $('#newListForm').show();
    });

    // Hide new list form
    $('#cancelListButton').click(function () {
        $('#newListForm').hide();
    });

    // Dropdown functionality
    $('.dropbtn').click(function () {
        $('.dropdown-content').toggle();
    });

    // Account deletion confirmation modal
    var modal = $('#deleteAccountModal');
    var span = $('.close');

    $('#deleteAccount').click(function () {
        modal.show();
    });

    span.click(function () {
        modal.hide();
    });

    $('#cancelDeleteAccount').click(function () {
        modal.hide();
    });

    $('#confirmDeleteAccount').click(function () {
        // Execute delete_user.php on confirm
        $.ajax({
            url: "PHP/delete_user.php",
            method: "POST",
            success: function (data) {
                console.log("Account deleted:", data);
                modal.hide();
            },
            error: function (xhr, status, error) {
                console.error("Error deleting account:", xhr.responseText);
                alert("An error occurred while deleting the account.");
            }
        });
    });

    // Hide modal when clicking outside of it
    $(window).click(function (event) {
        if (event.target == modal[0]) {
            modal.hide();
        }
    });

    // Logout functionality
    $('#logout').click(function () {
        window.location.href = 'PHP/logout.php';
    });

    
});
