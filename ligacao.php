<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "berserkfit";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falhou a ligação: " . $conn->connect_error);
}
//echo "Ligação bem sucedida";

?>

