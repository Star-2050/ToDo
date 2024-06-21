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

if (!isset($_SESSION['userID']))
{
    http_response_code(401);
    echo "User not logged in";
    exit();
}

$user_id = $_SESSION['userID'];

$conn = Connect();

$sql = "
    SELECT 
        sr.RequestID,
        sr.ListID,
        tdl.ListName,
        u.Username AS RequesterUsername
    FROM 
        ShareRequests sr
    INNER JOIN 
        ToDoLists tdl ON sr.ListID = tdl.ListID
    INNER JOIN 
        Users u ON sr.RequesterID = u.UserID
    WHERE 
        sr.RequestedUserID = ? AND sr.Status = 'pending'
";

if ($stmt = $conn->prepare($sql))
{
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc())
    {
        echo '<div class="request" style="display: flex; flex-direction: column; align-items: center; margin: 10px 0; width: 90%;">';
        echo '<p style="color: white; padding: 10px; border: 1px solid black; border-radius: 5px; text-align: center; width: 100%; margin: 10px 0; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);">' . 'Request from ' . htmlspecialchars($row['RequesterUsername'], ENT_QUOTES, 'UTF-8') . ' to share list "' . htmlspecialchars($row['ListName'], ENT_QUOTES, 'UTF-8') . '"</p>';
        echo '<div style="display: flex; justify-content: center; width: 100%;">';
        echo '<button class="accept-request" data-id="' . htmlspecialchars($row['RequestID'], ENT_QUOTES, 'UTF-8') . '" style="background-color: #2d2c2c51; color: white; padding: 10px; border: 1px solid black; border-radius: 5px; cursor: pointer; transition: background-color 0.3s, color 0.3s; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5); margin: 5px;" onmouseover="this.style.backgroundColor=\'#dddddd8f\'; this.style.color=\'black\';" onmouseout="this.style.backgroundColor=\'#2d2c2c51\'; this.style.color=\'white\';">Accept</button>';
        echo '<button class="reject-request" data-id="' . htmlspecialchars($row['RequestID'], ENT_QUOTES, 'UTF-8') . '" style="background-color: #2d2c2c51; color: white; padding: 10px; border: 1px solid black; border-radius: 5px; cursor: pointer; transition: background-color 0.3s, color 0.3s; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5); margin: 5px;" onmouseover="this.style.backgroundColor=\'#dddddd8f\'; this.style.color=\'black\';" onmouseout="this.style.backgroundColor=\'#2d2c2c51\'; this.style.color=\'white\';">Reject</button>';
        echo '</div>';
        echo '</div>';


    }

    $stmt->close();
}
else
{
    http_response_code(500);
    echo "Failed to prepare SQL statement";
}

$conn->close();