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

// Busca dados atuais
$sql = "SELECT * FROM user WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Processa atualização
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
            // Recarrega dados
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
?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - BerserkFit</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/perfil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
    <style>
        .edit-container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--cor-texto);
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
            font-family: inherit;
        }

        .header-edit {
            text-align: center;
            margin-bottom: 30px;
        }

        .header-edit img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 3px solid var(--cor-destaque);
            cursor: pointer;
        }

        .btn-upload {
            background: var(--cor-secundaria);
            color: var(--cor-texto);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            cursor: pointer;
            display: inline-block;
        }

        .btn-save {
            width: 100%;
            padding: 12px;
            background: var(--cor-destaque);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-save:hover {
            background: var(--cor-destaque-escuro);
        }

        .back-link {
            display: block;
            margin-bottom: 20px;
            color: var(--cor-destaque);
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <main class="main-content" style="padding-top: 40px;">
        <div class="edit-container">
            <a href="configuracoes.php" class="back-link"><i class="fas fa-arrow-left"></i> Voltar</a>

            <?php if ($mensagem != ""): ?>
                <div
                    style="padding: 10px; border-radius: 8px; margin-bottom: 20px; text-align: center; background: <?php echo strpos($mensagem, '✅') !== false ? '#d1fae5' : '#fee2e2'; ?>; color: <?php echo strpos($mensagem, '✅') !== false ? '#065f46' : '#991b1b'; ?>;">
                    <?php echo $mensagem; ?>
                </div>
            <?php endif; ?>

            <div class="header-edit">
                <?php $fotoDisplay = !empty($user['foto']) ? $user['foto'] : 'assets/fotos/default-user.png'; ?>
                <img src="<?php echo htmlspecialchars($fotoDisplay); ?>" alt="Foto Atual"
                    onclick="document.getElementById('foto').click()">
                <br>
                <span class="btn-upload" onclick="document.getElementById('foto').click()">Alterar Foto</span>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="foto" id="foto" style="display:none;" onchange="previewImage(this)">
                <input type="hidden" name="remover_foto" id="remover_foto" value="0">

                <div class="form-group">
                    <label>Nome Completo</label>
                    <input type="text" name="nome" value="<?php echo htmlspecialchars($user['nome']); ?>" required>
                </div>

                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1;">
                        <label>DDD</label>
                        <input type="text" name="ddd" value="<?php echo htmlspecialchars($user['ddd'] ?? ''); ?>">
                    </div>
                    <div class="form-group" style="flex: 2;">
                        <label>Telefone</label>
                        <input type="text" name="telefone"
                            value="<?php echo htmlspecialchars($user['telefone'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>E-mail (Não editável)</label>
                    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled
                        style="background: #f9fafb; color: #6b7280;">
                </div>

                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Data de Nascimento</label>
                        <input type="date" name="data_nascimento"
                            value="<?php echo htmlspecialchars($user['data_nascimento'] ?? ''); ?>">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Género</label>
                        <select name="genero">
                            <option value="">Selecione...</option>
                            <option value="Masculino" <?php echo ($user['genero'] == 'Masculino' ? 'selected' : ''); ?>
                                >Masculino</option>
                            <option value="Feminino" <?php echo ($user['genero'] == 'Feminino' ? 'selected' : ''); ?>
                                >Feminino</option>
                            <option value="Outro" <?php echo ($user['genero'] == 'Outro' ? 'selected' : ''); ?>>Outro
                            </option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-save">Guardar Alterações</button>
            </form>
        </div>
    </main>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    document.querySelector('.header-edit img').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>

</html>