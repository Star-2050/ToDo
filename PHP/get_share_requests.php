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

// Ensure the user is logged in
if (!isset($_SESSION['userID']))
{
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['userID'];

$conn = Connect();

$sql = "
    SELECT 
        ShareRequests.RequestID, 
        Users.Username as requesterUsername, 
        ToDoLists.ListName 
    FROM 
        ShareRequests 
    JOIN 
        Users ON ShareRequests.RequesterID = Users.UserID 
    JOIN 
        ToDoLists ON ShareRequests.ListID = ToDoLists.ListID 
    WHERE 
        ShareRequests.RequestedUserID = ? 
        AND ShareRequests.Status = 'pending'
";

if ($stmt = $conn->prepare($sql))
{
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $requests = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode($requests);

    $stmt->close();
}
else
{
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Failed to prepare SQL statement']);
}

$conn->close();
?>