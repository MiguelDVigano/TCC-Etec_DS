<?php
include "../conexao.php";
session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../view/Login.html");
    exit();
}

$turma = $_POST['turma'];
$dataEnvio = $_POST['dataEnvio'];
$assunto = $_POST['assunto'];
$mensagem = $_POST['mensagem'];

$sql = "INSERT INTO mensagens (turma, data_envio, assunto, mensagem) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $turma, $dataEnvio, $assunto, $mensagem);

if ($stmt->execute()) {
    header("Location: ../view/mensagem.html");
    exit();
} else {
    $_SESSION['error_message'] = "Erro ao enviar mensagem: " . $stmt->error;
    header("Location: ../view/mensagem.html");
    exit();
}

$stmt->close();
$conn->close();