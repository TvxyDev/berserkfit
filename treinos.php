<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'ligacao.php';

$user_id = $_SESSION['user_id'];
$nome_usuario = $_SESSION['user_nome'] ?? 'Visitante';

// Buscar treinos do usuário
$sql = "SELECT t.*, COUNT(e.id_exercicio) as qtd_exercicios 
        FROM treino t 
        LEFT JOIN exercicio e ON t.id_treino = e.id_treino 
        WHERE t.id_user = ? 
        GROUP BY t.id_treino 
        ORDER BY t.data_criacao DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$treinos = [];
while ($row = $result->fetch_assoc()) {
    $treinos[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Treinos - BerserkFit</title>
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
            <div style="display: flex; gap: 15px; align-items: center;">
                <div class="streak-counter">
                    <i class="fa-solid fa-fire"></i>
                    <span>1</span>
                </div>
            </div>
        </div>
        <div class="header-greeting">
            <h2>Meus Treinos</h2>
            <p>Gerencie seus planos de treino personalizados.</p>
        </div>
    </header>

    <main>
        <section class="treinos-container fade-in-element">
            <div class="header-section">
                <h3>Seus Treinos Salvos</h3>
                <a href="criar_treino.php" class="btn-primary">
                    <i class="fas fa-plus"></i> Novo Treino
                </a>
            </div>

            <div id="lista-treinos" class="treinos-grid">
                <?php if (empty($treinos)): ?>
                    <div class="empty-state" style="grid-column: 1/-1;">
                        <i class="fas fa-dumbbell"></i>
                        <h3>Nenhum treino encontrado</h3>
                        <p>Comece criando seu primeiro treino personalizado com o botão acima!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($treinos as $treino): ?>
                        <div class="treino-card">
                            <div class="card-header">
                                <div>
                                    <h3 class="card-title"><?= htmlspecialchars($treino['nome_treino']) ?></h3>
                                    <div class="card-foco">
                                        <i class="fas fa-bullseye"></i> <?= htmlspecialchars($treino['foco']) ?>
                                    </div>
                                </div>
                                <div class="card-options">
                                    <a href="editar_treino.php?id=<?= $treino['id_treino'] ?>" class="btn-icon" title="Editar"
                                        style="color: var(--primary-color);">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="excluir_treino.php?id=<?= $treino['id_treino'] ?>" class="btn-icon btn-excluir"
                                        title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este treino?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-stats">
                                <div class="stat-item">
                                    <i class="fas fa-list-ul"></i>
                                    <span><?= $treino['qtd_exercicios'] ?> Exercícios</span>
                                </div>
                                <div class="stat-item">
                                    <i class="far fa-calendar-alt"></i>
                                    <span><?= date('d/m/Y', strtotime($treino['data_criacao'])) ?></span>
                                </div>
                            </div>
                            <a href="executar_treino.php?id=<?= $treino['id_treino'] ?>" class="btn-primary"
                                style="margin-top: 1rem; text-decoration: none; justify-content: center;">
                                <i class="fas fa-play"></i> Iniciar Treino
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
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
        // Efeito de carregamento suave
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.treino-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';

                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 50);
                }, index * 100);
            });
        });
    </script>
</body>

</html>