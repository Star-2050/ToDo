<?php

function ConnectAndSelect()
{
    $hostname = '89.58.47.144';
    $username = 'testUser';
    $password = 'testPasswort';
    $dbname = 'dbMeineDatenbank';

    $connection = mysqli_connect($hostname, $username, $password, $dbname);
    if (!$connection)
    {
        die("Verbindung fehlgeschlagen: " . mysqli_connect_error());
    }

    $query = "SELECT dtIAM, dtNote, dtFach FROM tblNote";
    $result = mysqli_query($connection, $query);

    echo "<table border='1'><tr><th>IAM</th><th>Note</th><th>Fach</th></tr>";
    while ($row = mysqli_fetch_assoc($result))
    {
        echo "<tr><td>{$row['dtIAM']}</td><td>{$row['dtNote']}</td><td>{$row['dtFach']}</td></tr>";
    }
    echo "</table>";

    mysqli_close($connection);
}

