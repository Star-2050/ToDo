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
    $listID = $_POST['listID'];

    if (deleteTodoListAndItems($userID, $listID))
    {
        echo json_encode(['status' => 'success', 'message' => 'ToDo list and related items deleted successfully.']);
    }
    else
    {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete ToDo list and related items.']);
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

function deleteTodoListAndItems($userID, $listID)
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

        // Delete todos related to the list
        $sql = "DELETE FROM ToDos WHERE ListID = ?";
        $stmt = $connection->prepare($sql);
        if (!$stmt)
        {
            throw new Exception("Prepare failed: (" . $connection->errno . ") " . $connection->error);
        }
        $stmt->bind_param("i", $listID);
        if (!$stmt->execute())
        {
            throw new Exception("Execution failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        $stmt->close();

        // Delete the list
        $sql = "DELETE FROM ToDoLists WHERE ListID = ?";
        $stmt = $connection->prepare($sql);
        if (!$stmt)
        {
            throw new Exception("Prepare failed: (" . $connection->errno . ") " . $connection->error);
        }
        $stmt->bind_param("i", $listID);
        if (!$stmt->execute())
        {
            throw new Exception("Execution failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        $stmt->close();

        // Delete the list association
        $sql = "DELETE FROM UserToDoLists WHERE ListID = ?";
        $stmt = $connection->prepare($sql);
        if (!$stmt)
        {
            throw new Exception("Prepare failed: (" . $connection->errno . ") " . $connection->error);
        }
        $stmt->bind_param("i", $listID);
        if (!$stmt->execute())
        {
            throw new Exception("Execution failed: (" . $stmt->errno . ") " . $stmt->error);
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
        return false;
    }
}