<?php
$servername = getenv('MYSQLHOST');
$username = getenv('MYSQLUSER');
$password = getenv('MYSQLPASSWORD');
$dbname = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT');

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falhou a ligação: " . $conn->connect_error);
}
//echo "Ligação bem sucedida";

?>


