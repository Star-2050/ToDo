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
        echo '<div style="display: flex; align-items: center; margin: 10px 0;">';

        // Form for setting the list ID
        echo '<form action="PHP/setListID.php" method="post" style="flex: 1; margin: 0;">';
        echo '<input type="hidden" name="listID" value="' . htmlspecialchars($todoList['ListID'], ENT_QUOTES, 'UTF-8') . '">';
        echo '<button type="submit" class="todo-list-button" style="background-color: #2d2c2c51; color: white; padding: 10px; border: 1px solid black; border-radius: 5px; cursor: pointer; text-align: center; width: 100%; transition: background-color 0.3s, color 0.3s; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5); margin: 10px 0;" onmouseover="this.style.backgroundColor=\'#dddddd8f\'; this.style.color=\'black\';" onmouseout="this.style.backgroundColor=\'#2d2c2c51\'; this.style.color=\'white\';">' . htmlspecialchars($todoList['ListName'], ENT_QUOTES, 'UTF-8') . '</button>';
        echo '</form>';

        // Form for deleting the to-do list
        echo '<form action="PHP/delete_todoList.php" method="post" style="display: inline-block; margin-left: 10px;">';
        echo '<input type="hidden" name="listID" value="' . htmlspecialchars($todoList['ListID'], ENT_QUOTES, 'UTF-8') . '">';
        echo '<button type="submit" style="background: none; border: none; cursor: pointer;">';
        echo '<img src="assets/icons/trash-bin.png" style="width: 20px;"></button>';
        echo '</form>';

        echo '</div>';
    }
    echo '</div>';
}