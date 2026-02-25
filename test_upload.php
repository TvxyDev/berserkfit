<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Teste Upload Foto - BerserkFit</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #1a0f2e;
            color: white;
        }

        .profile-pic {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #7c3aed;
            margin: 20px auto;
            display: block;
        }

        .btn {
            background: #7c3aed;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: block;
            margin: 10px auto;
        }

        .btn:hover {
            background: #6d28d9;
        }

        input[type="file"] {
            margin: 20px auto;
            display: block;
        }

        .success {
            background: #10b981;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            display: none;
        }
    </style>
</head>

<body>
    <h1 style="text-align: center;">🔧 Teste de Upload de Foto</h1>

    <img id="preview" class="profile-pic" src="assets/fotos/default-user.png" alt="Preview">

    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="foto" id="foto" accept="image/*">
        <button type="submit" class="btn">💾 Guardar Foto</button>
    </form>

    <div class="success" id="message">✅ Foto atualizada com sucesso!</div>

    <script>
        // Preview imediato quando seleciona ficheiro
        document.getElementById('foto').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                console.log('Ficheiro selecionado:', file.name);
                const reader = new FileReader();
                reader.onload = function (event) {
                    document.getElementById('preview').src = event.target.result;
                    console.log('Preview atualizado!');
                };
                reader.readAsDataURL(file);
            }
        });
    </script>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['foto']['name'])) {
        $diretorio = "assets/fotos/";
        if (!file_exists($diretorio)) {
            mkdir($diretorio, 0777, true);
        }

        $fotoNome = time() . "_" . basename($_FILES['foto']['name']);
        $caminhoFoto = $diretorio . $fotoNome;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminhoFoto)) {
            echo "<script>document.getElementById('message').style.display='block';</script>";
            echo "<p style='text-align:center; color: #10b981;'>📁 Foto guardada em: $caminhoFoto</p>";
        } else {
            echo "<p style='text-align:center; color: #ef4444;'>❌ Erro ao guardar foto</p>";
        }
    }
    ?>
</body>

</html>