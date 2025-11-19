<?php
session_set_cookie_params([
    'lifetime' => 3600, // 1 hora
    'path' => '/',
    'domain' => '', // Deixe vazio para usar o domínio atual
    'secure' => false, // Use true se estiver usando HTTPS
    'httponly' => true,
    'samesite' => 'Lax' // Ou 'Strict' dependendo do caso
]);
include("../conexao.php");

// coletar os campos do formulário
$email = $_POST['email'];
$senha = $_POST['senha'];
$tipoUsuario = $_POST['tipoUsuario'];

// Buscar usuário por email e tipo
$sql = "SELECT * FROM usuario WHERE email = '$email' AND senha_hash = '$senha' AND tipo_usuario = '$tipoUsuario'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    session_start();
    $user = $result->fetch_assoc();
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
} else {
    header("Location: ../view/Login.html");
    exit();
}
