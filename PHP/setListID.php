<?php
session_start();
//Oliver
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if (isset($_POST['listID']))
    {
        $listID = intval($_POST['listID']);
        $_SESSION['listID'] = $listID;
        echo "ListID set to " . $_SESSION['listID'];
        header('Location: ../ToDoPlus.html');
        exit();
    }
    else
    {
        echo "No listID provided.";
        header('Location: ../ToDoPlus.html');
        exit();
    }
}
else
{
    echo "Invalid request method.";
    header('Location: ../ToDoPlus.html');
    exit();
}