<?php
session_start();
//Oliver
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
    $sql = "SELECT ToDoLists.ListID, ToDoLists.ListName FROM UserToDoLists 
            JOIN ToDoLists ON UserToDoLists.ListID = ToDoLists.ListID 
            WHERE UserToDoLists.UserID = ?";
    $stmt = $connection->prepare($sql);
    if (!$stmt)
    {
        die("Prepare failed: (" . $connection->errno . ") " . $connection->error);
    }

    $stmt->bind_param("i", $userID);
    if (!$stmt->execute())
    {
        die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }

    $result = $stmt->get_result();
    if (!$result)
    {
        die("Getting result set failed: (" . $stmt->errno . ") " . $stmt->error);
    }

    $todoLists = array();
    while ($row = $result->fetch_assoc())
    {
        $todoLists[] = $row;
    }
    $stmt->close();
    $connection->close();
    return $todoLists;
}

function DisplayToDoLists($todoLists)
{
    echo '<div class="todo-lists">';
    foreach ($todoLists as $todoList)
    {
        echo '<button class="project-button" data-listid="' . htmlspecialchars($todoList['ListID'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($todoList['ListName'], ENT_QUOTES, 'UTF-8') . '</button>';
        echo '<div class="todoList-delete"> <img src="assets/icons/trash-bin.png" class="delete-icon2" data-task="' . htmlspecialchars($todoList['Task'], ENT_QUOTES, 'UTF-8') . '" style="width: 10%;" onclick="delete_todoList(\'' . htmlspecialchars($todoList['Task'], ENT_QUOTES, 'UTF-8') . '\')"></div>';
        echo '<br/>';
    }
    echo '</div>';
}