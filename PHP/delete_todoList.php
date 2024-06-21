<?php
session_start();
// Oliver

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if (!isset($_SESSION['userID']))
    {
        header('Location: ../Log-in.html');
        exit();
    }

    $userID = $_SESSION['userID'];
    $listID = $_POST['listID'];

    // Füge eine Debugging-Ausgabe hinzu
    error_log("UserID: $userID, ListID: $listID");

    if (removeUserFromTodoList($userID, $listID))
    {
        header('Location: ../ToDoPlus.html');
        exit();
    }
    else
    {
        echo "Failed to remove user from ToDo list.";
    }
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
 * Entfernt die Zuordnung eines Benutzers von einer ToDo-Liste.
 *
 * @param int $userID Die Benutzer-ID.
 * @param int $listID Die Listen-ID.
 * @return bool Wahr, wenn die Entfernung erfolgreich war, sonst falsch.
 */
function removeUserFromTodoList($userID, $listID)
{
    $connection = Connect();

    // Start transaction
    $connection->begin_transaction();

    try
    {
        // Verify that the list belongs to the user
        $sql = "SELECT * FROM UserToDoLists WHERE UserID = ? AND ListID = ?";
        $stmt = $connection->prepare($sql);
        if (!$stmt)
        {
            throw new Exception("Prepare failed: (" . $connection->errno . ") " . $connection->error);
        }
        $stmt->bind_param("ii", $userID, $listID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 0)
        {
            throw new Exception("No such list found for the user.");
        }
        $stmt->close();

        // Remove the user-list association
        $sql = "DELETE FROM UserToDoLists WHERE UserID = ? AND ListID = ?";
        $stmt = $connection->prepare($sql);
        if (!$stmt)
        {
            throw new Exception("Prepare failed: (" . $connection->errno . ") " . $connection->error);
        }
        $stmt->bind_param("ii", $userID, $listID);
        if (!$stmt->execute())
        {
            throw new Exception("Execution failed while deleting UserToDoLists: (" . $stmt->errno . ") " . $stmt->error);
        }
        $stmt->close();

        // Commit transaction
        $connection->commit();

        $connection->close();
        return true;
    }
    catch (Exception $e)
    {
        // Rollback transaction on error
        $connection->rollback();
        $connection->close();
        error_log($e->getMessage());
        echo "Error: " . $e->getMessage(); // Diese Zeile zeigt die Fehlermeldung direkt an
        return false;
    }
}