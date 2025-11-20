<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'ligacao.php';

$admin_id = $_SESSION['user_id'];

// Verifica se o usuário é Admin
$sql_check = "SELECT tipo_usuario FROM user WHERE id_user = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: dashboard.php");
    exit;
}

$admin_data = $result->fetch_assoc();
$stmt->close();

// Verifica se é Admin
if (!isset($admin_data['tipo_usuario']) || $admin_data['tipo_usuario'] !== 'Admin') {
    header("Location: dashboard.php");
    exit;
}

// Buscar ID do usuário a visualizar
$user_id_view = intval($_GET['id'] ?? 0);

if ($user_id_view == 0) {
    header("Location: admin.php");
    exit;
}

// Buscar dados completos do usuário
$user_data = null;
$sql_user = "SELECT * FROM user WHERE id_user = ?";
$stmt = $conn->prepare($sql_user);
$stmt->bind_param("i", $user_id_view);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: admin.php");
    exit;
}

$user_data = $result->fetch_assoc();
$stmt->close();

// Buscar hábitos do usuário
$habitos = [];
$sql_habitos = "SELECT id_habito, descricao, tipo, meta_diaria FROM habito WHERE id_user = ? ORDER BY id_habito DESC";
$stmt = $conn->prepare($sql_habitos);
$stmt->bind_param("i", $user_id_view);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $habitos[] = $row;
}
$stmt->close();

// Buscar registros de água (últimos 30 dias)
$registros_agua = [];
$sql_agua = "SELECT data, SUM(quantidade) as total FROM agua WHERE id_user = ? AND data >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) GROUP BY data ORDER BY data DESC LIMIT 30";
$stmt = $conn->prepare($sql_agua);
$stmt->bind_param("i", $user_id_view);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $registros_agua[] = $row;
}
$stmt->close();

// Buscar registros de peso (últimos 30)
$registros_peso = [];
$sql_peso = "SELECT peso, data FROM peso WHERE id_user = ? ORDER BY data DESC LIMIT 30";
$stmt = $conn->prepare($sql_peso);
$stmt->bind_param("i", $user_id_view);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $registros_peso[] = $row;
}
$stmt->close();

// Buscar registros de alimentação (últimos 30)
$registros_alimentacao = [];
$sql_alimentacao = "SELECT calorias, refeicao, descricao, data FROM alimentacao WHERE id_user = ? ORDER BY data DESC LIMIT 30";
$stmt = $conn->prepare($sql_alimentacao);
$stmt->bind_param("i", $user_id_view);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $registros_alimentacao[] = $row;
}
$stmt->close();

// Estatísticas do usuário
$total_agua = 0;
$total_calorias = 0;
$peso_atual = null;
$peso_inicial = null;

if (!empty($registros_agua)) {
    foreach ($registros_agua as $reg) {
        $total_agua += floatval($reg['total']);
    }
}

if (!empty($registros_alimentacao)) {
    foreach ($registros_alimentacao as $reg) {
        $total_calorias += floatval($reg['calorias']);
    }
}

if (!empty($registros_peso)) {
    $peso_atual = floatval($registros_peso[0]['peso']);
    $peso_inicial = floatval(end($registros_peso)['peso']);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Usuário - Admin - BerserkFit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/admin_usuario.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&family=Inter:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <a href="admin.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Voltar ao Painel Admin
        </a>

        <div class="user-header">
            <h1><i class="fas fa-user"></i> Detalhes do Usuário</h1>
            
            <div class="user-header-flex">
                <div>
                    <?php 
                    $foto_usuario = (!empty($user_data['foto']) && file_exists($user_data['foto'])) 
                        ? $user_data['foto'] 
                        : 'assets/fotos/default-user.png';
                    ?>
                    <img src="<?php echo htmlspecialchars($foto_usuario); ?>" alt="Foto" class="user-photo-large">
                </div>
                <div class="user-info-grid">
                    <div class="info-item">
                        <label>Nome Completo</label>
                        <div class="value"><?php echo htmlspecialchars($user_data['nome']); ?></div>
                    </div>
                    <div class="info-item">
                        <label>Email</label>
                        <div class="value"><?php echo htmlspecialchars($user_data['email']); ?></div>
                    </div>
                    <div class="info-item">
                        <label>Telefone</label>
                        <div class="value">
                            <?php 
                            if (!empty($user_data['ddd']) && !empty($user_data['telefone'])) {
                                echo htmlspecialchars($user_data['ddd']) . " " . htmlspecialchars($user_data['telefone']);
                            } else {
                                echo "--";
                            }
                            ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <label>Data de Nascimento</label>
                        <div class="value">
                            <?php 
                            if (!empty($user_data['data_nascimento'])) {
                                echo date('d/m/Y', strtotime($user_data['data_nascimento']));
                            } else {
                                echo "--";
                            }
                            ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <label>Género</label>
                        <div class="value"><?php echo htmlspecialchars($user_data['genero'] ?? '--'); ?></div>
                    </div>
                    <div class="info-item">
                        <label>Data de Registo</label>
                        <div class="value">
                            <?php 
                            if (!empty($user_data['data_registo'])) {
                                try {
                                    // Tenta converter para timestamp
                                    if (is_string($user_data['data_registo'])) {
                                        $timestamp = strtotime($user_data['data_registo']);
                                        if ($timestamp !== false) {
                                            echo date('d/m/Y H:i', $timestamp);
                                        } else {
                                            echo date('d/m/Y', strtotime($user_data['data_registo']));
                                        }
                                    } else {
                                        echo date('d/m/Y H:i', $user_data['data_registo']);
                                    }
                                } catch (Exception $e) {
                                    echo htmlspecialchars($user_data['data_registo']);
                                }
                            } else {
                                echo "--";
                            }
                            ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <label>Tipo de Usuário</label>
                        <div class="value">
                            <span class="badge-tipo <?php echo strtolower($user_data['tipo_usuario'] ?? 'Usuario'); ?>">
                                <?php echo htmlspecialchars($user_data['tipo_usuario'] ?? 'Usuario'); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas do Usuário -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total de Hábitos</h3>
                <p class="stat-value"><?php echo count($habitos); ?></p>
            </div>
            <div class="stat-card">
                <h3>Água (30 dias)</h3>
                <p class="stat-value"><?php echo number_format($total_agua, 1); ?>L</p>
            </div>
            <div class="stat-card">
                <h3>Calorias (30 dias)</h3>
                <p class="stat-value"><?php echo number_format($total_calorias, 0); ?></p>
            </div>
            <div class="stat-card">
                <h3>Peso Atual</h3>
                <p class="stat-value">
                    <?php 
                    if ($peso_atual) {
                        echo number_format($peso_atual, 1) . " kg";
                    } else {
                        echo "--";
                    }
                    ?>
                </p>
            </div>
        </div>

        <!-- Hábitos/Desafios -->
        <div class="admin-section">
            <h2><i class="fas fa-tasks"></i> Hábitos e Desafios</h2>
            <?php if (!empty($habitos)): ?>
                <?php foreach ($habitos as $habito): ?>
                    <div class="habito-item">
                        <div class="habito-info">
                            <h4><?php echo htmlspecialchars($habito['descricao']); ?></h4>
                            <p>
                                <?php if (!empty($habito['tipo'])): ?>
                                    <i class="fas fa-tag"></i> <?php echo htmlspecialchars($habito['tipo']); ?>
                                <?php endif; ?>
                                <?php if (!empty($habito['meta_diaria'])): ?>
                                    | Meta: <?php echo $habito['meta_diaria']; ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-message">
                    Nenhum hábito criado ainda.
                </p>
            <?php endif; ?>
        </div>

        <!-- Registros de Água -->
        <div class="admin-section">
            <h2><i class="fas fa-tint"></i> Registros de Água (Últimos 30 dias)</h2>
            <?php if (!empty($registros_agua)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Quantidade (L)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registros_agua as $reg): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($reg['data'])); ?></td>
                                <td><?php echo number_format($reg['total'], 2); ?>L</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="empty-message">
                    Nenhum registro de água encontrado.
                </p>
            <?php endif; ?>
        </div>

        <!-- Registros de Peso -->
        <div class="admin-section">
            <h2><i class="fas fa-weight"></i> Registros de Peso (Últimos 30)</h2>
            <?php if (!empty($registros_peso)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Peso (kg)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registros_peso as $reg): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($reg['data'])); ?></td>
                                <td><?php echo number_format($reg['peso'], 1); ?> kg</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="empty-message">
                    Nenhum registro de peso encontrado.
                </p>
            <?php endif; ?>
        </div>

        <!-- Registros de Alimentação -->
        <div class="admin-section">
            <h2><i class="fas fa-utensils"></i> Registros de Alimentação (Últimos 30)</h2>
            <?php if (!empty($registros_alimentacao)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Refeição</th>
                            <th>Descrição</th>
                            <th>Calorias</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registros_alimentacao as $reg): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($reg['data'])); ?></td>
                                <td><?php echo htmlspecialchars($reg['refeicao']); ?></td>
                                <td><?php echo htmlspecialchars($reg['descricao']); ?></td>
                                <td><?php echo number_format($reg['calorias'], 0); ?> kcal</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="empty-message">
                    Nenhum registro de alimentação encontrado.
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

