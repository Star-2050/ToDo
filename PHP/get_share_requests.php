<?php
session_start();
require 'db_connection.php';

$user_id = $_SESSION['user_id']; // Assume user_id is stored in session
$sql = "SELECT ShareRequests.RequestID, Users.Username as requesterUsername, ToDoLists.ListName 
        FROM ShareRequests 
        JOIN Users ON ShareRequests.RequesterID = Users.UserID 
        JOIN ToDoLists ON ShareRequests.ListID = ToDoLists.ListID 
        WHERE ShareRequests.RequestedUserID = ? AND ShareRequests.Status = 'pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$requests = [];
while ($row = $result->fetch_assoc())
{
    $requests[] = $row;
}

echo json_encode($requests);
