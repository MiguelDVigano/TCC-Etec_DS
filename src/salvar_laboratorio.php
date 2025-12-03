<?php

session_start();
if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo_usuario"] !== "Manutenção") {
    header("Location: logout.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../view/manutencao/manutencao.php");
    exit();
}

include '../controller/conexao.php';

// Coleta e validação dos dados do formulário
$nome = trim($_POST['nome'] ?? '');
$numero_sala = trim($_POST['numero_sala'] ?? '');
$tipo_sala = $_POST['tipo_sala'] ?? '';
$capacidade = intval($_POST['capacidade'] ?? 0);
$status_sala = $_POST['status_sala'] ?? 'Ativa';

// Validação
if ($nome === '' || $numero_sala === '' || !is_numeric($numero_sala) || $capacidade < 1 || $tipo_sala === '') {
    $_SESSION['msg_lab'] = "Preencha todos os campos corretamente.";
    header("Location: ../view/manutencao/adicionar_lab.php");
    exit();
}

// Insere no banco conforme estrutura do banco
$stmt = $conn->prepare("INSERT INTO sala (numero_sala, titulo_sala, tipo_sala, capacidade, status_sala) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issds", $numero_sala, $nome, $tipo_sala, $capacidade, $status_sala);

if ($stmt->execute()) {
    $_SESSION['msg_lab'] = "Laboratório cadastrado com sucesso!";
    header("Location: ../view/manutencao/manutencao.php");
    exit();
} else {
    $_SESSION['msg_lab'] = "Erro ao cadastrar laboratório.";
    header("Location: ../view/manutencao/adicionar_lab.php");
    exit();
}