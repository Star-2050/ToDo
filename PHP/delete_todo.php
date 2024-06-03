<?php
session_start();
include 'functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if (!isset($_SESSION['userID']))
    {
        header('Location: ../Log-in.html');
        exit();
    }

    $userID = $_SESSION['userID'];
    $listID = $_SESSION['listID'];
    $task = $_POST['task'];

    if (deleteTodoItem($userID, $listID, $task))
    {
        echo "To-Do item deleted successfully.";
    }
    else
    {
        echo "An error occurred.";
    }
}

function deleteTodoItem($userID, $listID, $task)
{
    $connection = Connect();
    $stmt = mysqli_prepare($connection, "DELETE td FROM ToDos td INNER JOIN UserToDoLists utl ON td.ListID = utl.ListID WHERE utl.UserID = ? AND td.ListID = ? AND td.Task = ?");
    mysqli_stmt_bind_param($stmt, 'iis', $userID, $listID, $task);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
    return $result;
}