<?php
require "ligacao.php";

session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['user_id'];
$mensagem = "";

// Busca os dados do usuário
$sql = "SELECT * FROM user WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>Erro: Usuário não encontrado.</p>";
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();

// Atualizar dados se o formulário for submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $ddd = $_POST['ddd'];
    $telefone = $_POST['telefone'];
    $data_nascimento = $_POST['data_nascimento'];
    $genero = $_POST['genero'];

    // Verifica se deve remover a foto
    $remover_foto = isset($_POST['remover_foto']) && $_POST['remover_foto'] == '1';
    
    // Upload da imagem
    $foto = isset($user['foto']) ? $user['foto'] : null; // mantém a antiga, caso não envie nova
    
    if ($remover_foto) {
        // Remove foto antiga se existir
        if (!empty($user['foto']) && file_exists($user['foto'])) {
            unlink($user['foto']);
        }
        $foto = null;
    } elseif (!empty($_FILES['foto']['name'])) {
        // Cria diretório se não existir
        $diretorio = "assets/fotos/";
        if (!file_exists($diretorio)) {
            mkdir($diretorio, 0777, true);
        }

        $fotoNome = time() . "_" . basename($_FILES['foto']['name']);
        $fotoTmp = $_FILES['foto']['tmp_name'];
        $caminhoFoto = $diretorio . $fotoNome;
        
        // Valida tipo de arquivo
        $extensao = strtolower(pathinfo($fotoNome, PATHINFO_EXTENSION));
        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($extensao, $extensoesPermitidas)) {
            if (move_uploaded_file($fotoTmp, $caminhoFoto)) {
                // Remove foto antiga se existir
                if (!empty($user['foto']) && file_exists($user['foto'])) {
                    unlink($user['foto']);
                }
                $foto = $caminhoFoto;
            } else {
                $mensagem = "❌ Erro ao fazer upload da foto.";
            }
        } else {
            $mensagem = "❌ Formato de arquivo não permitido. Use JPG, PNG ou GIF.";
        }
    }

    // Atualiza os dados (com ou sem foto)
    if (empty($mensagem)) {
        if ($remover_foto || $foto !== null) {
            // Atualiza com foto (nova ou remove)
            $update = "UPDATE user SET nome=?, ddd=?, telefone=?, data_nascimento=?, genero=?, foto=? WHERE id_user=?";
            $stmt = $conn->prepare($update);
            $stmt->bind_param("ssssssi", $nome, $ddd, $telefone, $data_nascimento, $genero, $foto, $id_user);
        } else {
            // Atualiza sem alterar foto
            $update = "UPDATE user SET nome=?, ddd=?, telefone=?, data_nascimento=?, genero=? WHERE id_user=?";
            $stmt = $conn->prepare($update);
            $stmt->bind_param("sssssi", $nome, $ddd, $telefone, $data_nascimento, $genero, $id_user);
        }
        
        if ($stmt->execute()) {
            $mensagem = "✅ Dados atualizados com sucesso!";
            // Atualiza os dados na variável para refletir na tela
            $user['nome'] = $nome;
            $user['ddd'] = $ddd;
            $user['telefone'] = $telefone;
            $user['data_nascimento'] = $data_nascimento;
            $user['genero'] = $genero;
            if ($remover_foto) {
                $user['foto'] = null;
                unset($_SESSION['user_foto']);
            } elseif ($foto !== null) {
                $user['foto'] = $foto;
                $_SESSION['user_foto'] = $foto;
            }
            // Atualiza sessão
            $_SESSION['user_nome'] = $nome;
        } else {
            $mensagem = "❌ Erro ao atualizar dados: " . $conn->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - BerserkFit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="estilo.css">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="dashboard.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body.login-page {
            background-image: none;
            overflow: auto;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        .main-login {
            width: 100%;
            box-sizing: border-box;
        }

        .perfil-container {
            max-width: 500px;
            margin: 40px auto;
            padding: 20px;
            width: 100%;
            box-sizing: border-box;
        }

        .perfil-box {
            background-color: var(--cor-fundo-login, #fff);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 100%;
            box-sizing: border-box;
        }

        .perfil-box h1 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 2em;
            margin-bottom: 30px;
            text-align: center;
            color: var(--cor-destaque, #0078d7);
        }

        .mensagem {
            text-align: center;
            margin-bottom: 20px;
            padding: 12px;
            border-radius: 5px;
            font-weight: 500;
        }

        .mensagem.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .mensagem.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .foto-section {
            text-align: center;
            margin-bottom: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .user-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--cor-destaque, #0078d7);
            margin-bottom: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }


        .foto-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 15px;
            width: 100%;
            max-width: 200px;
        }

        .file-input-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-input-wrapper input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-input-label {
            display: inline-block;
            padding: 10px 20px;
            background-color: var(--cor-destaque, #0078d7);
            color: white;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 0.9em;
        }

        .file-input-label:hover {
            background-color: var(--cor-destaque-hover, #005fa3);
        }

        .btn-remove {
            width: auto;
            padding: 6px 12px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 0.75em;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            align-self: center;
        }

        .btn-remove:hover {
            background-color: #c82333;
        }

        .btn-remove.hidden {
            display: none;
        }

        .input-group {
            margin-bottom: 25px;
        }

        .input-group label {
            display: block;
            font-family: 'Inter', sans-serif;
            font-size: 0.9em;
            color: var(--cor-texto-login, #333);
            margin-bottom: 10px;
        }

        .input-group input,
        .input-group select {
            width: 100%;
            padding: 12px 0;
            box-sizing: border-box;
            border: none;
            border-bottom: 1px solid var(--cor-input-borda, #ccc);
            background-color: transparent;
            color: var(--cor-texto-login, #333);
            font-size: 1em;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.3s ease;
        }

        .input-group input:focus,
        .input-group select:focus {
            outline: none;
            border-bottom-color: var(--cor-destaque, #0078d7);
        }

        .input-group input[readonly] {
            background-color: #f5f5f5;
            cursor: not-allowed;
        }

        .input-group-row {
            display: flex;
            gap: 10px;
        }

        .input-group-row .input-group {
            flex: 1;
        }

        .input-group-row .input-group:first-child {
            flex: 0 0 100px;
        }

        .btn-save {
            width: 100%;
            padding: 15px;
            background-color: var(--cor-destaque, #0078d7);
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 1em;
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }

        .btn-save:hover {
            background-color: var(--cor-destaque-hover, #005fa3);
        }

        .link {
            text-align: center;
            margin-top: 20px;
        }

        .link a {
            color: var(--cor-destaque, #0078d7);
            text-decoration: none;
            font-size: 0.9em;
        }

        .link a:hover {
            text-decoration: underline;
        }

        body.login-page {
            padding-bottom: 100px;
        }

        /* Remove animação hover da navbar */
        .navbar .nav-link {
            transition: none !important;
        }

        .navbar .nav-link:hover {
            background-color: transparent !important;
            color: var(--cor-secundaria) !important;
        }

        .navbar .nav-link.active:hover {
            background-color: var(--cor-intermedia) !important;
            color: var(--cor-destaque-escuro) !important;
        }

        /* Responsividade Mobile */
        @media (max-width: 768px) {
            body.login-page {
                padding: 0;
                margin: 0;
            }

            .main-login {
                padding: 20px 10px;
                min-height: 100vh;
            }

            .perfil-container {
                max-width: 100%;
                margin: 0;
                padding: 10px;
            }

            .perfil-box {
                padding: 20px 15px;
                border-radius: 10px;
                margin: 0;
            }

            .perfil-box h1 {
                font-size: 1.5em;
                margin-bottom: 20px;
            }

            .user-photo {
                width: 100px;
                height: 100px;
            }

            .foto-buttons {
                max-width: 100%;
            }

            .input-group-row {
                flex-direction: column;
                gap: 0;
            }

            .input-group-row .input-group:first-child {
                flex: 1;
            }

            .input-group {
                margin-bottom: 20px;
            }

            .input-group label {
                font-size: 0.85em;
            }

            .input-group input,
            .input-group select {
                font-size: 0.95em;
                padding: 10px 0;
            }

            .btn-save {
                padding: 12px;
                font-size: 0.95em;
            }

            .file-input-label {
                padding: 8px 16px;
                font-size: 0.85em;
            }

            .mensagem {
                font-size: 0.9em;
                padding: 10px;
            }
        }

        @media (max-width: 480px) {
            .perfil-box {
                padding: 15px 10px;
            }

            .perfil-box h1 {
                font-size: 1.3em;
            }

            .user-photo {
                width: 90px;
                height: 90px;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fotoInput = document.getElementById('foto');
            const previewFoto = document.getElementById('preview-foto');
            const btnRemoverFoto = document.getElementById('btn-remover-foto');
            const removerFotoInput = document.getElementById('remover_foto');
            
            const defaultFoto = previewFoto.getAttribute('data-default');
            
            // Função para mostrar/esconder botão de remover
            function atualizarBotaoRemover() {
                const temFotoPersonalizada = previewFoto.src !== '' && 
                                            !previewFoto.src.includes('default-user.png') &&
                                            previewFoto.style.display !== 'none';
                if (temFotoPersonalizada) {
                    btnRemoverFoto.classList.remove('hidden');
                } else {
                    btnRemoverFoto.classList.add('hidden');
                }
            }
            
            // Inicializa o estado do botão
            atualizarBotaoRemover();
            
            fotoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                
                if (file) {
                    // Verifica se é uma imagem
                    if (!file.type.match('image.*')) {
                        alert('Por favor, selecione apenas arquivos de imagem!');
                        fotoInput.value = ''; // Limpa o input
                        return;
                    }
                    
                    // Verifica o tamanho do arquivo (máximo 5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        alert('A imagem é muito grande! Por favor, selecione uma imagem menor que 5MB.');
                        fotoInput.value = ''; // Limpa o input
                        return;
                    }
                    
                    // Reseta o campo de remover foto
                    removerFotoInput.value = '0';
                    
                    // Cria um FileReader para ler o arquivo
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        // Mostra a imagem de preview
                        previewFoto.src = e.target.result;
                        previewFoto.style.display = 'block';
                        
                        // Atualiza botão de remover
                        atualizarBotaoRemover();
                    };
                    
                    reader.onerror = function() {
                        alert('Erro ao ler a imagem. Por favor, tente novamente.');
                        fotoInput.value = ''; // Limpa o input
                    };
                    
                    // Lê o arquivo como Data URL
                    reader.readAsDataURL(file);
                } else {
                    // Se não há arquivo selecionado, mantém o estado atual
                    // (não volta ao placeholder se já tinha foto)
                }
            });
            
            // Botão de remover foto
            btnRemoverFoto.addEventListener('click', function() {
                if (confirm('Tem certeza que deseja remover a foto?')) {
                    // Volta para a foto padrão
                    previewFoto.src = defaultFoto;
                    previewFoto.style.display = 'block';
                    
                    // Limpa o input de arquivo
                    fotoInput.value = '';
                    
                    // Marca para remover a foto no servidor
                    removerFotoInput.value = '1';
                    
                    // Esconde o botão de remover
                    atualizarBotaoRemover();
                }
            });
        });
    </script>
</head>
<body class="login-page">
    <main class="main-login">
        <div class="perfil-container">
            <div class="perfil-box">
                <h1>Meu Perfil</h1>
                
                <?php if ($mensagem != ""): ?>
                    <div class="mensagem <?php echo strpos($mensagem, '✅') !== false ? 'success' : 'error'; ?>">
                        <?php echo $mensagem; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="foto-section">
                        <?php 
                        $defaultFoto = 'assets/fotos/default-user.png';
                        $fotoUsuario = (!empty($user['foto']) && file_exists($user['foto'])) ? $user['foto'] : $defaultFoto;
                        ?>
                        <img src="<?php echo htmlspecialchars($fotoUsuario); ?>" class="user-photo" id="preview-foto" alt="Foto do Usuário" data-default="<?php echo htmlspecialchars($defaultFoto); ?>">
                        <div class="foto-buttons">
                            <div class="file-input-wrapper">
                                <input type="file" name="foto" id="foto" accept="image/*">
                                <label for="foto" class="file-input-label">
                                    <i class="fas fa-camera"></i> Alterar Foto
                                </label>
                            </div>
                            <input type="hidden" name="remover_foto" id="remover_foto" value="0">
                            <button type="button" class="btn-remove <?php echo (empty($user['foto']) || !file_exists($user['foto'])) ? 'hidden' : ''; ?>" id="btn-remover-foto">
                                <i class="fas fa-trash"></i> Remover Foto
                            </button>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="nome">Nome Completo</label>
                        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($user['nome'] ?? ''); ?>" required>
                    </div>

                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" readonly>
                    </div>

                    <div class="input-group-row">
                        <div class="input-group">
                            <label for="ddd">DDD</label>
                            <input type="text" id="ddd" name="ddd" value="<?php echo htmlspecialchars($user['ddd'] ?? ''); ?>" maxlength="3" pattern="[0-9]{2,3}">
                        </div>
                        <div class="input-group">
                            <label for="telefone">Telefone</label>
                            <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($user['telefone'] ?? ''); ?>" maxlength="9" pattern="[0-9]{8,9}">
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="data_nascimento">Data de Nascimento</label>
                        <input type="date" id="data_nascimento" name="data_nascimento" value="<?php echo htmlspecialchars($user['data_nascimento'] ?? ''); ?>">
                    </div>

                    <div class="input-group">
                        <label for="genero">Género</label>
                        <select id="genero" name="genero">
                            <option value="">Selecione...</option>
                            <option value="Masculino" <?php echo (isset($user['genero']) && $user['genero'] == 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
                            <option value="Feminino" <?php echo (isset($user['genero']) && $user['genero'] == 'Feminino') ? 'selected' : ''; ?>>Feminino</option>
                            <option value="Outro" <?php echo (isset($user['genero']) && $user['genero'] == 'Outro') ? 'selected' : ''; ?>>Outro</option>
                            <option value="Prefiro não dizer" <?php echo (isset($user['genero']) && $user['genero'] == 'Prefiro não dizer') ? 'selected' : ''; ?>>Prefiro não dizer</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-save">Salvar Alterações</button>
                </form>
            </div>
        </div>
    </main>

    <nav class="navbar">
        <a href="dashboard.php" class="nav-link"><i class="fas fa-home icon"></i> <span class="text">Home</span></a>
        <a href="#" class="nav-link"><i class="fas fa-dumbbell icon"></i> <span class="text">Treinos</span></a>
        <a href="#" class="nav-link"><i class="fas fa-chart-line icon"></i> <span class="text">Progresso</span></a>
        <a href="#" class="nav-link"><i class="fas fa-brain icon"></i> <span class="text">IA</span></a>
        <a href="perfil.php" class="nav-link active"><i class="fas fa-user icon"></i> <span class="text">Perfil</span></a>
    </nav>

</body>
</html>

