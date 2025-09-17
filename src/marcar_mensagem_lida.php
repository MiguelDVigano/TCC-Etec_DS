<?php
session_start();
include '../conexao.php';

$id_mensagem = $_POST['id_mensagem'] ?? null;
$id_aluno = $_SESSION['id_usuario'] ?? null;

if ($id_mensagem && $id_aluno) {
    // Marca como lida
    $insert_sql = "INSERT INTO mensagem_leitura (id_mensagem, id_aluno) VALUES (?, ?) ON DUPLICATE KEY UPDATE data_leitura = NOW();";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param('ii', $id_mensagem, $id_aluno);
    $insert_stmt->execute();
    $insert_stmt->close();
    header('Location: ../view/aluno/mensagem_aluno.php');
    exit();
}