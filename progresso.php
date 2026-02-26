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

// Processar formulários
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'adicionar_agua') {
        $quantidade = floatval($_POST['quantidade'] ?? 0);
        $data = $_POST['data'] ?? date('Y-m-d');

        // Verifica se já existe registo para hoje
        $sql = "SELECT id, quantidade FROM agua WHERE id_user = ? AND data = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $data);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Atualiza registo existente
            $row = $result->fetch_assoc();
            $nova_quantidade = $row['quantidade'] + $quantidade;
            $update = "UPDATE agua SET quantidade = ? WHERE id = ?";
            $stmt2 = $conn->prepare($update);
            $stmt2->bind_param("di", $nova_quantidade, $row['id']);
            $stmt2->execute();
            $stmt2->close();
            $mensagem = "✅ Água adicionada com sucesso!";
        } else {
            // Cria novo registo
            $insert = "INSERT INTO agua (id_user, quantidade, data) VALUES (?, ?, ?)";
            $stmt2 = $conn->prepare($insert);
            $stmt2->bind_param("ids", $user_id, $quantidade, $data);
            if ($stmt2->execute()) {
                $mensagem = "✅ Água registada com sucesso!";
            } else {
                $mensagem = "❌ Erro ao registar água.";
            }
            $stmt2->close();
        }
        $stmt->close();
    } elseif ($acao === 'adicionar_peso') {
        $peso = floatval($_POST['peso'] ?? 0);
        $data = $_POST['data'] ?? date('Y-m-d');

        // Verifica se já existe registo para hoje
        $sql = "SELECT id FROM peso WHERE id_user = ? AND data = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $data);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Atualiza registo existente
            $row = $result->fetch_assoc();
            $update = "UPDATE peso SET peso = ? WHERE id = ?";
            $stmt2 = $conn->prepare($update);
            $stmt2->bind_param("di", $peso, $row['id']);
            $stmt2->execute();
            $stmt2->close();
            $mensagem = "✅ Peso atualizado com sucesso!";
        } else {
            // Cria novo registo
            $insert = "INSERT INTO peso (id_user, peso, data) VALUES (?, ?, ?)";
            $stmt2 = $conn->prepare($insert);
            $stmt2->bind_param("ids", $user_id, $peso, $data);
            if ($stmt2->execute()) {
                $mensagem = "✅ Peso registado com sucesso!";
            } else {
                $mensagem = "❌ Erro ao registar peso.";
            }
            $stmt2->close();
        }
        $stmt->close();
    } elseif ($acao === 'adicionar_alimentacao') {
        $calorias = floatval($_POST['calorias'] ?? 0);
        $refeicao = $_POST['refeicao'] ?? '';
        $descricao = $_POST['descricao'] ?? '';
        $data = $_POST['data'] ?? date('Y-m-d');

        $insert = "INSERT INTO alimentacao (id_user, calorias, refeicao, descricao, data) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert);
        $stmt->bind_param("idsss", $user_id, $calorias, $refeicao, $descricao, $data);
        if ($stmt->execute()) {
            $mensagem = "✅ Refeição registada com sucesso!";
        } else {
            $mensagem = "❌ Erro ao registar refeição.";
        }
        $stmt->close();
    } elseif ($acao === 'criar_habito') {
        $descricao = $_POST['descricao'] ?? '';
        $meta_diaria = !empty($_POST['meta_diaria']) ? floatval($_POST['meta_diaria']) : null;
        $tipo = $_POST['tipo'] ?? '';

        if (!empty($descricao)) {
            $insert = "INSERT INTO habito (id_user, descricao, meta_diaria, tipo) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert);
            $stmt->bind_param("isds", $user_id, $descricao, $meta_diaria, $tipo);
            if ($stmt->execute()) {
                $mensagem = "✅ Rotina criada com sucesso!";
            } else {
                $mensagem = "❌ Erro ao criar Rotina.";
            }
            $stmt->close();
        }
    } elseif ($acao === 'criar_checklist') {
        $id_habito = intval($_POST['id_habito'] ?? 0);
        $data = $_POST['data'] ?? date('Y-m-d');

        if ($id_habito > 0) {
            // Verifica se já existe checklist para este Rotina nesta data
            $check = "SELECT id_checklist FROM checklist_diario WHERE id_habito = ? AND data = ?";
            $stmt = $conn->prepare($check);
            $stmt->bind_param("is", $id_habito, $data);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                // Cria novo checklist
                $insert = "INSERT INTO checklist_diario (id_habito, data, concluido) VALUES (?, ?, 0)";
                $stmt2 = $conn->prepare($insert);
                $stmt2->bind_param("is", $id_habito, $data);
                if ($stmt2->execute()) {
                    $mensagem = "✅ Desafio adicionado com sucesso!";
                } else {
                    $mensagem = "❌ Erro ao criar desafio.";
                }
                $stmt2->close();
            } else {
                $mensagem = "⚠️ Este desafio já existe para esta data!";
            }
            $stmt->close();
        }
    } elseif ($acao === 'toggle_checklist') {
        $checklist_id = intval($_POST['checklist_id'] ?? 0);
        $concluido = intval($_POST['concluido'] ?? 0);

        $update = "UPDATE checklist_diario SET concluido = ? WHERE id_checklist = ?";
        $stmt = $conn->prepare($update);
        $stmt->bind_param("ii", $concluido, $checklist_id);
        if ($stmt->execute()) {
            $mensagem = $concluido ? "✅ Desafio marcado como completo!" : "✅ Desafio desmarcado!";
        } else {
            $mensagem = "❌ Erro ao atualizar desafio.";
        }
        $stmt->close();
    } elseif ($acao === 'deletar_checklist') {
        $checklist_id = intval($_POST['checklist_id'] ?? 0);

        $delete = "DELETE FROM checklist_diario WHERE id_checklist = ?";
        $stmt = $conn->prepare($delete);
        $stmt->bind_param("i", $checklist_id);
        if ($stmt->execute()) {
            $mensagem = "✅ Desafio removido com sucesso!";
        } else {
            $mensagem = "❌ Erro ao remover desafio.";
        }
        $stmt->close();
    } elseif ($acao === 'deletar_habito') {
        $habito_id = intval($_POST['habito_id'] ?? 0);

        // Primeiro remove os checklists relacionados
        $delete_checklists = "DELETE FROM checklist_diario WHERE id_habito = ?";
        $stmt = $conn->prepare($delete_checklists);
        $stmt->bind_param("i", $habito_id);
        $stmt->execute();
        $stmt->close();

        // Depois remove o Rotina
        $delete = "DELETE FROM habito WHERE id_habito = ? AND id_user = ?";
        $stmt = $conn->prepare($delete);
        $stmt->bind_param("ii", $habito_id, $user_id);
        if ($stmt->execute()) {
            $mensagem = "✅ Rotina removida com sucesso!";
        } else {
            $mensagem = "❌ Erro ao remover Rotina.";
        }
        $stmt->close();
    } elseif ($acao === 'editar_meta_agua') {
        $nova_meta = floatval($_POST['meta_agua'] ?? 3.0);

        try {
            // Verifica se já existe meta para o usuário
            $check = "SELECT id FROM meta_usuario WHERE id_user = ? AND tipo = 'agua'";
            $stmt = $conn->prepare($check);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Atualiza meta existente
                $update = "UPDATE meta_usuario SET valor = ? WHERE id_user = ? AND tipo = 'agua'";
                $stmt2 = $conn->prepare($update);
                $stmt2->bind_param("di", $nova_meta, $user_id);
                if ($stmt2->execute()) {
                    $mensagem = "✅ Meta de água atualizada com sucesso!";
                } else {
                    $mensagem = "❌ Erro ao atualizar meta. Certifique-se de que a tabela 'meta_usuario' existe.";
                }
                $stmt2->close();
            } else {
                // Cria nova meta
                $insert = "INSERT INTO meta_usuario (id_user, tipo, valor) VALUES (?, 'agua', ?)";
                $stmt2 = $conn->prepare($insert);
                $stmt2->bind_param("id", $user_id, $nova_meta);
                if ($stmt2->execute()) {
                    $mensagem = "✅ Meta de água definida com sucesso!";
                } else {
                    $mensagem = "❌ Erro ao definir meta. Certifique-se de que a tabela 'meta_usuario' existe.";
                }
                $stmt2->close();
            }
            $stmt->close();
        } catch (Exception $e) {
            $mensagem = "❌ Erro: A tabela 'meta_usuario' não existe. Execute o arquivo 'sql/criar_tabela_meta.sql' no banco de dados.";
        }
    }
}

// Buscar dados de água de hoje
$agua_hoje = 0;
$agua_meta = 3.0; // Meta padrão de 3L

// Buscar meta personalizada do usuário
try {
    $sql_meta = "SELECT valor FROM meta_usuario WHERE id_user = ? AND tipo = 'agua'";
    $stmt = $conn->prepare($sql_meta);
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $agua_meta = floatval($row['valor']);
        }
        $stmt->close();
    }
} catch (Exception $e) {
    // Tabela não existe ainda, usar valor padrão
    $agua_meta = 3.0;
}

// Buscar consumo de hoje
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

// Buscar calorias de hoje
$calorias_hoje = 0;
$sql_calorias = "SELECT COALESCE(SUM(calorias), 0) as total FROM alimentacao WHERE id_user = ? AND data = CURDATE()";
$stmt = $conn->prepare($sql_calorias);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $calorias_hoje = floatval($row['total']);
}
$stmt->close();

// Buscar Rotinas do usuário
$habitos = [];
$sql_habitos = "SELECT id_habito, descricao, meta_diaria, tipo FROM habito WHERE id_user = ? ORDER BY id_habito DESC";
$stmt = $conn->prepare($sql_habitos);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $habitos[] = $row;
}
$stmt->close();

// Buscar checklists diários de hoje (com JOIN para pegar dados do Rotina)
$checklists = [];
$data_hoje = date('Y-m-d');
$sql_checklist = "SELECT c.id_checklist, c.id_habito, c.data, c.concluido, h.descricao, h.meta_diaria, h.tipo 
                  FROM checklist_diario c 
                  INNER JOIN habito h ON c.id_habito = h.id_habito 
                  WHERE h.id_user = ? AND c.data = ? 
                  ORDER BY c.id_checklist DESC";
$stmt = $conn->prepare($sql_checklist);
$stmt->bind_param("is", $user_id, $data_hoje);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $checklists[] = $row;
}
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progresso - BerserkFit</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/progresso.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode"></script>
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
            <h2>Meu Progresso</h2>
            <p>Acompanha a tua evolução diária</p>
        </div>
    </header>

    <main>
        <div class="progresso-container">
            <?php if ($mensagem != ""): ?>
                <div class="mensagem <?php echo strpos($mensagem, '✅') !== false ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>

            <!-- Seção Água -->
            <div class="categoria-item fade-in-element">
                <div class="categoria-header" onclick="toggleCategoria(this)">
                    <h3><i class="fas fa-tint"></i> Água</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="categoria-content">
                    <div class="grid-progresso">
                        <div class="card-progresso">
                            <h4>Consumo de Hoje</h4>
                            <div class="valor-destaque">
                                <?php echo number_format($agua_hoje, 1); ?><span class="unidade">L</span>
                            </div>
                            <div class="progresso-bar">
                                <div class="progresso"
                                    style="width: <?php echo min(100, ($agua_meta > 0 ? ($agua_hoje / $agua_meta) * 100 : 0)); ?>%;">
                                </div>
                            </div>
                            <div class="meta-header">
                                <div class="progresso-percentual">
                                    Meta: <?php echo $agua_meta; ?>L
                                    (<?php echo number_format(($agua_meta > 0 ? ($agua_hoje / $agua_meta) * 100 : 0), 0); ?>%)
                                </div>
                                <button type="button" class="btn-editar-meta" onclick="abrirModalMeta()">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                            </div>
                        </div>
                        <div class="card-progresso">
                            <h4>Adicionar Água</h4>
                            <form method="POST" class="form-progresso">
                                <input type="hidden" name="acao" value="adicionar_agua">
                                <div class="form-group">
                                    <label for="quantidade_agua">Quantidade (L)</label>
                                    <input type="number" id="quantidade_agua" name="quantidade" step="0.1" min="0"
                                        max="5" value="0.5" required>
                                </div>
                                <div class="form-group">
                                    <label for="data_agua">Data</label>
                                    <input type="date" id="data_agua" name="data" value="<?php echo date('Y-m-d'); ?>"
                                        required>
                                </div>
                                <button type="submit" class="btn-adicionar">
                                    <i class="fas fa-plus"></i> Adicionar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção Peso -->
            <div class="categoria-item fade-in-element">
                <div class="categoria-header" onclick="toggleCategoria(this)">
                    <h3><i class="fas fa-weight"></i> Peso</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="categoria-content">
                    <div class="grid-progresso">
                        <div class="card-progresso">
                            <h4>Peso Atual</h4>
                            <?php if ($peso_atual): ?>
                                <div class="valor-destaque">
                                    <?php echo number_format($peso_atual, 1); ?><span class="unidade">kg</span>
                                </div>
                                <div class="progresso-percentual">
                                    Registado em: <?php echo date('d/m/Y', strtotime($data_peso)); ?>
                                </div>
                            <?php else: ?>
                                <div class="valor-destaque">
                                    --<span class="unidade">kg</span>
                                </div>
                                <div class="progresso-percentual">
                                    Nenhum registo ainda
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-progresso">
                            <h4>Registar Peso</h4>
                            <form method="POST" class="form-progresso">
                                <input type="hidden" name="acao" value="adicionar_peso">
                                <div class="form-group">
                                    <label for="peso">Peso (kg)</label>
                                    <input type="number" id="peso" name="peso" step="0.1" min="0" max="500" required>
                                </div>
                                <div class="form-group">
                                    <label for="data_peso">Data</label>
                                    <input type="date" id="data_peso" name="data" value="<?php echo date('Y-m-d'); ?>"
                                        required>
                                </div>
                                <button type="submit" class="btn-adicionar">
                                    <i class="fas fa-save"></i> Registar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção Alimentação -->
            <div class="categoria-item fade-in-element">
                <div class="categoria-header" onclick="toggleCategoria(this)">
                    <h3><i class="fas fa-utensils"></i> Alimentação</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="categoria-content">
                    <div class="grid-progresso">
                        <div class="card-progresso">
                            <h4>Calorias de Hoje</h4>
                            <div class="valor-destaque">
                                <?php echo number_format($calorias_hoje, 0); ?><span class="unidade">kcal</span>
                            </div>
                            <div class="progresso-percentual">
                                Total consumido hoje
                            </div>
                        </div>
                        <div class="card-progresso">
                            <h4>Registar Refeição</h4>

                            <!-- Barra de pesquisa da Open Food Facts com Imagens -->
                            <div class="form-group"
                                style="margin-bottom: 20px; border-bottom: 2px solid var(--cor-secundaria); padding-bottom: 15px;">
                                <label for="food-search-progresso"><i class="fas fa-search"></i> Pesquisar Alimento
                                    (Preenchimento Automático)</label>
                                <div style="display: flex; gap: 10px;">
                                    <input type="text" id="food-search-progresso"
                                        placeholder="Ex: Banana, Leite, Pão..."
                                        style="flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 8px;">
                                    <button type="button" onclick="searchFoodProgresso()"
                                        style="background: var(--cor-destaque); color: var(--cor-primaria); border: none; padding: 10px 15px; border-radius: 8px; cursor: pointer;"
                                        title="Pesquisar por nome"><i class="fas fa-search"></i></button>
                                    <button type="button" onclick="startBarcodeScanner()"
                                        style="background: var(--cor-intermedia); color: var(--cor-destaque); border: none; padding: 10px 15px; border-radius: 8px; cursor: pointer; font-weight: bold;"
                                        title="Ler Código de Barras"><i class="fas fa-barcode"></i> LER</button>
                                </div>
                                <div id="barcode-reader-container"
                                    style="display: none; margin-top: 15px; background: white; border-radius: 8px; overflow: hidden; border: 1px solid #ccc; padding: 10px; position: relative;">
                                    <div
                                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                        <h5 style="margin: 0; color: var(--cor-destaque);">Aponte a câmara para o código
                                            de barras</h5>
                                        <button type="button" onclick="stopBarcodeScanner()"
                                            style="background: none; border: none; font-size: 1.5em; cursor: pointer; color: #ef4444;">&times;</button>
                                    </div>
                                    <div id="reader" style="width: 100%;"></div>
                                </div>
                                <div id="food-results-progresso"
                                    style="margin-top: 10px; max-height: 300px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px;">
                                    <!-- Resultados aparecerão aqui -->
                                </div>

                                <!-- Container para Lista de Selecionados -->
                                <div id="selected-foods-container"
                                    style="margin-top: 15px; display: none; flex-direction: column; gap: 10px;">
                                    <h5 style="margin: 0; color: var(--cor-destaque);"><i
                                            class="fas fa-shopping-basket"></i> Alimentos Selecionados:</h5>
                                    <div id="selected-foods-list"
                                        style="display: flex; flex-direction: column; gap: 8px;"></div>
                                    <div
                                        style="display: flex; justify-content: space-between; align-items: center; background: #e2e8f0; padding: 10px; border-radius: 8px;">
                                        <strong>Total de Calorias:</strong>
                                        <span id="selected-foods-total-cals"
                                            style="color: #ea580c; font-weight: bold; font-size: 1.1em;">0 kcal</span>
                                    </div>
                                    <button type="button" onclick="confirmSelectedFoods()"
                                        style="background: #22c55e; color: white; border: none; padding: 10px; border-radius: 8px; cursor: pointer; font-weight: 600; text-align: center;"><i
                                            class="fas fa-check"></i> Confirmar</button>
                                </div>
                            </div>

                            <form method="POST" class="form-progresso">
                                <input type="hidden" name="acao" value="adicionar_alimentacao">
                                <div class="form-group">
                                    <label for="refeicao">Refeição</label>
                                    <select id="refeicao" name="refeicao" required>
                                        <option value="Café da Manhã">Pequeno-almoço</option>
                                        <option value="Lanche da Manhã">Lanche da Manhã</option>
                                        <option value="Almoço">Almoço</option>
                                        <option value="Lanche da Tarde">Lanche da Tarde</option>
                                        <option value="Jantar">Jantar</option>
                                        <option value="Ceia">Ceia</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="descricao">Descrição</label>
                                    <input type="text" id="descricao" name="descricao"
                                        placeholder="Ex: Arroz, frango, salada">
                                </div>
                                <div class="form-group">
                                    <label for="calorias">Calorias</label>
                                    <input type="number" id="calorias" name="calorias" step="1" min="0" required>
                                </div>
                                <div class="form-group">
                                    <label for="data_alimentacao">Data</label>
                                    <input type="date" id="data_alimentacao" name="data"
                                        value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <button type="submit" class="btn-adicionar">
                                    <i class="fas fa-plus"></i> Adicionar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção Desafios (Checklists Diários) -->
            <div class="categoria-item fade-in-element">
                <div class="categoria-header" onclick="toggleCategoria(this)">
                    <h3><i class="fas fa-tasks"></i> Desafios Diários</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="categoria-content">

                    <!-- Formulário para criar nova Rotina -->

                    <div class="card-progresso" style="margin-bottom: 25px;">
                        <h4>Criar Nova Rotina</h4>
                        <form method="POST" class="form-progresso">
                            <input type="hidden" name="acao" value="criar_habito">
                            <div class="form-group">
                                <label for="descricao_habito">Descrição da Rotina</label>
                                <input type="text" id="descricao_habito" name="descricao"
                                    placeholder="Ex: Beber 3L de água" required>
                            </div>
                            <div class="form-group">
                                <label for="tipo_habito">Tipo (opcional)</label>
                                <input type="text" id="tipo_habito" name="tipo"
                                    placeholder="Ex: Saúde, Exercício, Alimentação">
                            </div>
                            <div class="form-group">
                                <label for="meta_diaria">Meta Diária (opcional)</label>
                                <input type="number" id="meta_diaria" name="meta_diaria" step="0.1" min="0"
                                    placeholder="Ex: 3.0">
                            </div>
                            <button type="submit" class="btn-adicionar">
                                <i class="fas fa-plus"></i> Criar Rotina
                            </button>
                        </form>
                    </div>

                    <!-- Lista de Rotinas/desafios permanentes -->
                    <?php if (!empty($habitos)): ?>
                        <div style="margin-bottom: 25px;">
                            <h4 style="margin-bottom: 15px;">Meus Desafios</h4>
                            <?php foreach ($habitos as $habito): ?>
                                <div class="checklist-item"
                                    style="background: var(--cor-primaria); border: 1px solid var(--cor-secundaria);">
                                    <div class="checklist-content" style="flex: 1;">
                                        <h5 class="checklist-titulo"><?php echo htmlspecialchars($habito['descricao']); ?></h5>
                                        <?php if (!empty($habito['tipo'])): ?>
                                            <p class="checklist-descricao" style="font-size: 0.85em; color: var(--cor-intermedia);">
                                                <i class="fas fa-tag"></i> <?php echo htmlspecialchars($habito['tipo']); ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php if (!empty($habito['meta_diaria'])): ?>
                                            <p class="checklist-descricao" style="font-size: 0.85em;">
                                                Meta: <?php echo $habito['meta_diaria']; ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="checklist-actions">
                                        <form method="POST" style="display: inline;"
                                            onsubmit="return confirm('Tem a certeza que deseja remover este desafio?');">
                                            <input type="hidden" name="acao" value="deletar_habito">
                                            <input type="hidden" name="habito_id" value="<?php echo $habito['id_habito']; ?>">
                                            <button type="submit" class="btn-icon delete" title="Remover Desafio">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p style="text-align: center; color: var(--cor-texto); opacity: 0.7; padding: 20px;">
                            Nenhum desafio criado ainda. Crie uma Rotina acima ou complete o onboarding para receber
                            desafios
                            automáticos!
                        </p>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </main>

    <!-- Modal para editar meta de água -->
    <div id="modalMeta" class="modal" onclick="fecharModalMeta(event)">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h4>Editar Meta de Água</h4>
                <button type="button" class="btn-fechar" onclick="fecharModalMeta(event)">&times;</button>
            </div>
            <form method="POST" class="form-progresso">
                <input type="hidden" name="acao" value="editar_meta_agua">
                <div class="form-group">
                    <label for="meta_agua">Meta Diária (L)</label>
                    <input type="number" id="meta_agua" name="meta_agua" step="0.1" min="0" max="10"
                        value="<?php echo $agua_meta; ?>" required>
                </div>
                <button type="submit" class="btn-adicionar">
                    <i class="fas fa-save"></i> Guardar Meta
                </button>
            </form>
        </div>
    </div>

    <script>
        function toggleCategoria(header) {
            const content = header.nextElementSibling;
            const isActive = content.classList.contains('active');

            // Fecha todas as outras categorias
            document.querySelectorAll('.categoria-content').forEach(item => {
                item.classList.remove('active');
            });
            document.querySelectorAll('.categoria-header').forEach(item => {
                item.classList.remove('active');
            });

            // Abre/fecha a categoria clicada
            if (!isActive) {
                content.classList.add('active');
                header.classList.add('active');
            }
        }

        function abrirModalMeta() {
            document.getElementById('modalMeta').classList.add('active');
        }

        function fecharModalMeta(event) {
            if (event) {
                event.stopPropagation();
            }
            document.getElementById('modalMeta').classList.remove('active');
        }

        // Fecha modal ao pressionar ESC
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                fecharModalMeta();
            }
        });

        // Abre a primeira categoria por padrão
        document.addEventListener('DOMContentLoaded', function () {
            const firstCategory = document.querySelector('.categoria-header');
            if (firstCategory) {
                toggleCategoria(firstCategory);
            }
        });

        // Função de pesquisa com imagens para o progresso
        async function searchFoodProgresso() {
            const query = document.getElementById('food-search-progresso').value.trim();
            if (!query) return;

            const resultsContainer = document.getElementById('food-results-progresso');
            resultsContainer.innerHTML = '<p style="text-align:center; padding: 10px;">A procurar...</p>';

            try {
                // API Open Food Facts
                const response = await fetch(`https://world.openfoodfacts.org/cgi/search.pl?search_terms=${encodeURIComponent(query)}&search_simple=1&action=process&json=1&page_size=10`);
                const data = await response.json();

                if (data.products && data.products.length > 0) {
                    resultsContainer.innerHTML = '';

                    data.products.forEach(product => {
                        const productName = product.product_name || 'Produto Sem Nome';
                        let calories = 0;
                        let caloriesText = 'N/A';

                        if (product.nutriments) {
                            if (product.nutriments['energy-kcal_100g'] !== undefined) {
                                caloriesText = product.nutriments['energy-kcal_100g'] + ' kcal / 100g';
                                calories = Math.round(product.nutriments['energy-kcal_100g']);
                            } else if (product.nutriments['energy-kcal_value'] !== undefined) {
                                caloriesText = product.nutriments['energy-kcal_value'] + ' kcal';
                                calories = Math.round(product.nutriments['energy-kcal_value']);
                            }
                        }

                        // Imagem do produto
                        const imageUrl = product.image_front_small_url || product.image_url || 'https://via.placeholder.com/60?text=Sem+Foto';

                        // Criar o cartão de resultado
                        const resultDiv = document.createElement('div');
                        resultDiv.style.cssText = 'display: flex; gap: 15px; align-items: center; padding: 10px; background: var(--cor-secundaria); border-radius: 8px; cursor: pointer; border: 1px solid #ddd; transition: all 0.2s';
                        resultDiv.onmouseover = () => resultDiv.style.background = '#e2e8f0';
                        resultDiv.onmouseout = () => resultDiv.style.background = 'var(--cor-secundaria)';

                        // Ao clicar preenche o formulário
                        resultDiv.onclick = () => {
                            addFoodToSelection(productName, calories, imageUrl);
                        };

                        resultDiv.innerHTML = `
                            <img src="${imageUrl}" alt="${productName}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; background: #fff;">
                            <div style="flex: 1;">
                                <h5 style="margin: 0 0 5px 0; font-size: 14px; color: var(--cor-destaque);">${productName}</h5>
                                <p style="margin: 0; font-size: 12px; color: #555;"><i class="fas fa-fire" style="color: #ff9900;"></i> ${caloriesText}</p>
                            </div>
                        `;

                        resultsContainer.appendChild(resultDiv);
                    });
                } else {
                    resultsContainer.innerHTML = '<p style="text-align:center; padding: 10px;">Nenhum produto encontrado com esse nome.</p>';
                }
            } catch (error) {
                console.error('Erro na pesquisa:', error);
                resultsContainer.innerHTML = '<p style="text-align:center; padding: 10px; color: red;">Ocorreu um erro ao pesquisar os alimentos.</p>';
            }
        }

        // Fechar scanner de código de barras
        let html5QrcodeScanner = null;

        function stopBarcodeScanner() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear().catch(error => {
                    console.error("Failed to clear html5QrcodeScanner. ", error);
                });
                html5QrcodeScanner = null;
            }
            document.getElementById('barcode-reader-container').style.display = 'none';
        }

        function startBarcodeScanner() {
            const container = document.getElementById('barcode-reader-container');
            container.style.display = 'block';

            if (html5QrcodeScanner == null) {
                html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader",
                    { fps: 10, qrbox: { width: 250, height: 150 } },
                    false
                );

                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            }
        }

        async function onScanSuccess(decodedText, decodedResult) {
            // Parar scanner
            stopBarcodeScanner();

            const resultsContainer = document.getElementById('food-results-progresso');
            resultsContainer.innerHTML = '<p style="text-align:center; padding: 10px; color: var(--cor-destaque);">A analisar o código de barras ' + decodedText + '...</p>';

            try {
                // Pesquisar produto por código de barras na Open Food Facts
                const response = await fetch(`https://world.openfoodfacts.org/api/v0/product/${decodedText}.json`);
                const data = await response.json();

                if (data.status === 1 && data.product) {
                    const product = data.product;
                    const productName = product.product_name || 'Produto Sem Nome';
                    let calories = 0;

                    if (product.nutriments) {
                        if (product.nutriments['energy-kcal_100g'] !== undefined) {
                            calories = Math.round(product.nutriments['energy-kcal_100g']);
                        } else if (product.nutriments['energy-kcal_value'] !== undefined) {
                            calories = Math.round(product.nutriments['energy-kcal_value']);
                        }
                    }

                    const imageUrl = product.image_front_small_url || product.image_url || 'https://via.placeholder.com/60?text=Sem+Foto';

                    addFoodToSelection(productName, calories, imageUrl);

                    resultsContainer.innerHTML = '<p style="text-align:center; padding: 10px; color: #22c55e;"><i class="fas fa-check-circle"></i> Produto adicionado com sucesso!</p>';
                    setTimeout(() => { resultsContainer.innerHTML = ''; }, 3000);
                } else {
                    resultsContainer.innerHTML = '<p style="text-align:center; padding: 10px; color: #ea580c;">Produto não encontrado na base de dados com esse código de barras.</p>';
                    setTimeout(() => { resultsContainer.innerHTML = ''; }, 4000);
                }
            } catch (error) {
                console.error('Erro na pesquisa por código de barras:', error);
                resultsContainer.innerHTML = '<p style="text-align:center; padding: 10px; color: red;">Ocorreu um erro ao comunicar com a base de dados.</p>';
                setTimeout(() => { resultsContainer.innerHTML = ''; }, 3000);
            }
        }

        function onScanFailure(error) {
            // Pode ignorar o erro do scan failure contínuo (fps)
        }

        // --- Lógica para múltiplos alimentos (Lista de Compras/Refeição) ---
        let selectedFoodsList = [];

        function addFoodToSelection(name, caloriesPer100g, imageUrl) {
            // Adiciona com valor padrão de 100g
            selectedFoodsList.push({
                id: Date.now(),
                name: name,
                calPer100g: caloriesPer100g > 0 ? caloriesPer100g : 0,
                grams: 100,
                img: imageUrl
            });
            renderSelectedFoods();

            // Limpa a barra de pesquisa para melhor UX
            document.getElementById('food-search-progresso').value = '';
            document.getElementById('food-results-progresso').innerHTML = '';
        }

        function updateFoodGrams(id, newGrams) {
            const food = selectedFoodsList.find(f => f.id === id);
            if (food) {
                food.grams = parseInt(newGrams) || 0;
                renderSelectedFoods();
            }
        }

        function removeFoodFromSelection(id) {
            selectedFoodsList = selectedFoodsList.filter(f => f.id !== id);
            renderSelectedFoods();
        }

        function renderSelectedFoods() {
            const container = document.getElementById('selected-foods-container');
            const list = document.getElementById('selected-foods-list');
            const totalSpan = document.getElementById('selected-foods-total-cals');

            if (selectedFoodsList.length === 0) {
                container.style.display = 'none';
                return;
            }

            container.style.display = 'flex';
            list.innerHTML = '';

            let totalCals = 0;

            selectedFoodsList.forEach(food => {
                const itemCals = Math.round((food.calPer100g / 100) * food.grams);
                totalCals += itemCals;

                const div = document.createElement('div');
                div.style.cssText = 'display: flex; align-items: center; gap: 10px; background: white; padding: 8px; border-radius: 8px; border: 1px solid #ddd;';

                div.innerHTML = `
                    <img src="${food.img}" style="width: 40px; height: 40px; border-radius: 4px; object-fit: cover; border: 1px solid #eee;">
                    <div style="flex: 1; min-width: 0;">
                        <p style="margin: 0; font-size: 13px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${food.name}</p>
                        <p style="margin: 0; font-size: 11px; color: #666;">${food.calPer100g} kcal / 100g</p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 5px;">
                        <input type="number" value="${food.grams}" min="1" max="5000" style="width: 60px; padding: 5px; border: 1px solid #ccc; border-radius: 4px; font-family: var(--fonte-texto);" onchange="updateFoodGrams(${food.id}, this.value)" onkeyup="updateFoodGrams(${food.id}, this.value)">
                        <span style="font-size: 12px; color: #555;">g</span>
                    </div>
                    <div style="width: 65px; text-align: right; font-weight: bold; font-size: 13px; color: var(--cor-destaque);">
                        ${itemCals} <span style="font-size: 10px; font-weight: normal;">kcal</span>
                    </div>
                    <button type="button" onclick="removeFoodFromSelection(${food.id})" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 5px;"><i class="fas fa-trash"></i></button>
                `;
                list.appendChild(div);
            });

            totalSpan.textContent = totalCals + ' kcal';
        }

        function confirmSelectedFoods() {
            if (selectedFoodsList.length === 0) return;

            let descriptions = [];
            let totalCalories = 0;

            selectedFoodsList.forEach(food => {
                const itemCals = Math.round((food.calPer100g / 100) * food.grams);
                totalCalories += itemCals;
                descriptions.push(`${food.name} (${food.grams}g)`);
            });

            // Preencher Formulário
            document.getElementById('descricao').value = descriptions.join(', ');
            document.getElementById('calorias').value = totalCalories;

            // Mostrar feedback visual
            const resultsContainer = document.getElementById('food-results-progresso');
            resultsContainer.innerHTML = '<div style="background: #f0fdf4; color: #166534; padding: 10px; border-radius: 8px; border-left: 4px solid #22c55e;"><i class="fas fa-check-circle"></i> Refeição pronta para ser adicionada no formulário abaixo!</div>';

            // Limpar lista
            selectedFoodsList = [];
            renderSelectedFoods();

            // Limpa o feedback passado alguns segundos
            setTimeout(() => {
                resultsContainer.innerHTML = '';
            }, 3000);
        }

    </script>

    <nav class="navbar">
        <a href="dashboard.php" class="nav-link"><i class="fas fa-home icon"></i> <span class="text">Início</span></a>
        <a href="treinos.php" class="nav-link"><i class="fas fa-dumbbell icon"></i> <span
                class="text">Treinos</span></a>
        <a href="progresso.php" class="nav-link active"><i class="fas fa-chart-line icon"></i> <span
                class="text">Progresso</span></a>
        <a href="chatbot.php" class="nav-link"><i class="fas fa-robot icon"></i> <span class="text">Chatbot</span></a>
        <a href="perfil.php" class="nav-link"><i class="fas fa-user icon"></i> <span class="text">Perfil</span></a>
    </nav>

</body>

</html>