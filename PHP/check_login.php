<?php
//Oliver
session_start();

if (isset($_SESSION['userID']))
{
    // Benutzer ist bereits angemeldet, weiterleiten zur ToDoPlus-Seite
    header('Location: ../ToDoPlus.html');
    exit();
}
else
{
    header('Location: ../Log-in.html');
    exit();
}