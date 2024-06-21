<?php
session_start();

function Connect()
{
    $hostname = '89.58.47.144';
    $username = 'ToDoPlusUser';
    $password = 'todopluspw';
    $dbname = 'dbToDoPlus';

    $connection = new mysqli($hostname, $username, $password, $dbname);
    if ($connection->connect_error)
    {
        die("Verbindung fehlgeschlagen: " . $connection->connect_error);
    }
    return $connection;
}

$conn = Connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $request_id = $_POST['requestID'];
    $action = $_POST['action']; // 'accept' or 'reject'

    if ($action === 'accept')
    {
        // Update the status of the share request to 'accepted'
        $sql = "UPDATE ShareRequests SET Status = 'accepted' WHERE RequestID = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt)
        {
            $stmt->bind_param("i", $request_id);
            if ($stmt->execute())
            {
                // Get the details of the share request
                $sql = "SELECT ListID, RequestedUserID FROM ShareRequests WHERE RequestID = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt)
                {
                    $stmt->bind_param("i", $request_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result && $result->num_rows === 1)
                    {
                        $request = $result->fetch_assoc();
                        $list_id = $request['ListID'];
                        $user_id = $request['RequestedUserID'];
                        $stmt->close();

                        // Add the user to the UserToDoLists table
                        $sql = "INSERT INTO UserToDoLists (ListID, UserID) VALUES (?, ?)";
                        $stmt = $conn->prepare($sql);
                        if ($stmt)
                        {
                            $stmt->bind_param("ii", $list_id, $user_id);
                            if ($stmt->execute())
                            {
                                echo "Anfrage erfolgreich akzeptiert!";
                            }
                            else
                            {
                                echo "Fehler beim Hinzufügen des Benutzers zur Liste.";
                            }
                            $stmt->close();
                        }
                        else
                        {
                            echo "Fehler beim Vorbereiten der SQL-Anweisung für das Hinzufügen.";
                        }
                    }
                    else
                    {
                        echo "Fehler beim Abrufen der Anfrage.";
                    }
                }
                else
                {
                    echo "Fehler beim Vorbereiten der SQL-Anweisung für das Abrufen.";
                }
            }
            else
            {
                echo "Fehler beim Aktualisieren des Anfrage-Status.";
            }
            $stmt->close();
        }
        else
        {
            echo "Fehler beim Vorbereiten der SQL-Anweisung für das Aktualisieren.";
        }
    }
    else if ($action === 'reject')
    {
        // Update the status of the share request to 'rejected'
        $sql = "UPDATE ShareRequests SET Status = 'rejected' WHERE RequestID = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt)
        {
            $stmt->bind_param("i", $request_id);
            if ($stmt->execute())
            {
                echo "Anfrage abgelehnt.";
            }
            else
            {
                echo "Fehler beim Aktualisieren des Anfrage-Status.";
            }
            $stmt->close();
        }
        else
        {
            echo "Fehler beim Vorbereiten der SQL-Anweisung für das Aktualisieren.";
        }
    }
    else
    {
        echo "Ungültige Aktion.";
    }
}

$conn->close();