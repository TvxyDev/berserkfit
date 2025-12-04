<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_nome = $_SESSION['user_nome'] ?? 'Guerreiro';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot - BerserkFit AI</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/chatbot.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"
        integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>

<body>
    <header class="fade-in-element">
        <div class="header-top">
            <h1 class="app-title">BerserkFit AI</h1>
            <div class="streak-counter">
                <i class="fa-solid fa-fire"></i>
                <span>1</span>
            </div>
        </div>
        <div class="header-greeting">
            <h2>Personal Trainer AI 💪</h2>
            <p>Seu treinador virtual está pronto para ajudar!</p>
        </div>
    </header>

    <main>
        <section id="chatbot-section" class="fade-in-element">
            <div class="chatbot-container">
                <div id="chat-box">
                    <div class="message bot-message">
                        Olá, <?php echo htmlspecialchars($user_nome); ?>! 👋<br>
                        Sou seu Personal Trainer virtual. 💪<br><br>
                        Estou aqui para criar o treino perfeito para você. Para começar, qual é o seu principal objetivo
                        hoje?<br>
                        <em>(Ex: perder peso, ganhar massa, condicionamento...)</em>
                    </div>
                </div>

                <div class="typing-indicator" id="typing-indicator">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>

                <div class="input-area">
                    <input type="text" id="user-input" placeholder="Digite sua mensagem..." autocomplete="off">
                    <button id="send-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </section>
    </main>

    <nav class="navbar">
        <a href="dashboard.php" class="nav-link"><i class="fas fa-home icon"></i> <span class="text">Home</span></a>
        <a href="#" class="nav-link"><i class="fas fa-dumbbell icon"></i> <span class="text">Treinos</span></a>
        <a href="progresso.php" class="nav-link"><i class="fas fa-chart-line icon"></i> <span
                class="text">Progresso</span></a>
        <a href="chatbot.php" class="nav-link active"><i class="fas fa-robot icon"></i> <span
                class="text">Chatbot</span></a>
        <a href="perfil.php" class="nav-link"><i class="fas fa-user icon"></i> <span class="text">Perfil</span></a>
    </nav>

    <script src="js/chatbot.js"></script>
</body>

</html>