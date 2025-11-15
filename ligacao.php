<?php
$servername = 'sql7.freesqldatabase.com';
$username = 'sql7808008';
$password = 'FVYW7uwPyH';
$dbname = 'sql7808008';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falhou a ligação: " . $conn->connect_error);
}
//echo "Ligação bem sucedida";

?>



