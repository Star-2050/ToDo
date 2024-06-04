<?php
session_start();

// Fehleranzeige aktivieren
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if (isset($_POST['title']) && isset($_POST['description']) && isset($_POST['date']))
    {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $date = $_POST['date'];
        $listID = $_SESSION['listID'];

        if (isValidListID($listID))
        {
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
            die('Invalid list ID.');
        }
    }
    else
    {
        header('Location: ../ToDoPlus.html');
        exit();
    }
}

function isValidListID($listID)
{
    $connection = Connect();
    $stmt = mysqli_prepare($connection, "SELECT 1 FROM ToDoLists WHERE ListID = ?");
    mysqli_stmt_bind_param($stmt, 'i', $listID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $isValid = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
    return $isValid;
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
        die('Execute failed: ' . htmlspecialchars(mysqli_stmt_error($stmt)) . ' with ListID=' . $listID . ', Title=' . $title . ', Description=' . $description . ', Date=' . $date);
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
