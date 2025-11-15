<?php
session_start();

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conexão
    require 'ligacao.php';

    // Pega dados do formulário
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Prepara SQL para buscar o usuário
    $sql = "SELECT id_user, nome, email, password_hash FROM user WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verifica a senha
        if (password_verify($senha, $user['password_hash'])) {
            // Login bem-sucedido
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['user_nome'] = $user['nome'];
            $_SESSION['user_email'] = $user['email'];
            
            // Redireciona para dashboard ou página principal
            header("Location: dashboard.php");
            exit;
        } else {
            $mensagem = "❌ Email ou senha incorretos!";
        }
    } else {
        $mensagem = "❌ Email ou senha incorretos!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BerserkFit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="estilo.css">
    <link rel="stylesheet" href="login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&family=Inter:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="login-page">
    <header>
        <nav>
            <div class="logotipo">
                <img src="assets/logotipo1.png" alt="Logotipo BerserkFit">
            </div>
            <ul>
                <li><a href="index.php#inicio">Início</a></li>
                <li><a href="index.php#funcionalidades">Funcionalidades</a></li>
                <li><a href="index.php#planos">Planos</a></li>
                <li><a href="index.php#sobre">Sobre</a></li>
                <li><a href="index.php#depoimentos">Depoimentos</a></li>
                <li><a href="index.php#contato">Contato</a></li>
            </ul>
        </nav>
    </header>

    <main class="main-login">
        <div class="floating-icons-container">
            <i class="fa-solid fa-dumbbell floating-icon" style="--i:1"></i>
            <i class="fa-solid fa-heart floating-icon" style="--i:2"></i>
            <i class="fa-solid fa-person-running floating-icon" style="--i:3"></i>
            <i class="fa-solid fa-bicycle floating-icon" style="--i:4"></i>
            <i class="fa-solid fa-medal floating-icon" style="--i:5"></i>
            <i class="fa-solid fa-fire-flame-simple floating-icon" style="--i:6"></i>
        </div>
        <div class="login-container">
            <div class="login-box">
                <h1>Bem-vindo de volta!</h1>
                <?php if ($mensagem != ""): ?>
                    <p class="mensagem" style="text-align: center; margin-bottom: 15px; color: red;"><?php echo $mensagem; ?></p>
                <?php endif; ?>
                <form method="POST" action="login.php">
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="input-group">
                        <label for="senha">Palavra-passe</label>
                        <input type="password" id="senha" name="senha" required>
                    </div>
                    <a href="#" class="forgot-password">Esqueceu a sua palavra-passe?</a>
                    <button type="submit" class="btn-signin">Entrar</button>
                    <button type="button" class="btn-google">
                        <img src="assets/google-icon.svg" alt="Google Icon"> Entrar com Google
                    </button>
                </form>
                <p class="signup-link">Não tem uma conta? <a href="registro.php">Crie uma conta</a></p>
            </div>
        </div>
    </main>
</body>
</html>

