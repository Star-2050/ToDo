<?php
// Oliver

include 'functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    // Retrieve form data
    $emailOrUsername = $_POST['emailOrUsername'];
    $password = $_POST['password'];

    // Authenticate user
    if (AuthenticateUser($emailOrUsername, $password))
    {
        // Redirect to the ToDoPlus page
        header('Location: ../ToDoPlus.html');
        exit();
    }
    else
    {
        // Redirect to the login page
        header('Location: ../Log-in.html');
        exit();
    }
}

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
