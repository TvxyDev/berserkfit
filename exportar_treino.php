<?php
session_start();
require 'ligacao.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['treinos'])) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos.']);
    exit;
}

$treinos = $input['treinos'];
$conn->begin_transaction();

try {
    foreach ($treinos as $treino) {
        $nome_treino = $treino['nome'] ?? 'Treino Personalizado';
        $foco = $treino['foco'] ?? 'Geral';
        $data_criacao = date('Y-m-d');

        // Inserir Treino
        $stmt = $conn->prepare("INSERT INTO treino (id_user, nome_treino, foco, data_criacao) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $nome_treino, $foco, $data_criacao);
        $stmt->execute();
        $id_treino = $conn->insert_id;
        $stmt->close();

        // Inserir Exercícios
        if (isset($treino['exercicios']) && is_array($treino['exercicios'])) {
            $stmt_ex = $conn->prepare("INSERT INTO exercicio (id_treino, nome_exercicio, series, repeticoes, grupo_muscular) VALUES (?, ?, ?, ?, ?)");

            foreach ($treino['exercicios'] as $ex) {
                $nome_ex = $ex['nome'];
                $series = intval($ex['series']);
                $repeticoes = intval($ex['repeticoes']); // Pode ser 0 se for tempo, mas o campo é int
                $grupo = $ex['grupo_muscular'] ?? null;

                $stmt_ex->bind_param("isiis", $id_treino, $nome_ex, $series, $repeticoes, $grupo);
                $stmt_ex->execute();
            }
            $stmt_ex->close();
        }
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Treinos salvos com sucesso!']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar treinos: ' . $e->getMessage()]);
}

$conn->close();
?>