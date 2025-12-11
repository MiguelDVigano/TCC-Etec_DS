<?php
session_set_cookie_params([
    'lifetime' => 3600, // 1 hora
    'path' => '/',
    'domain' => '', // Deixe vazio para usar o domínio atual
    'secure' => false, // Use true se estiver usando HTTPS
    'httponly' => true,
    'samesite' => 'Lax' // Ou 'Strict' dependendo do caso
]);
include("../controller/conexao.php");

// coletar os campos do formulário
$email = $_POST['email'];
$senha = $_POST['senha'];
$tipoUsuario = $_POST['tipoUsuario'];

// Buscar usuário por email e tipo (NÃO compare senha aqui!)
$sql = "SELECT * FROM usuario WHERE email = ? AND tipo_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $tipoUsuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    // Verifica a senha usando password_verify
    if (password_verify($senha, $user['senha_hash'])) {
        session_start();
        $_SESSION["id_usuario"] = $user["id_usuario"];
        $_SESSION["tipo_usuario"] = $user["tipo_usuario"];
        if ($tipoUsuario == "Professor") {
            header("Location: ../view/professor/laboratorios.php");
        } else if ($tipoUsuario == "Aluno") {
            header("Location: ../view/aluno/mensagem_aluno.php");
        } else if ($tipoUsuario == "Manutencao") {
            header("Location: ../view/manutencao/manutencao.php");
        } else {
            header("Location: ../view/Login.html");
        }
        exit();
    }
}

// Falha de login
header("Location: ../view/Login.html");
exit();
