<?php
session_start();
include 'functions.php';

$response = [];

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    // Retrieve form data
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password1 = $_POST['password-1'];
    $password2 = $_POST['password-2'];

    if ($password1 === $password2)
    {
        // Check if the username already exists
        if (!UsernameExist($username))
        {
            // Add new user to the database
            UserAdd($username, $password1, $email);
            $response['success'] = true;
        }
        else
        {
            $response['success'] = false;
            $response['message'] = 'Username already exists.';
        }
    }
    else
    {
        $response['success'] = false;
        $response['message'] = 'Passwords do not match.';
    }
}
else
{
    $response['success'] = false;
    $response['message'] = 'Invalid request method.';
}



function Connect()
{
    // Database connection settings
    $hostname = '89.58.47.144';
    $username = 'ToDoPlusUser';
    $password = 'todopluspw';
    $dbname = 'dbToDoPlus';

    // Establishing connection
    $connection = mysqli_connect($hostname, $username, $password, $dbname);
    if (!$connection)
    {
        die("Verbindung fehlgeschlagen: " . mysqli_connect_error());
    }
    return $connection;
}

/**
 * Check if a username already exists in the database.
 * 
 * @param string $username The username to check.
 * @return bool True if the username exists, otherwise false.
 */
function UsernameExist($username)
{
    $connection = Connect();
    $stmt = mysqli_prepare($connection, "SELECT Username FROM Users WHERE Username = ?");
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $exists = mysqli_num_rows($result) > 0;
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
    return $exists;
}

/**
 * Add a new user to the database.
 * 
 * @param string $username The username of the new user.
 * @param string $password The password of the new user.
 * @param string $email The email of the new user.
 */
function UserAdd($username, $password, $email)
{
    $connection = Connect();
    // Hash the password to store it securely in the database.
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($connection, "INSERT INTO Users (Username, Password, Email) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'sss', $username, $hashedPassword, $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
}
