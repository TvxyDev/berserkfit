<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'ligacao.php';

$user_id = $_SESSION['user_id'];

// Processar formulário de criação
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_treino = $_POST['nome_treino'] ?? 'Treino Sem Nome';
    $foco = $_POST['foco'] ?? 'Geral';
    $data_criacao = date('Y-m-d');

    $conn->begin_transaction();

    try {
        // Inserir Treino
        $stmt = $conn->prepare("INSERT INTO treino (id_user, nome_treino, foco, data_criacao) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $nome_treino, $foco, $data_criacao);
        $stmt->execute();
        $id_treino = $conn->insert_id;
        $stmt->close();

        // Inserir Exercícios
        if (isset($_POST['exercicios']) && is_array($_POST['exercicios'])) {
            $stmt_ex = $conn->prepare("INSERT INTO exercicio (id_treino, nome_exercicio, series, repeticoes, grupo_muscular) VALUES (?, ?, ?, ?, ?)");

            foreach ($_POST['exercicios'] as $ex) {
                if (!empty($ex['nome'])) {
                    $nome = $ex['nome'];
                    $series = intval($ex['series'] ?? 3);
                    $repeticoes = intval($ex['repeticoes'] ?? 12);
                    $grupo = $ex['grupo_muscular'] ?? '';

                    $stmt_ex->bind_param("isiis", $id_treino, $nome, $series, $repeticoes, $grupo);
                    $stmt_ex->execute();
                }
            }
            $stmt_ex->close();
        }

        $conn->commit();
        header("Location: treinos.php?sucesso=1");
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        $erro = "Erro ao salvar treino.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Treino - BerserkFit</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/treinos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700;800&display=swap"
        rel="stylesheet">
</head>

<body>

    <header class="fade-in-element">
        <div class="header-top">
            <h1 class="app-title">BerserkFit AI</h1>
        </div>
        <div class="header-greeting">
            <h2>Criar Novo Treino</h2>
            <p>Adicione exercícios ao seu treino personalizado.</p>
        </div>
    </header>

    <main>
        <section class="treinos-container fade-in-element">
            <?php if (isset($erro)): ?>
                <div style="background: #fee; color: #c00; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                    <?= htmlspecialchars($erro) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="criar_treino.php" style="max-width: 800px; margin: 0 auto;">
                <div class="form-group">
                    <label for="nome_treino">Nome do Treino</label>
                    <input type="text" id="nome_treino" name="nome_treino" class="form-control"
                        placeholder="Ex: Peito e Tríceps - Hipertrofia" required>
                </div>

                <div class="form-group">
                    <label for="foco">Foco Principal</label>
                    <select id="foco" name="foco" class="form-control">
                        <option value="Hipertrofia">Hipertrofia</option>
                        <option value="Força">Força</option>
                        <option value="Resistência">Resistência</option>
                        <option value="Perda de Peso">Perda de Peso</option>
                        <option value="Flexibilidade">Flexibilidade</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Exercícios</label>
                    <div id="lista-exercicios" class="exercicios-list">
                        <!-- Primeiro exercício -->
                        <div class="exercicio-item">
                            <div class="exercicio-header">
                                <span class="exercicio-numero">Exercício #1</span>
                            </div>
                            <div class="form-group">
                                <input type="text" name="exercicios[0][nome]" class="form-control"
                                    placeholder="Nome do Exercício" required>
                            </div>
                            <div class="exercicio-row">
                                <input type="text" name="exercicios[0][grupo_muscular]" class="form-control"
                                    placeholder="Grupo Muscular (Opcional)">
                                <input type="number" name="exercicios[0][series]" class="form-control"
                                    placeholder="Séries" value="3">
                                <input type="number" name="exercicios[0][repeticoes]" class="form-control"
                                    placeholder="Reps" value="12">
                            </div>
                        </div>
                    </div>
                    <button type="button" id="btn-add-exercicio" class="btn-secondary"
                        style="margin-top: 1rem; width: 100%;">
                        <i class="fas fa-plus"></i> Adicionar Exercício
                    </button>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <a href="treinos.php" class="btn-secondary"
                        style="flex: 1; text-align: center; text-decoration: none; padding: 0.8rem;">
                        Cancelar
                    </a>
                    <button type="submit" class="btn-primary" style="flex: 1;">
                        Salvar Treino
                    </button>
                </div>
            </form>
        </section>
    </main>

    <nav class="navbar">
        <a href="dashboard.php" class="nav-link"><i class="fas fa-home icon"></i> <span class="text">Início</span></a>
        <a href="treinos.php" class="nav-link active"><i class="fas fa-dumbbell icon"></i> <span
                class="text">Treinos</span></a>
        <a href="progresso.php" class="nav-link"><i class="fas fa-chart-line icon"></i> <span
                class="text">Progresso</span></a>
        <a href="chatbot.php" class="nav-link"><i class="fas fa-robot icon"></i> <span class="text">Chatbot</span></a>
        <a href="perfil.php" class="nav-link"><i class="fas fa-user icon"></i> <span class="text">Perfil</span></a>
    </nav>

    <script>
        let exercicioCount = 1;
        document.getElementById('btn-add-exercicio').addEventListener('click', () => {
            const container = document.getElementById('lista-exercicios');
            const div = document.createElement('div');
            div.className = 'exercicio-item';
            div.innerHTML = `
                <div class="exercicio-header">
                    <span class="exercicio-numero">Exercício #${exercicioCount + 1}</span>
                    <button type="button" class="btn-remove" onclick="this.parentElement.parentElement.remove()">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="form-group">
                    <input type="text" name="exercicios[${exercicioCount}][nome]" class="form-control" 
                           placeholder="Nome do Exercício" required>
                </div>
                <div class="exercicio-row">
                    <input type="text" name="exercicios[${exercicioCount}][grupo_muscular]" class="form-control" 
                           placeholder="Grupo Muscular (Opcional)">
                    <input type="number" name="exercicios[${exercicioCount}][series]" class="form-control" 
                           placeholder="Séries" value="3">
                    <input type="number" name="exercicios[${exercicioCount}][repeticoes]" class="form-control" 
                           placeholder="Reps" value="12">
                </div>
            `;
            container.appendChild(div);
            exercicioCount++;
        });
    </script>
</body>

</html>