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

// Verificar se já completou onboarding (já tem hábitos criados)
$sql_check = "SELECT COUNT(*) as total FROM habito WHERE id_user = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

// Se já tem hábitos, redireciona para dashboard
if ($row['total'] > 0) {
    header("Location: dashboard.php");
    exit;
}

// Processar seleção de nível
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nivel = $_POST['nivel'] ?? '';

    if (!empty($nivel)) {
        // Definir desafios/hábitos baseados no nível
        $desafios = [];

        switch ($nivel) {
            case 'iniciante':
                $desafios = [
                    ['descricao' => 'Beber 2L de água', 'tipo' => 'Saúde', 'meta_diaria' => 2.0],
                    ['descricao' => 'Fazer 10 flexões', 'tipo' => 'Exercício', 'meta_diaria' => 10],
                    ['descricao' => 'Fazer 20 polichinelos', 'tipo' => 'Exercício', 'meta_diaria' => 20],
                    ['descricao' => 'Caminhar 5.000 passos', 'tipo' => 'Exercício', 'meta_diaria' => 5000],
                ];
                break;

            case 'intermediario':
                $desafios = [
                    ['descricao' => 'Beber 2.5L de água', 'tipo' => 'Saúde', 'meta_diaria' => 2.5],
                    ['descricao' => 'Fazer 25 flexões', 'tipo' => 'Exercício', 'meta_diaria' => 25],
                    ['descricao' => 'Fazer 50 polichinelos', 'tipo' => 'Exercício', 'meta_diaria' => 50],
                    ['descricao' => 'Caminhar 8.000 passos', 'tipo' => 'Exercício', 'meta_diaria' => 8000],
                    ['descricao' => 'Correr 3km', 'tipo' => 'Exercício', 'meta_diaria' => 3],
                ];
                break;

            case 'avancado':
                $desafios = [
                    ['descricao' => 'Beber 3L de água', 'tipo' => 'Saúde', 'meta_diaria' => 3.0],
                    ['descricao' => 'Fazer 50 flexões', 'tipo' => 'Exercício', 'meta_diaria' => 50],
                    ['descricao' => 'Fazer 100 polichinelos', 'tipo' => 'Exercício', 'meta_diaria' => 100],
                    ['descricao' => 'Caminhar 12.000 passos', 'tipo' => 'Exercício', 'meta_diaria' => 12000],
                    ['descricao' => 'Correr 5km', 'tipo' => 'Exercício', 'meta_diaria' => 5],
                    ['descricao' => 'Treinar 45 minutos', 'tipo' => 'Exercício', 'meta_diaria' => 45],
                ];
                break;

            case 'spartano':
                $desafios = [
                    ['descricao' => 'Beber 3.5L de água', 'tipo' => 'Saúde', 'meta_diaria' => 3.5],
                    ['descricao' => 'Fazer 100 flexões', 'tipo' => 'Exercício', 'meta_diaria' => 100],
                    ['descricao' => 'Fazer 200 polichinelos', 'tipo' => 'Exercício', 'meta_diaria' => 200],
                    ['descricao' => 'Caminhar 15.000 passos', 'tipo' => 'Exercício', 'meta_diaria' => 15000],
                    ['descricao' => 'Correr 10km', 'tipo' => 'Exercício', 'meta_diaria' => 10],
                    ['descricao' => 'Treinar 60 minutos', 'tipo' => 'Exercício', 'meta_diaria' => 60],
                    ['descricao' => 'Fazer abdominais (3 séries de 20)', 'tipo' => 'Exercício', 'meta_diaria' => 60],
                ];
                break;
        }

        // Criar hábitos no banco de dados
        $sql_insert = "INSERT INTO habito (id_user, descricao, tipo, meta_diaria) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_insert);
        $criados = 0;

        foreach ($desafios as $desafio) {
            $stmt->bind_param("issd", $user_id, $desafio['descricao'], $desafio['tipo'], $desafio['meta_diaria']);
            if ($stmt->execute()) {
                $criados++;
            }
        }
        $stmt->close();

        // Definir meta de água baseada no nível
        $meta_agua = 3.0;
        switch ($nivel) {
            case 'iniciante':
                $meta_agua = 2.0;
                break;
            case 'intermediario':
                $meta_agua = 2.5;
                break;
            case 'avancado':
                $meta_agua = 3.0;
                break;
            case 'spartano':
                $meta_agua = 3.5;
                break;
        }

        // Salvar meta de água
        try {
            $sql_meta = "INSERT INTO meta_usuario (id_user, tipo, valor) VALUES (?, 'agua', ?) 
                        ON DUPLICATE KEY UPDATE valor = ?";
            $stmt = $conn->prepare($sql_meta);
            $stmt->bind_param("idd", $user_id, $meta_agua, $meta_agua);
            $stmt->execute();
            $stmt->close();
        } catch (Exception $e) {
            // Ignora se a tabela não existir ainda
        }

        if ($criados > 0) {
            // Redireciona para dashboard
            header("Location: dashboard.php");
            exit;
        } else {
            $mensagem = "❌ Erro ao criar desafios. Tenta novamente.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo - BerserkFit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/onboarding.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&family=Inter:wght@400;700&display=swap"
        rel="stylesheet">
</head>

<body class="login-page">
    <main class="main-login">
        <div class="onboarding-container">
            <div class="onboarding-box">
                <h1>Bem-vindo ao BerserkFit! 🎯</h1>
                <p>Seleciona o teu nível para criarmos um plano de desafios personalizado:</p>

                <?php if ($mensagem != ""): ?>
                    <div class="mensagem error">
                        <?php echo htmlspecialchars($mensagem); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="onboarding.php" id="formNivel">
                    <div class="niveis-grid">
                        <!-- Iniciante -->
                        <input type="radio" name="nivel" value="iniciante" id="iniciante" class="nivel-option" required>
                        <label for="iniciante" class="nivel-card">
                            <div class="nivel-icon">🌱</div>
                            <h3>Iniciante</h3>
                            <p>Ideal para começar</p>
                            <ul>
                                <li>2L de água</li>
                                <li>10 flexões</li>
                                <li>20 polichinelos</li>
                                <li>5.000 passos</li>
                            </ul>
                        </label>

                        <!-- Intermediário -->
                        <input type="radio" name="nivel" value="intermediario" id="intermediario" class="nivel-option">
                        <label for="intermediario" class="nivel-card">
                            <div class="nivel-icon">💪</div>
                            <h3>Intermediário</h3>
                            <p>Já tens experiência</p>
                            <ul>
                                <li>2.5L de água</li>
                                <li>25 flexões</li>
                                <li>50 polichinelos</li>
                                <li>8.000 passos</li>
                                <li>Correr 3km</li>
                            </ul>
                        </label>

                        <!-- Avançado -->
                        <input type="radio" name="nivel" value="avancado" id="avancado" class="nivel-option">
                        <label for="avancado" class="nivel-card">
                            <div class="nivel-icon">🔥</div>
                            <h3>Avançado</h3>
                            <p>Alto nível de fitness</p>
                            <ul>
                                <li>3L de água</li>
                                <li>50 flexões</li>
                                <li>100 polichinelos</li>
                                <li>12.000 passos</li>
                                <li>Correr 5km</li>
                                <li>45 min treino</li>
                            </ul>
                        </label>

                        <!-- Spartano -->
                        <input type="radio" name="nivel" value="spartano" id="spartano" class="nivel-option">
                        <label for="spartano" class="nivel-card">
                            <div class="nivel-icon">⚔️</div>
                            <h3>Spartano</h3>
                            <p>O máximo desafio</p>
                            <ul>
                                <li>3.5L de água</li>
                                <li>100 flexões</li>
                                <li>200 polichinelos</li>
                                <li>15.000 passos</li>
                                <li>Correr 10km</li>
                                <li>60 min treino</li>
                                <li>Abdominais</li>
                            </ul>
                        </label>
                    </div>

                    <button type="submit" class="btn-continuar" id="btnContinuar" disabled>
                        Continuar
                    </button>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Habilitar botão quando selecionar um nível
        const niveis = document.querySelectorAll('.nivel-option');
        const btnContinuar = document.getElementById('btnContinuar');

        niveis.forEach(nivel => {
            nivel.addEventListener('change', function () {
                if (this.checked) {
                    btnContinuar.disabled = false;
                }
            });
        });
    </script>
</body>

</html>