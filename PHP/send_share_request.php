<?php
session_start();
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $list_id = $_POST['listID'];
    $username_or_email = $_POST['usernameOrEmail'];
    $requester_id = $_SESSION['user_id'];

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

        // Insert the share request record
        $sql = "INSERT INTO ShareRequests (RequesterID, RequestedUserID, ListID) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $requester_id, $requested_user_id, $list_id);
        if ($stmt->execute())
        {
            echo "Anfrage erfolgreich gesendet!";
        }
        else
        {
            echo "Fehler beim Senden der Anfrage.";
        }
    }
    else
    {
        echo "Benutzer nicht gefunden!";
    }
}
