<?php
require_once 'vendor/autoload.php';

// Configurações do Google Cloud Console
$clientID = '713407111734-pgaujer9j1n2ma468e52hqf1jqbihfbj.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'http://localhost/TESTEGOOGLE/berserkfit-main/berserkfit-main/google_callback.php';

// Criar o cliente do Google
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

// Iniciar sessão se ainda não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>