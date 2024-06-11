<?php
session_start();
// Oliver

// Überprüfen, ob die Anfrage eine POST-Anfrage ist
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    // Überprüfen, ob der Benutzer angemeldet ist
    if (!isset($_SESSION['userID']))
    {
        header('Location: ../Log-in.html');
        exit();
    }

    $userID = $_SESSION['userID'];

    // ToDo-Listen abrufen
    $todoLists = GetToDoLists($userID);

    // ToDo-Listen anzeigen
    DisplayToDoLists($todoLists);
}

/**
 * Stellt eine Verbindung zur Datenbank her.
 *
 * @return mysqli Die Datenbankverbindung.
 */
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

/**
 * Holt die ToDo-Listen eines Benutzers aus der Datenbank.
 *
 * @param int $userID Die Benutzer-ID.
 * @return array Die ToDo-Listen.
 */
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

/**
 * Zeigt die ToDo-Listen in HTML an.
 *
 * @param array $todoLists Die ToDo-Listen.
 */
function DisplayToDoLists($todoLists)
{
    echo '<div class="todo-lists">';
    foreach ($todoLists as $todoList)
    {
        echo '<button class="project-button" data-listid="' . htmlspecialchars($todoList['ListID'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($todoList['ListName'], ENT_QUOTES, 'UTF-8') . '</button>';
        echo '<form action="PHP/delete_todoList.php" method="post" style="display:inline-block;">';
        echo '<input type="hidden" name="listID" value="' . htmlspecialchars($todoList['ListID'], ENT_QUOTES, 'UTF-8') . '">';
        echo '<button type="submit" class="delete-icon2" style="background:none;border:none;">';
        echo '<img src="assets/icons/trash-bin.png" style="width: 10%;"></button>';
        echo '</form><br>';
    }
    echo '</div>';
}
