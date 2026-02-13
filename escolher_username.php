<?php
session_start();

// Verifica se o utilizador está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'ligacao.php';

$user_id = $_SESSION['user_id'];
$mensagem = "";
$erro = "";

// Verificar se o usuário já tem username
$sql_check = "SELECT username FROM user WHERE id_user = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

// Se já tem username válido, redireciona
if (!empty($user_data['username']) && $user_data['username'] !== NULL) {
    header("Location: onboarding.php");
    exit;
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');

    // Validações
    if (empty($username)) {
        $erro = "Por favor, escolhe um nome de utilizador.";
    } elseif (strlen($username) < 3) {
        $erro = "O nome de utilizador deve ter pelo menos 3 caracteres.";
    } elseif (strlen($username) > 20) {
        $erro = "O nome de utilizador não pode ter mais de 20 caracteres.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $erro = "O nome de utilizador só pode conter letras, números e underscore (_).";
    } else {
        // Verificar se o username já existe
        $sql_check_username = "SELECT id_user FROM user WHERE username = ?";
        $stmt = $conn->prepare($sql_check_username);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $erro = "Este nome de utilizador já está em uso. Por favor, escolhe outro.";
        } else {
            // Atualizar username
            $sql_update = "UPDATE user SET username = ? WHERE id_user = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("si", $username, $user_id);

            if ($stmt_update->execute()) {
                $_SESSION['username'] = $username;
                $stmt_update->close();
                $conn->close();
                header("Location: onboarding.php");
                exit;
            } else {
                $erro = "Erro ao guardar o nome de utilizador. Tenta novamente.";
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escolher Username - BerserkFit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&family=Inter:wght@400;700&display=swap"
        rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--cor-fundo);
            color: var(--cor-texto);
            font-family: var(--fonte-texto);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: linear-gradient(rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.5)), url("assets/noise.png");
        }

        .username-container {
            width: 100%;
            max-width: 500px;
            padding: 1rem;
            position: relative;
            z-index: 2;
        }

        .username-box {
            background: var(--cor-primaria);
            border-radius: 20px;
            padding: 3rem 2rem;
            box-shadow: 0 10px 40px rgba(28, 12, 59, 0.1);
            text-align: center;
            border: 1px solid var(--cor-secundaria);
            width: 100%;
        }

        .username-box h1 {
            color: var(--cor-destaque);
            font-family: var(--fonte-titulo);
            font-size: 2rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .username-box p {
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.6;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 25px;
            text-align: left;
            width: 100%;
        }

        .form-group label {
            display: block;
            color: var(--cor-texto);
            margin-bottom: 10px;
            font-weight: 600;
            font-family: var(--fonte-texto);
        }

        .form-group input {
            width: 100%;
            padding: 12px 0;
            border: none;
            border-bottom: 1px solid var(--cor-intermedia);
            background: transparent;
            color: var(--cor-texto);
            font-size: 1.1rem;
            transition: all 0.3s ease;
            font-family: var(--fonte-texto);
            border-radius: 0;
        }

        .form-group input::placeholder {
            color: #9ca3af;
        }

        .form-group input:focus {
            outline: none;
            border-bottom-color: var(--cor-destaque);
        }

        .username-hint {
            font-size: 0.85rem;
            color: #6b7280;
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            background-color: var(--cor-destaque);
            color: var(--cor-primaria);
            border: none;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
            font-family: var(--fonte-texto);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(28, 12, 59, 0.2);
        }

        .btn-submit:hover {
            background-color: var(--cor-destaque-escuro);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(14, 6, 28, 0.3);
        }

        .mensagem {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            justify-content: center;
        }

        .mensagem.error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #ef4444;
        }

        .username-preview {
            margin-top: 1.5rem;
            padding: 1rem;
            background: rgba(196, 181, 253, 0.1);
            border-radius: 10px;
            border: 1px dashed var(--cor-intermedia);
        }

        .username-preview span {
            color: var(--cor-destaque);
            font-weight: 700;
            font-size: 1.2rem;
            letter-spacing: 0.5px;
        }

        .icon-user {
            font-size: 4rem;
            color: var(--cor-destaque);
            margin-bottom: 1.5rem;
        }

        /* Floating Icons Effect from Login */
        .floating-icons-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
            overflow: hidden;
        }

        .floating-icon {
            position: absolute;
            font-size: 60px;
            color: var(--cor-destaque);
            animation: float 10s ease-in-out infinite;
            opacity: 0.05;
        }

        @keyframes float {
            0% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-100px) rotate(180deg);
            }

            100% {
                transform: translateY(0) rotate(360deg);
            }
        }

        .floating-icon:nth-child(1) {
            top: 20%;
            left: 15%;
            animation-delay: 0s;
            font-size: 80px;
        }

        .floating-icon:nth-child(2) {
            top: 60%;
            left: 5%;
            animation-delay: 2s;
        }

        .floating-icon:nth-child(3) {
            top: 10%;
            left: 85%;
            animation-delay: 4s;
            font-size: 50px;
        }

        .floating-icon:nth-child(4) {
            top: 80%;
            left: 90%;
            animation-delay: 6s;
        }
    </style>
</head>

<body class="login-page">
    <main class="main-login">
        <div class="username-container">
            <div class="username-box">
                <i class="fas fa-user-circle icon-user"></i>
                <h1>Escolhe o teu Username 🎯</h1>
                <p>Bem-vindo ao BerserkFit! Para começares, escolhe um nome de utilizador único que te represente.</p>
                escolhe um nome de utilizador único que te represente.</p>
                <?php if (!empty($erro)): ?>
                    <div class="mensagem error"><i class="fas fa-exclamation-circle"></i>
                        <?php echo htmlspecialchars($erro); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="escolher_username.php" id="formUsername">
                    <div class="form-group"><label for="username"><i class="fas fa-at"></i>Nome de Utilizador
                        </label><input type="text" id="username" name="username" placeholder="ex: guerreiro_fit"
                            required minlength="3" maxlength="20" pattern="[a-zA-Z0-9_]+" autocomplete="off">
                        <div class="username-hint"><i class="fas fa-info-circle"></i>3-20 caracteres,
                            apenas letras,
                            números e underscore (_) </div>
                    </div>
                    <div class="username-preview" id="preview" style="display: none;"><i
                            class="fas fa-eye"></i>Pré-visualização: <span id="previewText">@</span></div><button
                        type="submit" class="btn-submit"><i class="fas fa-check-circle"></i>Confirmar Username </button>
                </form>
            </div>
        </div>
    </main>
    <script>const usernameInput = document.getElementById('username');
        const preview = document.getElementById('preview');
        const previewText = document.getElementById('previewText');

        usernameInput.addEventListener('input', function () {
            const value = this.value.trim();

            if (value.length > 0) {
                preview.style.display = 'block';
                previewText.textContent = '@' + value;
            }

            else {
                preview.style.display = 'none';
            }
        });

        // Validação em tempo real
        usernameInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^a-zA-Z0-9_]/g, '');
        });
    </script>
</body>

</html>