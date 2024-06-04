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

    $todoLists = GetToDoLists($userID);
    DisplayToDoLists($todoLists);
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

function GetToDoLists($userID)
{
    $connection = Connect();
    $sql = "SELECT ToDoLists.ListName FROM UserToDoLists 
            JOIN ToDoLists ON UserToDoLists.ListID = ToDoLists.ListID 
            WHERE UserToDoLists.UserID = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $todoLists = array();
    while ($row = $result->fetch_assoc())
    {
        $todoLists[] = $row["ListName"];
    }
    $stmt->close();
    $connection->close();
    return $todoLists;
}

function DisplayToDoLists($todos)
{
    echo '<div class="todo-lists">';
    foreach ($todos as $todo)
    {
        echo '<button class="todo-list-button">' . htmlspecialchars($todo) . '</button>';
        echo '<br/>';
    }
    echo '</div>';
}
