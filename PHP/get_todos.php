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
    $listID = $_SESSION['listID'];
    $filter = isset($_SESSION['todoFilter']) ? $_SESSION['todoFilter'] : 3;

    $todos = GetToDosFromUserList($userID, $listID, $filter);
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

function GetToDosFromUserList($userID, $listID, $filter)
{
    $connection = Connect();
    $sql = "SELECT td.Datum, td.Task, td.Beschreibung FROM ToDos td INNER JOIN UserToDoLists utl ON td.ListID = utl.ListID WHERE utl.UserID = ? AND td.ListID = ?";

    if ($filter == 1)
    {
        $sql .= " AND DATE(td.Datum) = CURDATE()";
    }
    elseif ($filter == 2)
    {
        $sql .= " AND DATE(td.Datum) > CURDATE() AND DATE(td.Datum) <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
    }

    $stmt = $connection->prepare($sql);
    if (!$stmt)
    {
        die("Prepare failed: (" . $connection->errno . ") " . $connection->error);
    }

    $stmt->bind_param("ii", $userID, $listID);
    if (!$stmt->execute())
    {
        die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }

    $result = $stmt->get_result();
    if (!$result)
    {
        die("Getting result set failed: (" . $stmt->errno . ") " . $stmt->error);
    }

    $todos = [];
    while ($row = $result->fetch_assoc())
    {
        $todos[] = $row;
    }

    $stmt->close();
    $connection->close();
    return $todos;
}

function DisplayToDos($todos)
{
    foreach ($todos as $todo)
    {
        echo '<div class="todo-item">';
        echo '<div class="todo-task">' . htmlspecialchars($todo['Task'], ENT_QUOTES, 'UTF-8') . '</div>';
        echo '<div class="todo-description">' . htmlspecialchars($todo['Beschreibung'], ENT_QUOTES, 'UTF-8') . '</div>';
        echo '<div class="todo-date">' . htmlspecialchars($todo['Datum'], ENT_QUOTES, 'UTF-8') . '</div>';
        echo '<div class="todo-delete"> <img src="assets/icons/trash-bin.png" class="delete-icon" style="width: 3%;" onclick="deleteTodo(\'' . htmlspecialchars($todo['Task'], ENT_QUOTES, 'UTF-8') . '\')"></div>';
        echo '</div>';
    }
}

