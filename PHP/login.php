<?php
// Oliver
session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    // Retrieve form data
    $emailOrUsername = $_POST['emailOrUsername'];
    $password = $_POST['password'];

    // Authenticate user
    if (AuthenticateUser($emailOrUsername, $password))
    {
        // Speichern der Benutzer-ID in der Session
        $_SESSION['userID'] = GetUserID($emailOrUsername);
        $_SESSION['listID'] = GetDefaultListID($_SESSION['userID']); // Falls es eine Standardliste gibt
        $_SESSION['todoFilter'] = 1;

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
 * Authentifiziert einen Benutzer basierend auf Benutzername/Email und Passwort.
 * 
 * @param string $usernameOrEmail Der Benutzername oder die E-Mail-Adresse des Benutzers.
 * @param string $password Das Passwort des Benutzers.
 * @return bool Wahr, wenn die Authentifizierung erfolgreich ist, sonst falsch.
 */
function AuthenticateUser($usernameOrEmail, $password)
{
    $connection = Connect();
    $stmt = mysqli_prepare($connection, "SELECT UserID, Password FROM Users WHERE Username = ? OR Email = ?");
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

/**
 * Holt die Benutzer-ID basierend auf Benutzername oder E-Mail.
 * 
 * @param string $usernameOrEmail Der Benutzername oder die E-Mail-Adresse des Benutzers.
 * @return int Die Benutzer-ID.
 */
function GetUserID($usernameOrEmail)
{
    $connection = Connect();
    $stmt = mysqli_prepare($connection, "SELECT UserID FROM Users WHERE Username = ? OR Email = ?");
    mysqli_stmt_bind_param($stmt, 'ss', $usernameOrEmail, $usernameOrEmail);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $userID = null;
    if ($row = mysqli_fetch_assoc($result))
    {
        $userID = $row['UserID'];
    }

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    return $userID;
}

/**
 * Holt die Standard-Listen-ID für einen Benutzer.
 * 
 * @param int $userID Die Benutzer-ID.
 * @return int Die Listen-ID.
 */
function GetDefaultListID($userID)
{
    $connection = Connect();
    $stmt = mysqli_prepare($connection, "SELECT ListID FROM UserToDoLists WHERE UserID = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 'i', $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $listID = null;
    if ($row = mysqli_fetch_assoc($result))
    {
        $listID = $row['ListID'];
    }

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    return $listID;
}
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