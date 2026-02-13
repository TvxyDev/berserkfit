<?php
/**
 * Funções para Autenticação de Dois Fatores (2FA)
 * BerserkFit - Sistema de 2FA usando Google Authenticator
 */

require_once __DIR__ . '/vendor/autoload.php';

use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\ImageChartsQRCodeProvider;

/**
 * Retorna uma instância do TwoFactorAuth
 */
function get_tfa_instance()
{
    // Usar ImageChartsQRCodeProvider para gerar QR Codes sem dependências locais complexas
    $qrProvider = new ImageChartsQRCodeProvider();
    return new TwoFactorAuth($qrProvider, 'BerserkFit');
}

/**
 * Gera um novo segredo 2FA e salva na base de dados
 * @param mysqli $conn Conexão com a base de dados
 * @param int $user_id ID do utilizador
 * @return string O segredo gerado
 */
function generate_tfa_secret($conn, $user_id)
{
    // Se $conn não for passado, tenta usar a global
    if (!$conn) {
        global $conn;
    }

    $tfa = get_tfa_instance();
    $secret = $tfa->createSecret();

    // Salvar o segredo na base de dados
    $stmt = $conn->prepare("UPDATE user SET tfa_secret = ? WHERE id_user = ?");
    $stmt->bind_param("si", $secret, $user_id);
    $stmt->execute();
    $stmt->close();

    return $secret;
}

/**
 * Gera a URL do QR Code para configuração no Google Authenticator
 * @param string $secret O segredo 2FA
 * @param string $username Nome do utilizador
 * @return string URL do QR Code
 */
function get_tfa_qr_code_url($secret, $username)
{
    $tfa = get_tfa_instance();
    return $tfa->getQRCodeImageAsDataUri($username, $secret);
}

/**
 * Verifica se o código fornecido é válido
 * @param string $secret O segredo 2FA do utilizador
 * @param string $code O código de 6 dígitos fornecido
 * @return bool True se o código for válido
 */
function verify_tfa_code($secret, $code)
{
    if (empty($secret) || empty($code))
        return false;
    $tfa = get_tfa_instance();
    return $tfa->verifyCode($secret, $code, 1); // 1 = discrepância de tempo (30s)
}

/**
 * Desativa o 2FA para um utilizador
 * @param mysqli $conn Conexão com a base de dados
 * @param int $user_id ID do utilizador
 */
function disable_tfa($conn, $user_id)
{
    // Se $conn não for passado, tenta usar a global
    if (!$conn) {
        global $conn;
    }

    $stmt = $conn->prepare("UPDATE user SET tfa_secret = NULL WHERE id_user = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
}

/**
 * Verifica se o utilizador tem 2FA ativo
 * @param mysqli $conn Conexão com a base de dados
 * @param int $user_id ID do utilizador
 * @return bool True se o 2FA estiver ativo
 */
function user_has_tfa_enabled($conn, $user_id)
{
    // Se $conn não for passado, tenta usar a global
    if (!$conn) {
        global $conn;
    }

    $stmt = $conn->prepare("SELECT tfa_secret FROM user WHERE id_user = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    return !empty($user['tfa_secret']);
}

/**
 * Registra ação do utilizador (log)
 * @param mysqli $conn Conexão com a base de dados
 * @param int $user_id ID do utilizador
 * @param string $action Descrição da ação
 */
function log_user_action($conn, $user_id, $action)
{
    error_log("User $user_id: $action");
}
