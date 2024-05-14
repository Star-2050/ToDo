<?

include 'functions.php';

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
            echo "Neuer User erstellt";
        }
        else
        {
            echo "Benutzername existiert bereits";
        }
    }
    else
    {
        echo "Passwörter stimmen nicht überein";
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
 */
function UserAdd($username, $password, $email)
{
    $connection = Connect();
    // Passwort wird gehasht, um es sicher in der Datenbank zu speichern.
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($connection, "INSERT INTO Users (Username, Password,Email) VALUES (?, ?,?)");
    mysqli_stmt_bind_param($stmt, 'sss', $username, $hashedPassword, $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
}
