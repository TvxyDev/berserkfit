<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'ligacao.php';

$user_id = $_SESSION['user_id'];
$id_treino = $_GET['id'] ?? 0;

// Buscar treino e exercícios
$stmt = $conn->prepare("SELECT * FROM treino WHERE id_treino = ? AND id_user = ?");
$stmt->bind_param("ii", $id_treino, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$treino = $result->fetch_assoc();
$stmt->close();

if (!$treino) {
    header("Location: treinos.php");
    exit;
}

// Buscar exercícios
$stmt = $conn->prepare("SELECT * FROM exercicio WHERE id_treino = ? ORDER BY id_exercicio");
$stmt->bind_param("i", $id_treino);
$stmt->execute();
$result = $stmt->get_result();
$exercicios = [];
while ($row = $result->fetch_assoc()) {
    $exercicios[] = $row;
}
$stmt->close();

// Processar finalização do treino
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $duracao_minutos = intval($_POST['duracao_minutos'] ?? 0);
    $data_treino = date('Y-m-d H:i:s');

    // Atualizar campo data_treino na tabela treino (se quiser registrar última execução)
    // Ou criar tabela separada de histórico de treinos

    $conn->close();
    header("Location: treinos.php?concluido=1");
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executar Treino - BerserkFit</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/treinos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700;800&display=swap"
        rel="stylesheet">
    <style>
        .timer-container {
            background: var(--bg-card);
            border: 2px solid var(--primary-color);
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(196, 181, 253, 0.3);
        }

        .timer-display {
            font-size: 3rem;
            font-weight: 700;
            color: var(--cor-destaque);
            font-family: var(--fonte-titulo);
            margin: 1rem 0;
        }

        .timer-controls {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 1rem;
        }

        .btn-timer {
            padding: 0.8rem 2rem;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-start {
            background: #10b981;
            color: white;
        }

        .btn-pause {
            background: #f59e0b;
            color: white;
        }

        .btn-stop {
            background: #ef4444;
            color: white;
        }

        .exercicio-lista {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .exercicio-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s;
        }

        .exercicio-card.concluido {
            opacity: 0.6;
            background: rgba(196, 181, 253, 0.1);
        }

        .exercicio-checkbox {
            width: 30px;
            height: 30px;
            cursor: pointer;
            accent-color: var(--primary-color);
        }

        .exercicio-info {
            flex: 1;
        }

        .exercicio-nome {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--cor-destaque);
            margin-bottom: 0.5rem;
        }

        .exercicio-detalhes {
            color: var(--text-gray);
            font-size: 0.9rem;
        }

        .progresso-treino {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .progresso-bar-container {
            background: var(--cor-secundaria);
            height: 20px;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .progresso-bar-fill {
            background: linear-gradient(90deg, var(--primary-dark), var(--primary-color));
            height: 100%;
            transition: width 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--cor-destaque);
            font-weight: 600;
            font-size: 0.8rem;
        }
    </style>
</head>

<body>

    <header class="fade-in-element">
        <div class="header-top">
            <a href="treinos.php" style="color: var(--cor-destaque); text-decoration: none;">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <h1 class="app-title">BerserkFit AI</h1>
            <div></div>
        </div>
        <div class="header-greeting">
            <h2>
                <?= htmlspecialchars($treino['nome_treino']) ?>
            </h2>
            <p>Foco:
                <?= htmlspecialchars($treino['foco']) ?>
            </p>
        </div>
    </header>

    <main>
        <section class="treinos-container fade-in-element">

            <!-- Cronômetro -->
            <div class="timer-container">
                <h3>Tempo de Treino</h3>
                <div class="timer-display" id="timer">00:00:00</div>
                <div class="timer-controls">
                    <button class="btn-timer btn-start" id="btn-start">
                        <i class="fas fa-play"></i> Iniciar
                    </button>
                    <button class="btn-timer btn-pause" id="btn-pause" style="display: none;">
                        <i class="fas fa-pause"></i> Pausar
                    </button>
                    <button class="btn-timer btn-stop" id="btn-stop" style="display: none;">
                        <i class="fas fa-stop"></i> Finalizar
                    </button>
                </div>
            </div>

            <!-- Progresso -->
            <div class="progresso-treino">
                <h3>Progresso</h3>
                <p id="progresso-texto">0 de
                    <?= count($exercicios) ?> exercícios concluídos
                </p>
                <div class="progresso-bar-container">
                    <div class="progresso-bar-fill" id="progresso-bar" style="width: 0%;">0%</div>
                </div>
            </div>

            <!-- Lista de Exercícios -->
            <h3>Exercícios</h3>
            <div class="exercicio-lista">
                <?php foreach ($exercicios as $index => $ex): ?>
                    <div class="exercicio-card" data-index="<?= $index ?>">
                        <input type="checkbox" class="exercicio-checkbox" id="ex-<?= $ex['id_exercicio'] ?>">
                        <div class="exercicio-info">
                            <div class="exercicio-nome">
                                <?= htmlspecialchars($ex['nome_exercicio']) ?>
                            </div>
                            <div class="exercicio-detalhes">
                                <?= $ex['series'] ?> séries ×
                                <?= $ex['repeticoes'] ?> repetições
                                <?php if (!empty($ex['grupo_muscular'])): ?>
                                    •
                                    <?= htmlspecialchars($ex['grupo_muscular']) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Formulário oculto para enviar dados -->
            <form method="POST" id="form-finalizar" style="display: none;">
                <input type="hidden" name="duracao_minutos" id="duracao_minutos">
            </form>

        </section>
    </main>

    <script>
        let segundos = 0;
        let intervalo = null;
        let rodando = false;
        const totalExercicios = <?= count($exercicios) ?>;
        let concluidos = 0;

        const timerDisplay = document.getElementById('timer');
        const btnStart = document.getElementById('btn-start');
        const btnPause = document.getElementById('btn-pause');
        const btnStop = document.getElementById('btn-stop');
        const progressoTexto = document.getElementById('progresso-texto');
        const progressoBar = document.getElementById('progresso-bar');

        function formatarTempo(seg) {
            const h = Math.floor(seg / 3600).toString().padStart(2, '0');
            const m = Math.floor((seg % 3600) / 60).toString().padStart(2, '0');
            const s = (seg % 60).toString().padStart(2, '0');
            return `${h}:${m}:${s}`;
        }

        function atualizarTimer() {
            segundos++;
            timerDisplay.textContent = formatarTempo(segundos);
        }

        btnStart.addEventListener('click', () => {
            if (!rodando) {
                intervalo = setInterval(atualizarTimer, 1000);
                rodando = true;
                btnStart.style.display = 'none';
                btnPause.style.display = 'inline-block';
                btnStop.style.display = 'inline-block';
            }
        });

        btnPause.addEventListener('click', () => {
            clearInterval(intervalo);
            rodando = false;
            btnStart.style.display = 'inline-block';
            btnPause.style.display = 'none';
        });

        btnStop.addEventListener('click', () => {
            if (confirm('Deseja finalizar o treino?')) {
                clearInterval(intervalo);
                document.getElementById('duracao_minutos').value = Math.floor(segundos / 60);
                document.getElementById('form-finalizar').submit();
            }
        });

        // Marcar exercícios como concluídos
        document.querySelectorAll('.exercicio-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const card = this.closest('.exercicio-card');

                if (this.checked) {
                    card.classList.add('concluido');
                    concluidos++;
                } else {
                    card.classList.remove('concluido');
                    concluidos--;
                }

                const percentual = Math.round((concluidos / totalExercicios) * 100);
                progressoTexto.textContent = `${concluidos} de ${totalExercicios} exercícios concluídos`;
                progressoBar.style.width = percentual + '%';
                progressoBar.textContent = percentual + '%';
            });
        });
    </script>
</body>

</html>