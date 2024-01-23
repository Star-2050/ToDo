<?php
// Hier Code für die Datenbankverbindung einfügen
$servername = "DeinServername";
$username = "DeinBenutzername";
$password = "DeinPasswort";
$dbname = "DeineDatenbankname";

$conn = mysqli_connect($servername, $username, $password, $dbname);

// Überprüfe die Verbindung
if (!$conn) {
    die("Verbindung fehlgeschlagen: " . mysqli_connect_error());
}

// Funktion zum Abrufen und Anzeigen von Notizen
function displayNotes($conn) {
    // Hier SQL-Abfrage für Notizen einfügen
    $query = "SELECT * FROM notes";
    
    // Hier Datenbankabfrage durchführen und Ergebnisse anzeigen
    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        echo '<div class="note">';
        echo '<p class="category">' . $row['category'] . '</p>';
        echo '<p>' . $row['content'] . '</p>';
        echo '</div>';
    }
}

// Hier Code für Formularverarbeitung einfügen (Hinzufügen von Notizen)
// Beispiel: if ($_SERVER['REQUEST_METHOD'] == 'POST') { ... }
// ...

// Hier Code für die Kategorienauswahl einfügen (z. B. aus der Datenbank abrufen)
// Beispiel: $categories = ["Arbeit", "Persönlich", "Einkauf"];
// ...
?>

<form action="" method="post">
    <label for="category">Kategorie:</label>
    <!-- Hier Dropdown mit Kategorien einfügen -->
    <!-- Beispiel: <select name="category" id="category"><?php foreach ($categories as $category) { echo '<option value="' . $category . '">' . $category . '</option>'; } ?></select> -->
    <br>
    <label for="content">Notiz:</label>
    <textarea name="content" id="content" rows="4" cols="50"></textarea>
    <br>
    <input type="submit" value="Hinzufügen">
</form>

<?php
// Notizen anzeigen
displayNotes($conn);

// Hier Code für Datenbankverbindung schließen einfügen
mysqli_close($conn);
?>
