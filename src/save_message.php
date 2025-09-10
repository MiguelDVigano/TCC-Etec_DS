<?php
include "../conexao.php";
session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../view/Login.html");
    exit();
}

// Permitir múltiplas turmas no futuro
define('TODAS_TURMAS_VALUE', 'todas');

$turma = $_POST['turma'];
$dataEnvio = $_POST['dataEnvio'];
$assunto = $_POST['assunto'];
$mensagem = $_POST['mensagem'];
$id_remetente = $_SESSION['id_usuario'];

// Verifica se turma é array (para múltiplas turmas no futuro)
if (!is_array($turma)) {
    $turmas = [$turma];
} else {
    $turmas = $turma;
}

$enviar_para_todas = in_array(TODAS_TURMAS_VALUE, $turmas) ? 1 : 0;

// Inserir mensagem na tabela mensagem
$sql = "INSERT INTO mensagem (assunto, mensagem, data_envio, id_remetente, enviar_para_todas) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $assunto, $mensagem, $dataEnvio, $id_remetente, $enviar_para_todas);

if ($stmt->execute()) {
    $id_mensagem = $stmt->insert_id;
    // Se não for para todas as turmas, insere as turmas selecionadas
    if (!$enviar_para_todas) {
        $sqlTurma = "INSERT INTO mensagem_turma (id_mensagem, id_turma) VALUES (?, ?)";
        $stmtTurma = $conn->prepare($sqlTurma);
        foreach ($turmas as $turma_id) {
            if ($turma_id === TODAS_TURMAS_VALUE) continue;
            // Buscar id_turma pelo nome (caso venha nome da turma)
            if (!is_numeric($turma_id)) {
                $q = $conn->prepare("SELECT id_turma FROM turma WHERE nome_turma = ? LIMIT 1");
                $q->bind_param("s", $turma_id);
                $q->execute();
                $q->bind_result($id_turma_real);
                if ($q->fetch()) {
                    $turma_id = $id_turma_real;
                }
                $q->close();
            }
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