<?php
require "ligacao.php";

session_start();

// Verifica se o utilizador está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['user_id'];
$mensagem = "";

// Busca os dados do utilizador
$sql = "SELECT * FROM user WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>Erro: Utilizador não encontrado.</p>";
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();

// --- CALCULAR ESTATÍSTICAS ---
// 1. Total Concluído (Checklists)
$sql_xp = "SELECT COUNT(*) as total FROM checklist_diario c 
           JOIN habito h ON c.id_habito = h.id_habito 
           WHERE h.id_user = ? AND c.concluido = 1";
$stmt = $conn->prepare($sql_xp);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$res_xp = $stmt->get_result();
$total_concluido = $res_xp->fetch_assoc()['total'];
$stmt->close();

// 2. Metas
$sql_metas = "SELECT COUNT(*) as total FROM meta_usuario WHERE id_user = ?";
$stmt = $conn->prepare($sql_metas);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$res_metas = $stmt->get_result();
$total_metas = $res_metas->fetch_assoc()['total'];
$stmt->close();

// 3. Treinos Concluídos (Real)
$total_treinos = 0;
// Verifica se tabela treino existe (pelo arquivo SQL existe)
$sql_treinos = "SELECT COUNT(*) as total FROM treino WHERE id_user = ?";
$stmt = $conn->prepare($sql_treinos);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$res_treinos = $stmt->get_result();
$total_treinos = $res_treinos->fetch_assoc()['total'];
$stmt->close();

// 4. Mock Data (Coisas que não estão no DB ainda)
$plano = ucfirst($user['tipo_plano']);
$streak = 7; // Mock Example for "Day Streak" (Simulação)
$seguidores = 0;
$seguindo = 0;
$biografia = "Guerreiro em ascensão. Focado na disciplina e na conquista diária de objetivos."; // Mock Bio
$league_rank = "#12"; // Mock Monthly Rank
$current_league = "Liga " . ($plano === 'Gratuito' ? 'Spartan' : $plano);

// --- ATUALIZAÇÃO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $ddd = $_POST['ddd'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $data_nascimento = $_POST['data_nascimento'] ?? '';
    $genero = $_POST['genero'] ?? '';
    $remover_foto = isset($_POST['remover_foto']) && $_POST['remover_foto'] == '1';

    $foto = $user['foto'];

    if ($remover_foto) {
        if (!empty($user['foto']) && file_exists($user['foto']) && !strpos($user['foto'], 'default')) {
            unlink($user['foto']);
        }
        $foto = 'assets/fotos/default-user.png';
    } elseif (!empty($_FILES['foto']['name'])) {
        $diretorio = "assets/fotos/";
        if (!file_exists($diretorio))
            mkdir($diretorio, 0777, true);

        $fotoNome = time() . "_" . basename($_FILES['foto']['name']);
        $fotoTmp = $_FILES['foto']['tmp_name'];
        $caminhoFoto = $diretorio . $fotoNome;

        if (move_uploaded_file($fotoTmp, $caminhoFoto)) {
            $foto = $caminhoFoto;
        } else {
            $mensagem = "❌ Erro ao enviar foto.";
        }
    }

    if (empty($mensagem)) {
        $update = "UPDATE user SET nome=?, ddd=?, telefone=?, data_nascimento=?, genero=?, foto=? WHERE id_user=?";
        $stmt = $conn->prepare($update);
        $stmt->bind_param("ssssssi", $nome, $ddd, $telefone, $data_nascimento, $genero, $foto, $id_user);

        if ($stmt->execute()) {
            $mensagem = "✅ Dados atualizados!";
            $user['nome'] = $nome;
            $user['foto'] = $foto;
            $user['ddd'] = $ddd;
            $user['telefone'] = $telefone;
            $user['data_nascimento'] = $data_nascimento;
            $user['genero'] = $genero;
        } else {
            $mensagem = "❌ Erro ao atualizar.";
        }
    }
}

// Tratamento de display
$handle = "@" . strtolower(explode(' ', trim($user['nome']))[0]) . $user['id_user'];
$data_entrada = isset($user['data_registo']) ? date("F Y", strtotime($user['data_registo'])) : "Novembro 2024";

?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - BerserkFit</title>
    
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/perfil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
    <style>
        /* Estilos adicionais para as novas estatísticas */
        .public-stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 20px;
        }

        .stat-box {
            background: var(--cor-fundo);
            padding: 15px;
            border-radius: 12px;
            border: 1px solid var(--cor-secundaria);
            display: flex;
            align-items: center;
            gap: 15px;
            transition: transform 0.2s;
        }

        .stat-box:hover {
            transform: translateY(-2px);
            border-color: var(--cor-destaque);
        }

        .stat-box i {
            font-size: 1.5rem;
            color: var(--cor-destaque);
            width: 30px;
            text-align: center;
        }

        .stat-box div {
            display: flex;
            flex-direction: column;
        }

        .stat-box .stat-title {
            font-size: 0.8rem;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: 600;
        }

        .stat-box .stat-val {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--cor-texto);
        }

        .bio-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .bio-text {
            color: var(--cor-texto);
            font-size: 1rem;
            line-height: 1.6;
            font-style: italic;
            opacity: 0.8;
        }

        .hidden-panel {
            display: none;
        }

        /* Ajuste do botão secundário */
        .btn-cancel {
            background-color: transparent;
            color: #6b7280;
            border: 1px solid #d1d5db;
            margin-top: 10px;
        }

        .btn-cancel:hover {
            background-color: #f3f4f6;
            color: var(--cor-texto);
        }
    </style>
</head>

<body>



    <main class="main-content" style="padding-top: 40px;">

        <?php if ($mensagem != ""): ?>
            <div class="mensagem-float <?php echo strpos($mensagem, '✅') !== false ? 'success' : 'error'; ?>">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <div class="profile-container-grid">
            <!-- Coluna Esquerda: Cartão de Perfil -->
            <div class="profile-card">
                <div class="profile-header-img">
                    <?php
                    $fotoDisplay = !empty($user['foto']) ? $user['foto'] : 'assets/fotos/default-user.png';
                    ?>
                    <img src="<?php echo htmlspecialchars($fotoDisplay); ?>" alt="Foto de Perfil">
                    <!-- Botão Camera apenas abre o edit mode também -->
                    <button class="btn-edit-photo" onclick="toggleEditMode()"><i class="fas fa-camera"></i></button>
                </div>
                <h2><?php echo htmlspecialchars($user['nome']); ?></h2>
                <p class="handle"><?php echo $handle; ?></p>

                <div class="social-counts" style="margin-bottom: 20px; font-size: 0.95rem;">
                    <span style="margin-right: 15px; cursor: pointer;"><strong><?php echo $seguindo; ?></strong>
                        Seguindo</span>
                    <span style="cursor: pointer;"><strong><?php echo $seguidores; ?></strong> Seguidores</span>
                </div>

                <p class="joined"><i class="far fa-calendar-alt"></i> Membro desde <?php echo $data_entrada; ?></p>

                <button class="btn-save" onclick="toggleEditMode()" id="btn-toggle-text">Editar Perfil</button>
            </div>

            <!-- Coluna Direita: Informações Públicas (Padrão) -->
            <div class="settings-panel" id="public-panel">
                <h3><i class="fas fa-id-card"></i> Visão Geral</h3>

                <div class="bio-section">
                    <h4 style="margin-bottom: 10px; color: var(--cor-texto);">Sobre Mim</h4>
                    <p class="bio-text">"<?php echo $biografia; ?>"</p>
                </div>

                <h4 style="margin-bottom: 15px; color: var(--cor-texto);">Estatísticas de Guerreiro</h4>
                <div class="public-stats-grid">
                    <!-- Treinos Concluídos -->
                    <div class="stat-box">
                        <i class="fas fa-dumbbell"></i>
                        <div>
                            <span class="stat-val"><?php echo $total_treinos; ?></span>
                            <span class="stat-title">Treinos Feitos</span>
                        </div>
                    </div>

                    <!-- Day Streak -->
                    <div class="stat-box">
                        <i class="fas fa-fire" style="color: #ff9600;"></i>
                        <div>
                            <span class="stat-val"><?php echo $streak; ?></span>
                            <span class="stat-title">Dias Seguidos</span>
                        </div>
                    </div>

                    <!-- League Rank -->
                    <div class="stat-box">
                        <i class="fas fa-trophy" style="color: #ffc800;"></i>
                        <div>
                            <span class="stat-val"><?php echo $current_league; ?></span>
                            <span class="stat-title">Liga Atual</span>
                        </div>
                    </div>

                    <!-- Monthly Top -->
                    <div class="stat-box">
                        <i class="fas fa-medal" style="color: var(--cor-destaque);"></i>
                        <div>
                            <span class="stat-val"><?php echo $league_rank; ?></span>
                            <span class="stat-title">Top Mensal</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna Direita (Alternativa): Formulário de Edição (Oculto) -->
            <div class="settings-panel hidden-panel" id="edit-panel">
                <h3><i class="fas fa-user-cog"></i> Editar Detalhes</h3>
                <form method="POST" enctype="multipart/form-data" class="form-perfil">
                    <input type="file" name="foto" id="foto" style="display:none;">
                    <input type="hidden" name="remover_foto" id="remover_foto" value="0">

                    <div class="form-group">
                        <label>Nome Completo</label>
                        <input type="text" name="nome" value="<?php echo htmlspecialchars($user['nome']); ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>DDD</label>
                            <input type="text" name="ddd" value="<?php echo htmlspecialchars($user['ddd'] ?? ''); ?>">
                        </div>
                        <div class="form-group" style="flex:2;">
                            <label>Telefone</label>
                            <input type="text" name="telefone"
                                value="<?php echo htmlspecialchars($user['telefone'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>E-mail</label>
                        <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled
                            style="background: #eee;">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Nascimento</label>
                            <input type="date" name="data_nascimento"
                                value="<?php echo htmlspecialchars($user['data_nascimento'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Género</label>
                            <select name="genero">
                                <option value="">Selecione...</option>
                                <option value="Masculino" <?php echo ($user['genero'] == 'Masculino' ? 'selected' : ''); ?>>
                                    Masculino</option>
                                <option value="Feminino" <?php echo ($user['genero'] == 'Feminino' ? 'selected' : ''); ?>>
                                    Feminino</option>
                                <option value="Outro" <?php echo ($user['genero'] == 'Outro' ? 'selected' : ''); ?>>Outro
                                </option>
                            </select>
                        </div>
                    </div>

                    <p style="font-size: 0.8rem; color: #888;">Para alterar a foto, clique no ícone da câmera ao lado da
                        sua foto de perfil.</p>

                    <div class="form-actions">
                        <button type="submit" class="btn-save">Guardar Alterações</button>
                        <button type="button" class="btn-save btn-cancel" onclick="toggleEditMode()">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

    </main>

    <script>
        function toggleEditMode() {
            const publicPanel = document.getElementById('public-panel');
            const editPanel = document.getElementById('edit-panel');
            const btnText = document.getElementById('btn-toggle-text');

            publicPanel.classList.toggle('hidden-panel');
            editPanel.classList.toggle('hidden-panel');

            if (editPanel.classList.contains('hidden-panel')) {
                btnText.textContent = 'Editar Perfil';
            } else {
                btnText.textContent = 'Ver Estatísticas';
            }
        }

        document.querySelector('.btn-edit-photo').addEventListener('click', (e) => {
            e.preventDefault();
             const editPanel = document.getElementById('edit-panel');
            if (editPanel.classList.contains('hidden-panel')) {
                toggleEditMode();
            }
            document.getElementById('foto').click();
        });
    </script>
    <nav class="navbar">
        <a href="dashboard.php" class="nav-link"><i class="fas fa-home icon"></i> <span class="text">Início</span></a>
        <a href="#" class="nav-link"><i class="fas fa-dumbbell icon"></i> <span class="text">Treinos</span></a>
        <a href="progresso.php" class="nav-link"><i class="fas fa-chart-line icon"></i> <span
                class="text">Progresso</span></a>
        <a href="chatbot.php" class="nav-link"><i class="fas fa-robot icon"></i> <span class="text">Chatbot</span></a>
        <a href="perfil.php" class="nav-link active"><i class="fas fa-user icon"></i> <span
                class="text">Perfil</span></a>
    </nav>
</body>

</html>