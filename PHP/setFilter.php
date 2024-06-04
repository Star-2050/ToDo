<?php
session_start();

/*
1 = Heute
2 = Demnächst (die nächsten 7 Tage)
3 = Alle 
*/

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if (isset($_POST['filter']))
    {
        $filter = intval($_POST['filter']);
        $_SESSION['todoFilter'] = $filter;
        echo "Filter set to $filter";
    }
    else
    {
        echo "No filter specified.";
    }
}
