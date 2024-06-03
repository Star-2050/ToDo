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
        $listID = $_SESSION['listID'] ?? 1; // Beispielhafte ListID, falls nicht in der Session vorhanden

        if (addNewTodoItem($listID, $title, $description, $date))
        {
            header('Location: ../ToDoPlus.html');
            exit();
        }
        else
        {
            header('Location: ../ToDoPlus.html');
            exit();
        }
    }
    else
    {
        header('Location: ../ToDoPlus.html');
        exit();
    }
}

function addNewTodoItem($listID, $title, $description, $date)
{
    $connection = Connect();
    $stmt = mysqli_prepare($connection, "INSERT INTO ToDos (ListID, Task, Beschreibung, Datum) VALUES (?, ?, ?, ?)");
    if ($stmt === false)
    {
        die('Prepare failed: ' . htmlspecialchars($connection->error));
    }
    mysqli_stmt_bind_param($stmt, 'isss', $listID, $title, $description, $date);
    $result = mysqli_stmt_execute($stmt);
    if ($result === false)
    {
        die('Execute failed: ' . htmlspecialchars(mysqli_stmt_error($stmt)));
    }
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
    return $result;
}