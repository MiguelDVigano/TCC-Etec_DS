<?php
session_start();
if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo_usuario"] !== "Manutencao") {
    header("Location: ../view/Login.html");
    exit();
}

include '../controller/conexao.php';

// Coleta dados do formulário
$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$senha = $_POST['senha'] ?? '';
$id_turma = intval($_POST['id_turma'] ?? 0);

if ($nome === '' || $email === '' || $telefone === '' || $senha === '' || $id_turma < 1) {
    $_SESSION['msg_aluno'] = "Preencha todos os campos corretamente.";
    header("Location: ../view/manutencao/adicionar_aluno.php");
    exit();
}

// Verifica se o e-mail já existe
$stmtCheck = $conn->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
$stmtCheck->bind_param("s", $email);
$stmtCheck->execute();
$resCheck = $stmtCheck->get_result();
if ($resCheck->num_rows > 0) {
    $_SESSION['msg_aluno'] = "E-mail já cadastrado.";
    header("Location: ../view/manutencao/adicionar_aluno.php");
    exit();
}

// Hash da senha
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

// Insere aluno
$stmt = $conn->prepare("INSERT INTO usuario (nome, email, senha_hash, tipo_usuario, telefone) VALUES (?, ?, ?, 'Aluno', ?)");
$stmt->bind_param("ssss", $nome, $email, $senha_hash, $telefone);
if ($stmt->execute()) {
    $id_aluno = $stmt->insert_id;
    // Insere matrícula
    $stmtMat = $conn->prepare("INSERT INTO matricula (id_aluno, id_turma) VALUES (?, ?)");
    $stmtMat->bind_param("ii", $id_aluno, $id_turma);
    if ($stmtMat->execute()) {
        $_SESSION['msg_aluno'] = "Aluno cadastrado e matriculado com sucesso!";
        header("Location: ../view/manutencao/manutencao.php");
        exit();
    } else {
        $_SESSION['msg_aluno'] = "Erro ao salvar matrícula.";
        header("Location: ../view/manutencao/adicionar_aluno.php");
        exit();
    }
} else {
    $_SESSION['msg_aluno'] = "Erro ao cadastrar aluno.";
    header("Location: ../view/manutencao/adicionar_aluno.php");
    exit();
}
