<?php
session_start();

function connectDB()
{
    $hostname = '89.58.47.144';
    $username = 'ToDoPlusUser';
    $password = 'todopluspw';
    $dbname = 'dbToDoPlus';

    $connection = new mysqli($hostname, $username, $password, $dbname);

    if ($connection->connect_error)
    {
        die("Connection failed: " . $connection->connect_error);
    }

    return $connection;
}

if (!isset($_SESSION['userID']))
{
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['userID'];

$conn = connectDB();

$sql = "SELECT ListID, ListName FROM ToDoLists WHERE UserID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$lists = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($lists);

$stmt->close();
$conn->close();