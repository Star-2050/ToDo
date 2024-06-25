<?php
// Oliver
session_start();

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
            $_SESSION['userID'] = GetUserID($email);
            $_SESSION['listID'] = GetDefaultListID($_SESSION['userID']); // Falls es eine Standardliste gibt
            $_SESSION['todoFilter'] = 1;
            header('Location: ../ToDoPlus.html');
            exit();
        }
        else
        {
            header('Location: ../Sign-up.html');
            exit();
        }
    }
    else
    {
        header('Location: ../Sign-up.html');
        exit();
        // echo "Passwörter stimmen nicht überein"; // Diese Zeile entfernt, weil header() verwendet wird.
    }
}

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
 * Fügt einen neuen Benutzer in die Datenbank ein.
 * 
 * @param string $username Der Benutzername des neuen Benutzers.
 * @param string $password Das Passwort des neuen Benutzers.
 * @param string $email Die E-Mail des neuen Benutzers.
 */
function UserAdd($username, $password, $email)
{
    $connection = Connect();
    // Passwort wird gehasht, um es sicher in der Datenbank zu speichern.
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($connection, "INSERT INTO Users (Username, Password, Email) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'sss', $username, $hashedPassword, $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
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