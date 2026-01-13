<?php
//Dane logowania i dostępu do bazy danych
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$baza = 'moja_strona';

//Połączenie z bazą
$link = mysqli_connect($dbhost, $dbuser, $dbpass);
if (!$link)
    echo '<b>Przerwane polączenie</b>';
if (!mysqli_select_db($link, $baza))
    echo 'nie wybrano bazy';

//Dane logowania do panelu admina CMS
$login = 'admin@uwm.pl';
$pass = '@dmin';
?>