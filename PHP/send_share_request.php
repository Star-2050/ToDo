<?php
session_start();

function Connect()
{
    $hostname = '89.58.47.144';
    $username = 'ToDoPlusUser';
    $password = 'todopluspw';
    $dbname = 'dbToDoPlus';

    $connection = new mysqli($hostname, $username, $password, $dbname);
    if ($connection->connect_error)
    {
        die("Verbindung fehlgeschlagen: " . $connection->connect_error);
    }
    return $connection;
}

$conn = Connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $list_id = $_POST['listID'];
    $username_or_email = $_POST['usernameOrEmail'];
    $requester_id = $_SESSION['userID']; // Use 'userID' to match your other scripts

    // Get the user ID of the user to share with
    $sql = "SELECT UserID FROM Users WHERE Username = ? OR Email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username_or_email, $username_or_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1)
    {
        $requested_user = $result->fetch_assoc();
        $requested_user_id = $requested_user['UserID'];
        $stmt->close();

        // Insert the share request record
        $sql = "INSERT INTO ShareRequests (RequesterID, RequestedUserID, ListID, Status) VALUES (?, ?, ?, 'pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $requester_id, $requested_user_id, $list_id);
        if ($stmt->execute())
        {
            echo "Anfrage erfolgreich gesendet!";
            $stmt->close();
            header('Location: ../ToDoPlus.html');
            exit();
        }
        else
        {
            echo "Fehler beim Senden der Anfrage.";
            $stmt->close();
            header('Location: ../ToDoPlus.html');
            exit();
        }
    }
    else
    {
        echo "Benutzer nicht gefunden!";
        $stmt->close();
        header('Location: ../ToDoPlus.html');
        exit();
    }
}
else
{
    echo "Ungültige Anforderung.";
    header('Location: ../ToDoPlus.html');

}

$conn->close();
exit();