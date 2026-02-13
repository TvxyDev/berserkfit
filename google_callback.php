<?php
require_once 'config_google.php';
require_once 'ligacao.php';

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);

        // Obter informações do utilizador
        $google_oauth = new Google_Service_Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();

        $email = $google_account_info->email;
        $name = $google_account_info->name;
        $google_id = $google_account_info->id;

        // Verificar se o utilizador já existe na nossa base de dados
        $sql = "SELECT id_user, nome, email, username FROM user WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        $is_new_user = false;

        if ($result->num_rows > 0) {
            // Utilizador já existe, fazer login
            $user = $result->fetch_assoc();
            $user_id = $user['id_user'];
            $username = $user['username'];
        } else {
            // Criar novo utilizador (sem senha e sem username, pois usa Google)
            $password_placeholder = password_hash(bin2hex(random_bytes(10)), PASSWORD_DEFAULT);
            $tipo_usuario = 'Usuario';
            $username_empty = NULL; // Username NULL para ser preenchido depois

            $sql_insert = "INSERT INTO user (nome, email, password_hash, tipo_usuario, username) VALUES (?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("sssss", $name, $email, $password_placeholder, $tipo_usuario, $username_empty);
            $stmt_insert->execute();
            $user_id = $stmt_insert->insert_id;
            $stmt_insert->close();
            $is_new_user = true;
            $username = NULL;
        }

        // Definir sessões (igual ao login.php)
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_nome'] = $name;
        $_SESSION['user_email'] = $email;

        // Buscar tipo de utilizador atualizado
        $sql_tipo = "SELECT COALESCE(tipo_usuario, 'Usuario') as tipo_usuario FROM user WHERE id_user = ?";
        $stmt_tipo = $conn->prepare($sql_tipo);
        $stmt_tipo->bind_param("i", $user_id);
        $stmt_tipo->execute();
        $res_tipo = $stmt_tipo->get_result();
        if ($row_tipo = $res_tipo->fetch_assoc()) {
            $_SESSION['user_tipo'] = $row_tipo['tipo_usuario'];
        }
        $stmt_tipo->close();

        // Se é novo usuário OU não tem username, redireciona para escolher username
        if ($is_new_user || empty($username)) {
            header("Location: escolher_username.php");
            exit;
        }

        // Verificar hábitos (Lógica do onboarding)
        $sql_check = "SELECT COUNT(*) as total FROM habito WHERE id_user = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("i", $user_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $row_check = $result_check->fetch_assoc();

        if ($row_check['total'] == 0) {
            header("Location: onboarding.php");
        } else {
            header("Location: dashboard.php");
        }
        exit;
    }
}

// Se algo falhar, volta ao login
header("Location: login.php");
exit;
?>