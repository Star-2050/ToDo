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
                $stmt2 = $conn->prepare($sql);
                if ($stmt2)
                {
                    $stmt2->bind_param("i", $request_id);
                    $stmt2->execute();
                    $result = $stmt2->get_result();
                    if ($result && $result->num_rows === 1)
                    {
                        $request = $result->fetch_assoc();
                        $list_id = $request['ListID'];
                        $user_id = $request['RequestedUserID'];
                        $stmt2->close();

                        // Check if the user already has access to the list
                        $sql = "SELECT * FROM UserToDoLists WHERE ListID = ? AND UserID = ?";
                        $stmt3 = $conn->prepare($sql);
                        if ($stmt3)
                        {
                            $stmt3->bind_param("ii", $list_id, $user_id);
                            $stmt3->execute();
                            $result = $stmt3->get_result();
                            if ($result->num_rows === 0)
                            {
                                // Add the user to the UserToDoLists table
                                $sql = "INSERT INTO UserToDoLists (ListID, UserID) VALUES (?, ?)";
                                $stmt4 = $conn->prepare($sql);
                                if ($stmt4)
                                {
                                    $stmt4->bind_param("ii", $list_id, $user_id);
                                    if ($stmt4->execute())
                                    {
                                        echo "Anfrage erfolgreich akzeptiert!";
                                    }
                                    else
                                    {
                                        echo "Fehler beim Hinzufügen des Benutzers zur Liste.";
                                    }
                                    $stmt4->close();
                                }
                                else
                                {
                                    echo "Fehler beim Vorbereiten der SQL-Anweisung für das Hinzufügen.";
                                }
                            }
                            else
                            {
                                echo "Der Benutzer hat bereits Zugriff auf diese Liste.";
                            }
                            $stmt3->close();
                        }
                        else
                        {
                            echo "Fehler beim Überprüfen der bestehenden Zuordnung.";
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