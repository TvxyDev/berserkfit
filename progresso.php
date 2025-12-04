<?php
session_start();

// Verifica se o usuário está logado
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

        // Verifica se já existe registro para hoje
        $sql = "SELECT id, quantidade FROM agua WHERE id_user = ? AND data = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $data);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Atualiza registro existente
            $row = $result->fetch_assoc();
            $nova_quantidade = $row['quantidade'] + $quantidade;
            $update = "UPDATE agua SET quantidade = ? WHERE id = ?";
            $stmt2 = $conn->prepare($update);
            $stmt2->bind_param("di", $nova_quantidade, $row['id']);
            $stmt2->execute();
            $stmt2->close();
            $mensagem = "✅ Água adicionada com sucesso!";
        } else {
            // Cria novo registro
            $insert = "INSERT INTO agua (id_user, quantidade, data) VALUES (?, ?, ?)";
            $stmt2 = $conn->prepare($insert);
            $stmt2->bind_param("ids", $user_id, $quantidade, $data);
            if ($stmt2->execute()) {
                $mensagem = "✅ Água registrada com sucesso!";
            } else {
                $mensagem = "❌ Erro ao registrar água.";
            }
            $stmt2->close();
        }
        $stmt->close();
    } elseif ($acao === 'adicionar_peso') {
        $peso = floatval($_POST['peso'] ?? 0);
        $data = $_POST['data'] ?? date('Y-m-d');

        // Verifica se já existe registro para hoje
        $sql = "SELECT id FROM peso WHERE id_user = ? AND data = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $data);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Atualiza registro existente
            $row = $result->fetch_assoc();
            $update = "UPDATE peso SET peso = ? WHERE id = ?";
            $stmt2 = $conn->prepare($update);
            $stmt2->bind_param("di", $peso, $row['id']);
            $stmt2->execute();
            $stmt2->close();
            $mensagem = "✅ Peso atualizado com sucesso!";
        } else {
            // Cria novo registro
            $insert = "INSERT INTO peso (id_user, peso, data) VALUES (?, ?, ?)";
            $stmt2 = $conn->prepare($insert);
            $stmt2->bind_param("ids", $user_id, $peso, $data);
            if ($stmt2->execute()) {
                $mensagem = "✅ Peso registrado com sucesso!";
            } else {
                $mensagem = "❌ Erro ao registrar peso.";
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
            $mensagem = "✅ Refeição registrada com sucesso!";
        } else {
            $mensagem = "❌ Erro ao registrar refeição.";
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
                $mensagem = "✅ Rotina criado com sucesso!";
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
            $mensagem = "✅ Rotina removido com sucesso!";
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
<html lang="pt-br">

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
            <p>Acompanhe sua evolução diária</p>
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
                                    Registrado em: <?php echo date('d/m/Y', strtotime($data_peso)); ?>
                                </div>
                            <?php else: ?>
                                <div class="valor-destaque">
                                    --<span class="unidade">kg</span>
                                </div>
                                <div class="progresso-percentual">
                                    Nenhum registro ainda
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-progresso">
                            <h4>Registrar Peso</h4>
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
                                    <i class="fas fa-save"></i> Registrar
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
                            <h4>Registrar Refeição</h4>
                            <form method="POST" class="form-progresso">
                                <input type="hidden" name="acao" value="adicionar_alimentacao">
                                <div class="form-group">
                                    <label for="refeicao">Refeição</label>
                                    <select id="refeicao" name="refeicao" required>
                                        <option value="Café da Manhã">Café da Manhã</option>
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

                    <!-- Formulário para criar novo Rotina -->

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
                                            onsubmit="return confirm('Tem certeza que deseja remover este desafio?');">
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
                            Nenhum desafio criado ainda. Crie um Rotina acima ou complete o onboarding para receber desafios
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
                    <i class="fas fa-save"></i> Salvar Meta
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
    </script>

    <nav class="navbar">
        <a href="dashboard.php" class="nav-link"><i class="fas fa-home icon"></i> <span class="text">Home</span></a>
        <a href="#" class="nav-link"><i class="fas fa-dumbbell icon"></i> <span class="text">Treinos</span></a>
        <a href="progresso.php" class="nav-link active"><i class="fas fa-chart-line icon"></i> <span
                class="text">Progresso</span></a>
        <a href="chatbot.php" class="nav-link"><i class="fas fa-robot icon"></i> <span class="text">Chatbot</span></a>
        <a href="perfil.php" class="nav-link"><i class="fas fa-user icon"></i> <span class="text">Perfil</span></a>
    </nav>

</body>

</html>