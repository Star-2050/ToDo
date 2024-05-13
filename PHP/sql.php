<?php

function Connect()
{
    $hostname = '89.58.47.144';
    $username = 'ToDoPlusUser';
    $password = 'todopluspw';
    $dbname = 'dbToDoPlus';

    $connection = mysqli_connect($hostname, $username, $password, $dbname);
    if (!$connection)
    {
        die("Verbindung fehlgeschlagen: " . mysqli_connect_error());
    }
    return $connection;
}

function ToDoListAdd($listName)
{
    $connection = Connect();
    $stmt = mysqli_prepare($connection, "INSERT INTO ToDoLists (ListName) VALUES (?)");
    mysqli_stmt_bind_param($stmt, 's', $listName);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
}

function ToDoListDelete($listID)
{
    $connection = Connect();
    $stmt = mysqli_prepare($connection, "DELETE FROM ToDoLists WHERE ListID = ?");
    mysqli_stmt_bind_param($stmt, 'i', $listID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
}

function ToDoAdd($listID, $task)
{
    $connection = Connect();
    $stmt = mysqli_prepare($connection, "INSERT INTO ToDos (ListID, Task) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, 'is', $listID, $task);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
}

function ToDoDelete($todoID)
{
    $connection = Connect();
    $stmt = mysqli_prepare($connection, "DELETE FROM ToDos WHERE TodoID = ?");
    mysqli_stmt_bind_param($stmt, 'i', $todoID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
}

function UserAdd($username, $password)
{
    $connection = Connect();
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($connection, "INSERT INTO Users (Username, Password) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, 'ss', $username, $hashedPassword);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
}

function UserDelete($userID)
{
    $connection = Connect();
    $stmt = mysqli_prepare($connection, "DELETE FROM Users WHERE UserID = ?");
    mysqli_stmt_bind_param($stmt, 'i', $userID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
}

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

function AuthenticateUser($usernameOrEmail, $password)
{
    $connection = Connect();
    // Die Verwendung von Prepared Statements schützt gegen SQL-Injection
    $stmt = mysqli_prepare($connection, "SELECT Password FROM Users WHERE Username = ? OR Email = ?");
    mysqli_stmt_bind_param($stmt, 'ss', $usernameOrEmail, $usernameOrEmail);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result))
    {
        // Überprüfe, ob das Passwort mit dem gehashten Passwort in der Datenbank übereinstimmt
        if (password_verify($password, $row['Password']))
        {
            mysqli_stmt_close($stmt);
            mysqli_close($connection);
            return true; // Authentifizierung erfolgreich
        }
    }
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
    return false; // Authentifizierung fehlgeschlagen
}
