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

    if (deleteUserAndRelatedData($userID))
    {
        echo json_encode(['status' => 'success', 'message' => 'Account and related data deleted successfully.']);
    }
    else
    {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete account and related data.']);
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

function deleteUserAndRelatedData($userID)
{
    $connection = Connect();

    // Start transaction
    $connection->begin_transaction();

    try
    {
        // Delete todos related to user
        $sql = "DELETE td FROM ToDos td
                INNER JOIN UserToDoLists utl ON td.ListID = utl.ListID
                WHERE utl.UserID = ?";
        $stmt = $connection->prepare($sql);
        if (!$stmt)
        {
            throw new Exception("Prepare failed: (" . $connection->errno . ") " . $connection->error);
        }
        $stmt->bind_param("i", $userID);
        if (!$stmt->execute())
        {
            throw new Exception("Execution failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        $stmt->close();

        // Delete lists related to user
        $sql = "DELETE FROM ToDoLists WHERE ListID IN (SELECT ListID FROM UserToDoLists WHERE UserID = ?)";
        $stmt = $connection->prepare($sql);
        if (!$stmt)
        {
            throw new Exception("Prepare failed: (" . $connection->errno . ") " . $connection->error);
        }
        $stmt->bind_param("i", $userID);
        if (!$stmt->execute())
        {
            throw new Exception("Execution failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        $stmt->close();

        // Delete user's list associations
        $sql = "DELETE FROM UserToDoLists WHERE UserID = ?";
        $stmt = $connection->prepare($sql);
        if (!$stmt)
        {
            throw new Exception("Prepare failed: (" . $connection->errno . ") " . $connection->error);
        }
        $stmt->bind_param("i", $userID);
        if (!$stmt->execute())
        {
            throw new Exception("Execution failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        $stmt->close();

        // Delete user
        $sql = "DELETE FROM Users WHERE UserID = ?";
        $stmt = $connection->prepare($sql);
        if (!$stmt)
        {
            throw new Exception("Prepare failed: (" . $connection->errno . ") " . $connection->error);
        }
        $stmt->bind_param("i", $userID);
        if (!$stmt->execute())
        {
            throw new Exception("Execution failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        $stmt->close();

        // Commit transaction
        $connection->commit();

        // Clear session
        session_unset();
        session_destroy();

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