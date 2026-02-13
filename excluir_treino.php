<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'ligacao.php';

$user_id = $_SESSION['user_id'];
$id_treino = $_GET['id'] ?? 0;

// Verificar se o treino pertence ao usuário
$stmt = $conn->prepare("SELECT id_treino FROM treino WHERE id_treino = ? AND id_user = ?");
$stmt->bind_param("ii", $id_treino, $user_id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    header("Location: treinos.php");
    exit;
}
$stmt->close();

$conn->begin_transaction();
try {
    // Excluir exercícios primeiro (FK constraint)
    $stmt = $conn->prepare("DELETE FROM exercicio WHERE id_treino = ?");
    $stmt->bind_param("i", $id_treino);
    $stmt->execute();
    $stmt->close();

    // Excluir treino
    $stmt = $conn->prepare("DELETE FROM treino WHERE id_treino = ?");
    $stmt->bind_param("i", $id_treino);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    header("Location: treinos.php?excluido=1");
} catch (Exception $e) {
    $conn->rollback();
    header("Location: treinos.php?erro=1");
}

$conn->close();
exit;
?>