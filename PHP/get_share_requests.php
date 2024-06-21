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
        echo '<div class="request">';
        echo '<p>Request from ' . htmlspecialchars($row['RequesterUsername'], ENT_QUOTES, 'UTF-8') . ' to share list "' . htmlspecialchars($row['ListName'], ENT_QUOTES, 'UTF-8') . '"</p>';
        echo '<button class="accept-request" data-id="' . htmlspecialchars($row['RequestID'], ENT_QUOTES, 'UTF-8') . '">Accept</button>';
        echo '<button class="reject-request" data-id="' . htmlspecialchars($row['RequestID'], ENT_QUOTES, 'UTF-8') . '">Reject</button>';
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