<?php
require "ligacao.php";

session_start();

// Verifica se o utilizador está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['user_id'];

// Busca os dados do utilizador
$sql = "SELECT tfa_secret, username, nome FROM user WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$tfa_ativo = !empty($user['tfa_secret']);
?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - BerserkFit</title>

    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/perfil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
    <style>
        .settings-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
        }

        .settings-header {
            margin-bottom: 30px;
        }

        .settings-header h1 {
            font-size: 2rem;
            color: var(--cor-texto);
            margin-bottom: 10px;
        }

        .settings-header p {
            color: #6b7280;
            font-size: 1rem;
        }

        .settings-section {
            background: var(--cor-fundo);
            border: 1px solid var(--cor-secundaria);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .settings-section:hover {
            border-color: var(--cor-destaque);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .settings-section h2 {
            font-size: 1.3rem;
            color: var(--cor-texto);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .settings-section h2 i {
            color: var(--cor-destaque);
        }

        .settings-section p {
            color: #6b7280;
            font-size: 0.95rem;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .settings-action {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.05), rgba(59, 130, 246, 0.05));
            border: 1px solid rgba(6, 182, 212, 0.2);
            border-radius: 8px;
            margin-top: 15px;
        }

        .settings-action-info {
            flex: 1;
        }

        .settings-action-info h3 {
            margin: 0 0 5px;
            color: var(--cor-texto);
            font-size: 1rem;
            font-weight: 600;
        }

        .settings-action-info p {
            margin: 0;
            font-size: 0.85rem;
            color: #888;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 10px;
        }

        .badge-success {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
        }

        .btn-primary {
            padding: 10px 20px;
            background: linear-gradient(135deg, #06b6d4, #3b82f6);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(6, 182, 212, 0.3);
        }

        .btn-secondary {
            padding: 10px 20px;
            background: transparent;
            color: var(--cor-texto);
            border: 1px solid var(--cor-secundaria);
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-secondary:hover {
            background: var(--cor-secundaria);
            transform: translateY(-2px);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--cor-destaque);
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            gap: 12px;
        }
    </style>
</head>

<body>

    <main class="main-content" style="padding-top: 40px;">
        <div class="settings-container">
            <a href="perfil.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Voltar ao Perfil
            </a>

            <div class="settings-header">
                <h1><i class="fas fa-cog"></i> Configurações</h1>
                <p>Gerencie as suas preferências e segurança da conta</p>
            </div>

            <!-- Seção de Segurança -->
            <div class="settings-section">
                <h2><i class="fas fa-shield-alt"></i> Segurança</h2>
                <p>Proteja a sua conta com recursos adicionais de segurança</p>

                <!-- 2FA -->
                <div class="settings-action">
                    <div class="settings-action-info">
                        <h3>
                            Autenticação de Dois Fatores (2FA)
                            <?php if ($tfa_ativo): ?>
                                <span class="badge badge-success">Ativo</span>
                            <?php else: ?>
                                <span class="badge badge-warning">Inativo</span>
                            <?php endif; ?>
                        </h3>
                        <p>
                            <?php if ($tfa_ativo): ?>
                                A sua conta está protegida com autenticação de dois fatores.
                            <?php else: ?>
                                Adicione uma camada extra de segurança à sua conta.
                            <?php endif; ?>
                        </p>
                    </div>
                    <a href="setup_2fa.php" class="btn-primary">
                        <?php echo $tfa_ativo ? 'Gerir 2FA' : 'Ativar 2FA'; ?>
                    </a>
                </div>

                <!-- Senha -->
                <div class="settings-action">
                    <div class="settings-action-info">
                        <h3>Alterar Palavra-passe</h3>
                        <p>Atualize a sua senha regularmente para manter a conta segura.</p>
                    </div>
                    <button class="btn-secondary" onclick="alert('Funcionalidade em desenvolvimento')">
                        Alterar Senha
                    </button>
                </div>
            </div>

            <!-- Seção de Conta -->
            <div class="settings-section">
                <h2><i class="fas fa-user-circle"></i> Conta</h2>
                <p>Gerencie as informações da sua conta</p>

                <div class="settings-action">
                    <div class="settings-action-info">
                        <h3>Dados Pessoais</h3>
                        <p>Edite o seu nome, email, telefone e outros dados pessoais.</p>
                    </div>
                    <a href="editar_perfil.php" class="btn-secondary">
                        Editar Perfil
                    </a>
                </div>
            </div>

            <!-- Seção de Privacidade -->
            <div class="settings-section">
                <h2><i class="fas fa-lock"></i> Privacidade</h2>
                <p>Controle quem pode ver suas informações</p>

                <div class="settings-action">
                    <div class="settings-action-info">
                        <h3>Perfil Público</h3>
                        <p>Controle a visibilidade do seu perfil e estatísticas.</p>
                    </div>
                    <button class="btn-secondary" onclick="alert('Funcionalidade em desenvolvimento')">
                        Configurar
                    </button>
                </div>
            </div>

            <!-- Seção de Notificações -->
            <div class="settings-section">
                <h2><i class="fas fa-bell"></i> Notificações</h2>
                <p>Escolha como deseja receber notificações</p>

                <div class="settings-action">
                    <div class="settings-action-info">
                        <h3>Preferências de Notificação</h3>
                        <p>Configure email, push e outras notificações.</p>
                    </div>
                    <button class="btn-secondary" onclick="alert('Funcionalidade em desenvolvimento')">
                        Configurar
                    </button>
                </div>
            </div>

            <!-- Sair -->
            <div class="settings-section" style="border-color: rgba(239, 68, 68, 0.3);">
                <h2 style="color: #ef4444;"><i class="fas fa-sign-out-alt"></i> Sair</h2>
                <p>Terminar a sessão no dispositivo atual</p>

                <div class="settings-action"
                    style="background: rgba(239, 68, 68, 0.05); border-color: rgba(239, 68, 68, 0.2);">
                    <div class="settings-action-info">
                        <h3 style="color: #ef4444;">Terminar Sessão</h3>
                        <p>Deseja sair da sua conta?</p>
                    </div>
                    <a href="logout.php" class="btn-primary"
                        style="background: #ef4444; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);">
                        Sair
                    </a>
                </div>
            </div>
        </div>
    </main>

    <nav class="navbar">
        <a href="dashboard.php" class="nav-link"><i class="fas fa-home icon"></i> <span class="text">Início</span></a>
        <a href="treinos.php" class="nav-link"><i class="fas fa-dumbbell icon"></i> <span
                class="text">Treinos</span></a>
        <a href="progresso.php" class="nav-link"><i class="fas fa-chart-line icon"></i> <span
                class="text">Progresso</span></a>
        <a href="chatbot.php" class="nav-link"><i class="fas fa-robot icon"></i> <span class="text">Chatbot</span></a>
        <a href="perfil.php" class="nav-link"><i class="fas fa-user icon"></i> <span class="text">Perfil</span></a>
    </nav>
</body>

</html>