<?php
session_start();
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $request_id = $_POST['requestID'];
    $action = $_POST['action']; // 'accept' or 'reject'

    if ($action === 'accept')
    {
        // Update the status of the share request to 'accepted'
        $sql = "UPDATE ShareRequests SET Status = 'accepted' WHERE RequestID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();

        // Get the details of the share request
        $sql = "SELECT ListID, RequestedUserID FROM ShareRequests WHERE RequestID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $request = $result->fetch_assoc();

        $list_id = $request['ListID'];
        $user_id = $request['RequestedUserID'];

        // Add the user to the UserToDoLists table
        $sql = "INSERT INTO UserToDoLists (ListID, UserID) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $list_id, $user_id);
        $stmt->execute();

        echo "Anfrage erfolgreich akzeptiert!";
    }
    else
    {
        // Handle rejection of the request if needed
        echo "Anfrage abgelehnt.";
    }
}
