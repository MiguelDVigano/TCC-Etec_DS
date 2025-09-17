<?php
include "../conexao.php";
session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../view/Login.html");
    exit();
}

$assunto = $_POST['assunto'];
$mensagem = $_POST['mensagem'];
$id_remetente = $_SESSION['id_usuario'];
$enviar_para_todas = isset($_POST['enviar_para_todas']) && $_POST['enviar_para_todas'] == '1' ? 1 : 0;
$turmas = isset($_POST['turmas']) ? $_POST['turmas'] : [];
$data_expiracao = $_POST['data_expiracao'] ?? null;
if ($data_expiracao) {
    $data_expiracao = date('Y-m-d H:i:s', strtotime($data_expiracao));
} else {
    $data_expiracao = null; // ou defina um valor padrão, se necessário
}

// Inserir mensagem na tabela mensagem
$sql = "INSERT INTO mensagem (assunto, mensagem, data_envio, id_remetente, enviar_para_todas, data_expiracao) VALUES (?, ?, NOW(), ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssiis", $assunto, $mensagem, $id_remetente, $enviar_para_todas, $data_expiracao);

if ($stmt->execute()) {
    $id_mensagem = $stmt->insert_id;
    // Se não for para todas as turmas, insere as turmas selecionadas
    if (!$enviar_para_todas && !empty($turmas)) {
        $sqlTurma = "INSERT INTO mensagem_turma (id_mensagem, id_turma) VALUES (?, ?)";
        $stmtTurma = $conn->prepare($sqlTurma);
        foreach ($turmas as $turma_id) {
            if (!is_numeric($turma_id)) continue;
            $stmtTurma->bind_param("ii", $id_mensagem, $turma_id);
            $stmtTurma->execute();
        }
        $stmtTurma->close();
    }
    header("Location: ../view/professor/mensagem.php");
    exit();
} else {
    $_SESSION['error_message'] = "Erro ao enviar mensagem: " . $stmt->error;
    header("Location: ../view/professor/mensagem.php");
    exit();
}

$stmt->close();
$conn->close();