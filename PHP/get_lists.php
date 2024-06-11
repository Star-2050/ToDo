<?php
session_start();
require 'db_connection.php';

$user_id = $_SESSION['user_id']; // Assume user_id is stored in session
$sql = "SELECT id, name FROM lists WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$lists = [];
while ($row = $result->fetch_assoc())
{
    $lists[] = $row;
}

echo json_encode($lists);
