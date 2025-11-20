<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'ligacao.php';

$user_id = $_SESSION['user_id'];
$mensagem = "";

// Verifica se o usuário é Admin
$sql_check = "SELECT tipo_usuario FROM user WHERE id_user = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: dashboard.php");
    exit;
}

$user_data = $result->fetch_assoc();
$stmt->close();

// Verifica se é Admin
if (!isset($user_data['tipo_usuario']) || $user_data['tipo_usuario'] !== 'Admin') {
    header("Location: dashboard.php");
    exit;
}

// Buscar estatísticas gerais
$total_usuarios = 0;
$total_habitos = 0;
$total_registros_agua = 0;
$total_registros_peso = 0;

try {
    // Total de usuários
    $sql = "SELECT COUNT(*) as total FROM user";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        $total_usuarios = $row['total'];
    }
    
    // Total de hábitos
    $sql = "SELECT COUNT(*) as total FROM habito";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        $total_habitos = $row['total'];
    }
    
    // Total de registros de água
    $sql = "SELECT COUNT(*) as total FROM agua";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        $total_registros_agua = $row['total'];
    }
    
    // Total de registros de peso
    $sql = "SELECT COUNT(*) as total FROM peso";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        $total_registros_peso = $row['total'];
    }
} catch (Exception $e) {
    // Ignora erros se tabelas não existirem
}

// Buscar lista de usuários
$usuarios = [];
try {
    $sql = "SELECT id_user, nome, email, tipo_usuario, COALESCE(data_registo, NOW()) as data_registo FROM user ORDER BY data_registo DESC LIMIT 50";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
} catch (Exception $e) {
    // Ignora erro
}

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    if ($acao === 'alterar_tipo') {
        $user_id_alterar = intval($_POST['user_id'] ?? 0);
        $novo_tipo = $_POST['tipo_usuario'] ?? 'Usuario';
        
        // Não permite alterar o próprio tipo
        if ($user_id_alterar == $user_id) {
            $mensagem = "❌ Você não pode alterar seu próprio tipo de usuário!";
        } else {
            $update = "UPDATE user SET tipo_usuario = ? WHERE id_user = ?";
            $stmt = $conn->prepare($update);
            $stmt->bind_param("si", $novo_tipo, $user_id_alterar);
            if ($stmt->execute()) {
                $mensagem = "✅ Tipo de usuário atualizado com sucesso!";
                // Atualiza a lista
                foreach ($usuarios as &$u) {
                    if ($u['id_user'] == $user_id_alterar) {
                        $u['tipo_usuario'] = $novo_tipo;
                    }
                }
            } else {
                $mensagem = "❌ Erro ao atualizar tipo de usuário.";
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - BerserkFit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&family=Inter:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <a href="dashboard.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
        </a>

        <div class="admin-header">
            <h1><i class="fas fa-shield-alt"></i> Painel Administrativo</h1>
            <p>Gestão e Estatísticas do Sistema</p>
        </div>

        <?php if ($mensagem != ""): ?>
            <div class="mensagem <?php echo strpos($mensagem, '✅') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <!-- Estatísticas Gerais -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total de Usuários</h3>
                <p class="stat-value"><?php echo number_format($total_usuarios); ?></p>
            </div>
            <div class="stat-card">
                <h3>Total de Hábitos</h3>
                <p class="stat-value"><?php echo number_format($total_habitos); ?></p>
            </div>
            <div class="stat-card">
                <h3>Registros de Água</h3>
                <p class="stat-value"><?php echo number_format($total_registros_agua); ?></p>
            </div>
            <div class="stat-card">
                <h3>Registros de Peso</h3>
                <p class="stat-value"><?php echo number_format($total_registros_peso); ?></p>
            </div>
        </div>

        <!-- Gestão de Usuários -->
        <div class="admin-section">
            <h2><i class="fas fa-users"></i> Gestão de Usuários</h2>
            
            <?php if (!empty($usuarios)): ?>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Tipo</th>
                            <th>Data de Registo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo $usuario['id_user']; ?></td>
                                <td>
                                    <a href="admin_usuario.php?id=<?php echo $usuario['id_user']; ?>">
                                        <?php echo htmlspecialchars($usuario['nome']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td>
                                    <span class="badge <?php echo strtolower($usuario['tipo_usuario'] ?? 'Usuario'); ?>">
                                        <?php echo htmlspecialchars($usuario['tipo_usuario'] ?? 'Usuario'); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    if (!empty($usuario['data_registo'])) {
                                        try {
                                            // Tenta converter para timestamp
                                            if (is_string($usuario['data_registo'])) {
                                                $timestamp = strtotime($usuario['data_registo']);
                                                if ($timestamp !== false) {
                                                    echo date('d/m/Y', $timestamp);
                                                } else {
                                                    echo date('d/m/Y', strtotime($usuario['data_registo']));
                                                }
                                            } else {
                                                echo date('d/m/Y', $usuario['data_registo']);
                                            }
                                        } catch (Exception $e) {
                                            echo htmlspecialchars($usuario['data_registo']);
                                        }
                                    } else {
                                        echo "--";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if ($usuario['id_user'] != $user_id): ?>
                                        <form method="POST" class="form-inline" onsubmit="return confirm('Tem certeza que deseja alterar o tipo deste usuário?');">
                                            <input type="hidden" name="acao" value="alterar_tipo">
                                            <input type="hidden" name="user_id" value="<?php echo $usuario['id_user']; ?>">
                                            <select name="tipo_usuario" required>
                                                <option value="Usuario" <?php echo ($usuario['tipo_usuario'] ?? 'Usuario') == 'Usuario' ? 'selected' : ''; ?>>Usuario</option>
                                                <option value="Admin" <?php echo ($usuario['tipo_usuario'] ?? 'Usuario') == 'Admin' ? 'selected' : ''; ?>>Admin</option>
                                            </select>
                                            <button type="submit" class="btn-admin">
                                                <i class="fas fa-save"></i> Atualizar
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span style="color: #999; font-size: 0.9em;">Você</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; color: #999; padding: 20px;">
                    Nenhum usuário encontrado.
                </p>
            <?php endif; ?>
        </div>
    </div>

    <nav class="navbar">
        <a href="dashboard.php" class="nav-link"><i class="fas fa-home icon"></i> <span class="text">Home</span></a>
        <a href="#" class="nav-link"><i class="fas fa-dumbbell icon"></i> <span class="text">Treinos</span></a>
        <a href="progresso.php" class="nav-link"><i class="fas fa-chart-line icon"></i> <span class="text">Progresso</span></a>
        <a href="#" class="nav-link"><i class="fas fa-brain icon"></i> <span class="text">IA</span></a>
        <a href="perfil.php" class="nav-link"><i class="fas fa-user icon"></i> <span class="text">Perfil</span></a>
    </nav>
</body>
</html>

