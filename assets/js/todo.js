$(document).ready(function () {
    function loadTodos() {
        $.ajax({
            url: "PHP/get_todos.php",
            method: "POST",
            success: function (data) {
                $("#todo-container").html(data);
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
                alert("An error occurred while fetching the to-dos.");
            }
        });
    }

    // Load todos on page load
    loadTodos();

    document.getElementById('addButton').addEventListener('click', function () {
        document.getElementById('newTodoForm').style.display = 'block';
    });

    document.getElementById('addNewTodoButton').addEventListener('click', function () {
        const title = document.getElementById('newTodoTitle').value;
        const description = document.getElementById('newTodoDescription').value;
        const date = document.getElementById('newTodoDate').value;

        if (title && description && date) {
            $.ajax({
                url: "../PHP/add_todo.php",
                method: "POST",
                data: {
                    title: title,
                    description: description,
                    date: date
                },
                success: function (data) {
                    loadTodos();
                    document.getElementById('newTodoTitle').value = '';
                    document.getElementById('newTodoDescription').value = '';
                    document.getElementById('newTodoDate').value = '';
                    document.getElementById('newTodoForm').style.display = 'none';
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                    alert("An error occurred while adding the to-do.");
                }
            });
        } else {
            alert('Bitte Titel, Beschreibung und Datum eingeben.');
        }
    });

    function cancelNewTodo() {
        document.getElementById('newTodoTitle').value = '';
        document.getElementById('newTodoDescription').value = '';
        document.getElementById('newTodoDate').value = '';
        document.getElementById('newTodoForm').style.display = 'none';
    }

    window.cancelNewTodo = cancelNewTodo; // Expose the function to the global scope
});
