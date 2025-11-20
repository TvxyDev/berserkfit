<?php
session_start();

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conexão
    require 'ligacao.php';

    // Pega dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $ddd = $_POST['ddd'];
    $telefone = $_POST['telefone'];
    $data_nascimento = $_POST['data_nascimento'];
    $genero = $_POST['genero'];

    // Valida se as senhas são iguais
    if ($senha !== $confirmar_senha) {
        $mensagem = "❌ As senhas não coincidem!";
    } else {
        // Gera hash da senha
        $password_hash = password_hash($senha, PASSWORD_DEFAULT);

        // Prepara SQL para evitar SQL injection
        // data_registo e tipo_plano são preenchidos automaticamente pelo banco
        $sql = "INSERT INTO user (nome, email, password_hash, ddd, telefone, data_nascimento, genero) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $nome, $email, $password_hash, $ddd, $telefone, $data_nascimento, $genero);

        if ($stmt->execute()) {
            $mensagem = "✅ Conta criada com sucesso! Agora faça login.";
            // Redireciona para login
            header("Location: login.php");
            exit;
        } else {
            // Verifica se é erro de email duplicado
            if ($conn->errno == 1062) {
                $mensagem = "❌ Este email já está registado!";
            } else {
                $mensagem = "❌ Erro: " . $conn->error;
            }
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - BerserkFit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/registro.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <script>
        function validarSenhas() {
            const senha = document.getElementById('senha').value;
            const confirmarSenha = document.getElementById('confirmar_senha').value;
            const errorMsg = document.getElementById('senha-error');
            const confirmarInput = document.getElementById('confirmar_senha');
            
            if (confirmarSenha !== '') {
                if (senha !== confirmarSenha) {
                    errorMsg.classList.add('show');
                    confirmarInput.style.borderBottomColor = 'red';
                    return false;
                } else {
                    errorMsg.classList.remove('show');
                    confirmarInput.style.borderBottomColor = '';
                    return true;
                }
            }
            return true;
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('senha').addEventListener('input', validarSenhas);
            document.getElementById('confirmar_senha').addEventListener('input', validarSenhas);
            
            document.querySelector('form').addEventListener('submit', function(e) {
                if (!validarSenhas()) {
                    e.preventDefault();
                    alert('As senhas não coincidem!');
                }
            });
        });
    </script>
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
                <h1>Crie a sua conta</h1>
                <?php if ($mensagem != ""): ?>
                    <p class="mensagem" style="text-align: center; margin-bottom: 15px; color: <?php echo strpos($mensagem, '✅') !== false ? 'green' : 'red'; ?>;"><?php echo $mensagem; ?></p>
                <?php endif; ?>
                <form method="POST" action="registro.php">
                    <div class="input-group">
                        <label for="nome">Nome completo</label>
                        <input type="text" id="nome" name="nome" required>
                    </div>
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="input-group" style="display: flex; gap: 10px;">
                        <div style="flex: 0 0 80px;">
                            <label for="ddd">DDD</label>
                            <input type="text" id="ddd" name="ddd" placeholder="21" maxlength="3" pattern="[0-9]{3}" required>
                        </div>
                        <div style="flex: 1;">
                            <label for="telefone">Telefone</label>
                            <input type="text" id="telefone" name="telefone" placeholder="987654321" maxlength="9" pattern="[0-9]{8,9}" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <label for="senha">Palavra-passe</label>
                        <input type="password" id="senha" name="senha" required>
                    </div>
                    <div class="input-group">
                        <label for="confirmar_senha">Confirmar Palavra-passe</label>
                        <input type="password" id="confirmar_senha" name="confirmar_senha" required>
                        <div class="senha-error" id="senha-error">As senhas não coincidem!</div>
                    </div>
                    <div class="input-group">
                        <label for="data_nascimento">Data de Nascimento</label>
                        <input type="date" id="data_nascimento" name="data_nascimento" required>
                    </div>
                    <div class="input-group">
                        <label for="genero">Género</label>
                        <select id="genero" name="genero" required>
                            <option value="">Selecione...</option>
                            <option value="Masculino">Masculino</option>
                            <option value="Feminino">Feminino</option>
                            <option value="Outro">Outro</option>
                            <option value="Prefiro não dizer">Prefiro não dizer</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-signin">Registar</button>
                </form>
                <p class="signup-link">Já tem uma conta? <a href="login.php">Entrar</a></p>
            </div>
        </div>
    </main>
</body>
</html>

