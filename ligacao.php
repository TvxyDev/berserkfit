<?php
// Credenciais do FreeSQLDatabase.com
$servername = "sql7.freesqldatabase.com";
$username = "sql7808008";
$password = "FVYW7uwPyH";
$dbname = "sql7808008";
$port = 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Falhou a ligação: " . $conn->connect_error);
}
?>
