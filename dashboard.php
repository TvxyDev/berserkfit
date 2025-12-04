<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'ligacao.php';

// Verifica se já completou onboarding (já tem hábitos criados)
$user_id = $_SESSION['user_id'];
$sql_check = "SELECT COUNT(*) as total FROM habito WHERE id_user = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

// Se não tem hábitos, redireciona para onboarding
if ($row['total'] == 0) {
    header("Location: onboarding.php");
    exit;
}

// Buscar dados de água de hoje
$agua_hoje = 0;
$agua_meta = 3.0; // Meta padrão de 3L
$sql_agua = "SELECT COALESCE(SUM(quantidade), 0) as total FROM agua WHERE id_user = ? AND data = CURDATE()";
$stmt = $conn->prepare($sql_agua);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $agua_hoje = floatval($row['total']);
}
$stmt->close();

// Buscar peso mais recente
$peso_atual = null;
$data_peso = null;
$sql_peso = "SELECT peso, data FROM peso WHERE id_user = ? ORDER BY data DESC LIMIT 1";
$stmt = $conn->prepare($sql_peso);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $peso_atual = floatval($row['peso']);
    $data_peso = $row['data'];
}
$stmt->close();

// Buscar calorias queimadas de hoje (assumindo que existe tabela treino ou exercicio)
$calorias_queimadas = 0;
$calorias_meta = 800; // Meta padrão
// Tentar buscar de uma tabela de treinos se existir
try {
    $sql_calorias = "SELECT COALESCE(SUM(calorias_queimadas), 0) as total FROM treino WHERE id_user = ? AND DATE(data_treino) = CURDATE()";
    $stmt = $conn->prepare($sql_calorias);
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $calorias_queimadas = floatval($row['total']);
        }
        $stmt->close();
    }
} catch (Exception $e) {
    // Tabela não existe ou campos diferentes, usar valor padrão
    $calorias_queimadas = 0;
}

// Buscar minutos de treino de hoje
$minutos_treino = 0;
$minutos_meta = 60; // Meta padrão de 60 minutos
try {
    $sql_minutos = "SELECT COALESCE(SUM(duracao_minutos), 0) as total FROM treino WHERE id_user = ? AND DATE(data_treino) = CURDATE()";
    $stmt = $conn->prepare($sql_minutos);
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $minutos_treino = intval($row['total']);
        }
        $stmt->close();
    }
} catch (Exception $e) {
    // Tabela não existe ou campos diferentes, usar valor padrão
    $minutos_treino = 0;
}

// Buscar horas de sono (assumindo que existe uma tabela ou campo para isso)
$horas_sono = 0;
$minutos_sono = 0;
$sono_total_minutos = 0;
$sono_meta_minutos = 450; // 7h30min = 450 minutos
try {
    $sql_sono = "SELECT horas, minutos FROM sono WHERE id_user = ? AND data = CURDATE()";
    $stmt = $conn->prepare($sql_sono);
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $horas_sono = intval($row['horas'] ?? 0);
            $minutos_sono = intval($row['minutos'] ?? 0);
            $sono_total_minutos = ($horas_sono * 60) + $minutos_sono;
        }
        $stmt->close();
    }
} catch (Exception $e) {
    // Tabela não existe, usar valor padrão
    $horas_sono = 0;
    $minutos_sono = 0;
}

// Verificar se é Admin (antes de fechar a conexão)
$is_admin = false;
try {
    $sql_admin = "SELECT COALESCE(tipo_usuario, 'Usuario') as tipo_usuario FROM user WHERE id_user = ?";
    $stmt_admin = $conn->prepare($sql_admin);
    $stmt_admin->bind_param("i", $user_id);
    $stmt_admin->execute();
    $result_admin = $stmt_admin->get_result();
    if ($row_admin = $result_admin->fetch_assoc()) {
        $is_admin = ($row_admin['tipo_usuario'] ?? 'Usuario') === 'Admin';
    }
    $stmt_admin->close();
} catch (Exception $e) {
    // Campo não existe ainda
}

$conn->close();

// Calcular percentuais
$agua_percentual = $agua_meta > 0 ? min(100, ($agua_hoje / $agua_meta) * 100) : 0;
$calorias_percentual = $calorias_meta > 0 ? min(100, ($calorias_queimadas / $calorias_meta) * 100) : 0;
$minutos_percentual = $minutos_meta > 0 ? min(100, ($minutos_treino / $minutos_meta) * 100) : 0;
$sono_percentual = $sono_meta_minutos > 0 ? min(100, ($sono_total_minutos / $sono_meta_minutos) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - BerserkFit</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700;800&display=swap"
        rel="stylesheet">
</head>

<body>
    <header class="fade-in-element">
        <div class="header-top">
            <h1 class="app-title">BerserkFit AI</h1>
            <div style="display: flex; gap: 15px; align-items: center;">
                <?php if ($is_admin): ?>
                    <a href="admin.php"
                        style="color: var(--cor-texto-claro); text-decoration: none; font-size: 0.9em; padding: 8px 15px; background: rgba(220, 53, 69, 0.3); border-radius: 20px; transition: background 0.3s;"
                        onmouseover="this.style.background='rgba(220, 53, 69, 0.5)'"
                        onmouseout="this.style.background='rgba(220, 53, 69, 0.3)'">
                        <i class="fas fa-shield-alt"></i> Admin
                    </a>
                <?php endif; ?>
                <div class="streak-counter">
                    <i class="fa-solid fa-fire"></i>
                    <span>1</span>
                </div>
            </div>
        </div>
        <div class="calendar">
            <?php
            // Calcular os últimos 7 dias
            $dias_semana = ['D', 'S', 'T', 'Q', 'Q', 'S', 'S'];
            $dias_semana_nomes = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];

            // Começar 6 dias atrás para mostrar 7 dias (incluindo hoje)
            for ($i = 6; $i >= 0; $i--) {
                $timestamp = strtotime("-$i days");
                $dia_semana_num = date('w', $timestamp); // 0 = Domingo, 6 = Sábado
                $dia_mes = date('d', $timestamp);
                $dia_semana_letra = $dias_semana[$dia_semana_num];
                $is_hoje = date('Y-m-d', $timestamp) === date('Y-m-d');
                ?>
                <div class="calendar-day <?php echo $is_hoje ? 'active' : ''; ?>">
                    <span><?php echo $dia_semana_letra; ?></span>
                    <span><?php echo $dia_mes; ?></span>
                </div>
            <?php } ?>
        </div>
        <div class="header-greeting">
            <h2>Bom dia, <?php echo htmlspecialchars($_SESSION['user_nome']); ?>!</h2>
            <p>Pronto para conquistar o dia?</p>
        </div>
    </header>

    <main>
        <section id="resumo-dia" class="fade-in-element">
            <h2>Resumo do Dia</h2>
            <div class="grade-resumo">
                <div class="card-resumo">
                    <h3>💧 Água ingerida</h3>
                    <p><?php echo number_format($agua_hoje, 1); ?>L / <?php echo $agua_meta; ?>L</p>
                    <div class="progresso-bar">
                        <div class="progresso" style="width: <?php echo $agua_percentual; ?>%;"></div>
                    </div>
                </div>
                <div class="card-resumo">
                    <h3>🔥 Calorias queimadas</h3>
                    <p><?php echo number_format($calorias_queimadas, 0); ?> kcal</p>
                    <div class="progresso-bar">
                        <div class="progresso" style="width: <?php echo $calorias_percentual; ?>%;"></div>
                    </div>
                </div>
                <div class="card-resumo">
                    <h3>⏱️ Minutos de treino</h3>
                    <p><?php echo $minutos_treino; ?> min</p>
                    <div class="progresso-bar">
                        <div class="progresso" style="width: <?php echo $minutos_percentual; ?>%;"></div>
                    </div>
                </div>
                <div class="card-resumo">
                    <h3>💤 Sono</h3>
                    <p><?php
                    if ($horas_sono > 0 || $minutos_sono > 0) {
                        echo $horas_sono . "h " . $minutos_sono . "min";
                    } else {
                        echo "--";
                    }
                    ?></p>
                    <div class="progresso-bar">
                        <div class="progresso" style="width: <?php echo $sono_percentual; ?>%;"></div>
                    </div>
                </div>
            </div>
        </section>

        <section id="dashboard-pastas" class="fade-in-element">
            <div class="grid-pastas">
                <!-- Pasta Objetivos -->
                <div class="pasta">
                    <div class="pasta-header">
                        <i class="fa-solid fa-arrows-left-right"></i>
                    </div>
                    <div class="pasta-content">
                        <h3>Objetivos</h3>
                        <p>4 itens</p>
                    </div>
                </div>

                <!-- Pasta Progresso -->
                <div class="pasta">
                    <div class="pasta-header">
                        <i class="fa-solid fa-arrows-left-right"></i>
                    </div>
                    <div class="pasta-content">
                        <h3>Progresso</h3>
                        <p>3 gráficos</p>
                    </div>
                </div>

                <!-- Card Peso Corporal -->
                <div class="card-grande">
                    <div class="card-grande-header">
                        <span>Body weight</span>
                        <i class="fa-solid fa-arrows-left-right"></i>
                    </div>
                    <div class="card-grande-body">
                        <?php if ($peso_atual): ?>
                            <span><?php echo number_format($peso_atual, 1); ?></span>
                        <?php else: ?>
                            <span>--</span>
                        <?php endif; ?>
                        <span class="unidade">kg</span>
                    </div>
                    <div class="card-grande-footer">
                        <?php if ($data_peso): ?>
                            <?php
                            $data_diff = (time() - strtotime($data_peso)) / 3600; // diferença em horas
                            if ($data_diff < 24) {
                                echo "Registrado há " . round($data_diff) . " horas";
                            } else {
                                $dias = round($data_diff / 24);
                                echo "Registrado há " . $dias . " dia" . ($dias > 1 ? "s" : "");
                            }
                            ?>
                        <?php else: ?>
                            <span>Nenhum registro ainda</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Card Dica -->
                <div class="card-pequeno">
                    <div class="pasta-header">
                        <i class="fa-solid fa-arrows-left-right"></i>
                    </div>
                    <div class="pasta-content">
                        <h3>Dica do Dia</h3>
                    </div>
                </div>

                <!-- Card Nutrição -->
                <div class="card-pequeno">
                    <div class="pasta-header">
                        <i class="fa-solid fa-arrows-left-right"></i>
                    </div>
                    <div class="pasta-content">
                        <h3>Nutrição</h3>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <nav class="navbar">
        <a href="dashboard.php" class="nav-link active"><i class="fas fa-home icon"></i> <span
                class="text">Home</span></a>
        <a href="#" class="nav-link"><i class="fas fa-dumbbell icon"></i> <span class="text">Treinos</span></a>
        <a href="progresso.php" class="nav-link"><i class="fas fa-chart-line icon"></i> <span
                class="text">Progresso</span></a>
        <a href="chatbot.php" class="nav-link"><i class="fas fa-robot icon"></i> <span class="text">Chatbot</span></a>
        <a href="perfil.php" class="nav-link"><i class="fas fa-user icon"></i> <span class="text">Perfil</span></a>
    </nav>

</body>

</html>