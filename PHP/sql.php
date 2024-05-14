<?php
include 'functions.php';










/**
 * Stellt eine Verbindung zur Datenbank her.
 * 
 * @return mysqli Eine Verbindungskennung, die von mysqli_connect zurückgegeben wird.
 */
function Connect()
{
    // Datenbankverbindungseinstellungen
    $hostname = '89.58.47.144';
    $username = 'ToDoPlusUser';
    $password = 'todopluspw';
    $dbname = 'dbToDoPlus';

    // Verbindungsaufbau
    $connection = mysqli_connect($hostname, $username, $password, $dbname);
    if (!$connection)
    {
        die("Verbindung fehlgeschlagen: " . mysqli_connect_error());
    }
    return $connection;
}

/**
 * Fügt eine neue To-Do-Liste in die Datenbank ein.
 * 
 * @param string $listName Der Name der To-Do-Liste.
 */
function ToDoListAdd($listName)
{
    $connection = Connect();
    $stmt = mysqli_prepare($connection, "INSERT INTO ToDoLists (ListName) VALUES (?)");
    mysqli_stmt_bind_param($stmt, 's', $listName);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
}

/**
 * Löscht eine To-Do-Liste aus der Datenbank.
 * 
 * @param int $listID Die ID der zu löschenden Liste.
 */
function ToDoListDelete($listID)
{
    $connection = Connect();
    $stmt = mysqli_prepare($connection, "DELETE FROM ToDoLists WHERE ListID = ?");
    mysqli_stmt_bind_param($stmt, 'i', $listID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
}

/**
 * Fügt ein To-Do in die Datenbank ein.
 * 
 * @param int $listID Die ID der Liste, zu der das To-Do hinzugefügt wird.
 * @param string $task Die Beschreibung des To-Do.
 */
function ToDoAdd($listID, $task)
{
    $connection = Connect();
    $stmt = mysqli_prepare($connection, "INSERT INTO ToDos (ListID, Task) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, 'is', $listID, $task);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
}

/**
 * Löscht ein To-Do aus der Datenbank.
 * 
 * @param int $todoID Die ID des zu löschenden To-Do.
 */
function ToDoDelete($todoID)
{
    $connection = Connect();
    $stmt = mysqli_prepare($connection, "DELETE FROM ToDos WHERE TodoID = ?");
    mysqli_stmt_bind_param($stmt, 'i', $todoID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
}



/**
 * Löscht einen Benutzer aus der Datenbank.
 * 
 * @param int $userID Die ID des zu löschenden Benutzers.
 */
function UserDelete($userID)
{
    $connection = Connect();
    $stmt = mysqli_prepare($connection, "DELETE FROM Users WHERE UserID = ?");
    mysqli_stmt_bind_param($stmt, 'i', $userID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
}
