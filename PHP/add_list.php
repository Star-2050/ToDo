<?php
//Oliver
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if (isset($_POST['listName']) && isset($_SESSION['userID']))
    {
        $listName = $_POST['listName'];
        $userID = $_SESSION['userID'];

        if (addNewList($userID, $listName))
        {
            header('Location: ../ToDoPlus.html');
            exit();
        }
        else
        {
            echo "Failed to add new list.";
        }
    }
    else
    {
        echo "Invalid request.";
    }
}

function addNewList($userID, $listName)
{
    $connection = Connect();
    $stmt = mysqli_prepare($connection, "INSERT INTO ToDoLists (ListName) VALUES (?)");
    if ($stmt === false)
    {
        die('Prepare failed: ' . htmlspecialchars($connection->error));
    }
    mysqli_stmt_bind_param($stmt, 's', $listName);
    $result = mysqli_stmt_execute($stmt);
    if ($result === false)
    {
        die('Execute failed: ' . htmlspecialchars(mysqli_stmt_error($stmt)));
    }
    $listID = mysqli_insert_id($connection);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($connection, "INSERT INTO UserToDoLists (UserID, ListID) VALUES (?, ?)");
    if ($stmt === false)
    {
        die('Prepare failed: ' . htmlspecialchars($connection->error));
    }
    mysqli_stmt_bind_param($stmt, 'ii', $userID, $listID);
    $result = mysqli_stmt_execute($stmt);
    if ($result === false)
    {
        die('Execute failed: ' . htmlspecialchars(mysqli_stmt_error($stmt)));
    }
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
    return $result;
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