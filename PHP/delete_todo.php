<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if (!isset($_SESSION['userID']))
    {
        header('Location: ../Log-in.html');
        exit();
    }

    $userID = $_SESSION['userID'];
    $task = $_POST['task'];

    if (deleteTodoItem($userID, $task))
    {
        echo "Todo item deleted successfully.";
    }
    else
    {
        echo "Failed to delete todo item.";
    }
}

function Connect()
{
    $hostname = '89.58.47.144';
    $username = 'ToDoPlusUser';
    $password = 'todopluspw';
    $dbname = 'dbToDoPlus';

    $connection = mysqli_connect($hostname, $username, $password, $dbname);
    if (!$connection)
    {
        die("Verbindung fehlgeschlagen: " . mysqli_connect_error());
    }
    return $connection;
}

function deleteTodoItem($userID, $task)
{
    $connection = Connect();
    $sql = "DELETE td FROM ToDos td 
            INNER JOIN UserToDoLists utl ON td.ListID = utl.ListID 
            WHERE utl.UserID = ? AND td.Task = ?";
    $stmt = $connection->prepare($sql);
    if (!$stmt)
    {
        die("Prepare failed: (" . $connection->errno . ") " . $connection->error);
    }

    $stmt->bind_param("is", $userID, $task);
    if (!$stmt->execute())
    {
        return false;
    }

    $stmt->close();
    $connection->close();
    return true;
}
