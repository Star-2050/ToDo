<?php
session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST")
{

    if (!isset($_SESSION['userID']))
    {
        header('Location: ../Log-in.html');
        exit();
    }

    // Zugriff auf die Benutzer- und Listen-ID
    $userID = $_SESSION['userID'];
    $listID = $_SESSION['listID'];

    $todos = GetToDosFromUserList($userID, $listID);
    DisplayToDos($todos);
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

function GetToDosFromUserList($userID, $listID)
{
    $connection = Connect();
    $stmt = mysqli_prepare($connection, "SELECT td.Datum, td.Task, td.Beschreibung FROM ToDos td INNER JOIN UserToDoLists utl ON td.ListID = utl.ListID WHERE utl.UserID = ? AND td.ListID = ?");
    mysqli_stmt_bind_param($stmt, 'ii', $userID, $listID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $todos = [];
    while ($row = mysqli_fetch_assoc($result))
    {
        $todos[] = $row;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    return $todos;
}

function DisplayToDos($todos)
{
    foreach ($todos as $todo)
    {
        echo '<div class="todo-item">';
        echo '<div class="todo-task">' . htmlspecialchars($todo['Task']) . '</div>';
        echo '<div class="todo-description">' . htmlspecialchars($todo['Beschreibung']) . '</div>';
        echo '<div class="todo-date">' . htmlspecialchars($todo['Datum']) . '</div>';
        echo '</div>';
    }
}

