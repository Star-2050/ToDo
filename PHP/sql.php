<?php

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
 * Fügt einen neuen Benutzer in die Datenbank ein.
 * 
 * @param string $username Der Benutzername des neuen Benutzers.
 * @param string $password Das Passwort des neuen Benutzers.
 */
function UserAdd($username, $password)
{
    $connection = Connect();
    // Passwort wird gehasht, um es sicher in der Datenbank zu speichern.
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($connection, "INSERT INTO Users (Username, Password) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, 'ss', $username, $hashedPassword);
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

/**
 * Überprüft, ob ein Benutzername in der Datenbank existiert.
 * 
 * @param string $username Der zu überprüfende Benutzername.
 * @return bool Wahr, wenn der Benutzername existiert, sonst falsch.
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
 * Authentifiziert einen Benutzer basierend auf Benutzername/Email und Passwort.
 * 
 * @param string $usernameOrEmail Der Benutzername oder die E-Mail-Adresse des Benutzers.
 * @param string $password Das Passwort des Benutzers.
 * @return bool Wahr, wenn die Authentifizierung erfolgreich ist, sonst falsch.
 */
function AuthenticateUser($usernameOrEmail, $password)
{
    $connection = Connect();
    $stmt = mysqli_prepare($connection, "SELECT Password FROM Users WHERE Username = ? OR Email = ?");
    mysqli_stmt_bind_param($stmt, 'ss', $usernameOrEmail, $usernameOrEmail);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result))
    {
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
