<?php
session_start();
header("Content-Type: text/html");

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
        tdl.ListID, 
        tdl.ListName 
    FROM 
        ToDoLists tdl
    INNER JOIN 
        UserToDoLists utdl 
    ON 
        tdl.ListID = utdl.ListID 
    WHERE 
        utdl.UserID = ?
";

if ($stmt = $conn->prepare($sql))
{
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc())
    {
        echo '<option value="' . $row['ListID'] . '">' . $row['ListName'] . '</option>';
    }

    $stmt->close();
}
else
{
    http_response_code(500);
    echo "Failed to prepare SQL statement";
}

$conn->close();