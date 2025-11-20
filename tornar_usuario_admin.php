<?php
/**
 * Script para tornar um usuário Admin
 * 
 * INSTRUÇÕES:
 * 1. Execute o arquivo sql/adicionar_campo_tipo_usuario.sql no banco de dados primeiro
 * 2. Edite este arquivo e coloque o EMAIL do usuário que deseja tornar Admin
 * 3. Acesse este arquivo no navegador (ex: http://localhost/berserkfit-main/tornar_usuario_admin.php)
 * 4. Após usar, DELETE este arquivo por segurança!
 */

require 'ligacao.php';

// ⚠️ ALTERE AQUI O EMAIL DO USUÁRIO QUE DESEJA TORNAR ADMIN
$email_admin = 'admin@example.com'; // <-- COLOQUE O EMAIL AQUI

// Verifica se o campo tipo_usuario existe
try {
    $check = "SHOW COLUMNS FROM user LIKE 'tipo_usuario'";
    $result = $conn->query($check);
    
    if ($result->num_rows == 0) {
        // Campo não existe, criar
        $alter = "ALTER TABLE `user` ADD COLUMN `tipo_usuario` VARCHAR(20) DEFAULT 'Usuario' AFTER `genero`";
        $conn->query($alter);
        echo "<p>✅ Campo 'tipo_usuario' criado com sucesso!</p>";
    }
    
    // Buscar usuário pelo email
    $sql = "SELECT id_user, nome, email FROM user WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email_admin);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Tornar admin
        $update = "UPDATE user SET tipo_usuario = 'Admin' WHERE id_user = ?";
        $stmt2 = $conn->prepare($update);
        $stmt2->bind_param("i", $user['id_user']);
        
        if ($stmt2->execute()) {
            echo "<h2>✅ Sucesso!</h2>";
            echo "<p>O usuário <strong>" . htmlspecialchars($user['nome']) . "</strong> (" . htmlspecialchars($user['email']) . ") foi tornado Admin!</p>";
            echo "<p><a href='admin.php'>Acessar Painel Admin</a></p>";
        } else {
            echo "<p>❌ Erro ao atualizar usuário: " . $conn->error . "</p>";
        }
        $stmt2->close();
    } else {
        echo "<p>❌ Usuário com email '" . htmlspecialchars($email_admin) . "' não encontrado!</p>";
        echo "<p>Verifique se o email está correto.</p>";
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo "<p>❌ Erro: " . $e->getMessage() . "</p>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tornar Usuário Admin</title>
    <link rel="stylesheet" href="css/tornar_usuario_admin.css">
</head>
<body>
    <h1>Script de Configuração Admin</h1>
    <p><strong>⚠️ IMPORTANTE:</strong> Após usar este script, DELETE este arquivo por segurança!</p>
</body>
</html>

