<?php
session_start();
include 'functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if (isset($_POST['title']) && isset($_POST['description']) && isset($_POST['date']))
    {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $date = $_POST['date'];
        $userID = $_SESSION['userID'];
        $listID = $_SESSION['listID'];

        addNewTodoItem($userID, $listID, $title, $description, $date);
        echo "To-Do item added successfully.";
    }
    else
    {
        echo "Title, description, and date are required.";
    }
}

function addNewTodoItem($userID, $listID, $title, $description, $date)
{
    $connection = Connect();
    $stmt = mysqli_prepare($connection, "INSERT INTO ToDos (UserID, ListID, Task, Beschreibung, Datum) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'iisss', $userID, $listID, $title, $description, $date);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
    header('Location: ../ToDoPlus.html');
    exit();
}
