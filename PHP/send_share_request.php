<?php
session_start();
include "functions.php";


if ($conn->connect_error)
{
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $listName = $_POST['listName'];
    $username_or_email = $_POST['usernameOrEmail'];
    $requester_id = $_SESSION['userID'];

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

        // Get the ListID of the list with the given listName
        $sql = "SELECT ListID FROM ToDoLists WHERE ListName = ? AND UserID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $listName, $requester_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1)
        {
            $list = $result->fetch_assoc();
            $list_id = $list['ListID'];

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
            echo "Liste nicht gefunden!";
        }
    }
    else
    {
        echo "Benutzer nicht gefunden!";
    }

    // Close the statement and the database connection
    $stmt->close();
    $conn->close();
}