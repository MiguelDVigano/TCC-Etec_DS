<?php
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
        if ($tipoUsuario == "Professor") {
            header("Location: ../view/reserva_de_labs.php");
        } else if ($tipoUsuario == "Aluno") {
            header("Location: ../view/dashboard_aluno.php");
        } else if ($tipoUsuario == "Manutencao") {
            header("Location: ../view/dashboard_tecnico.php");
        } else {
            header("Location: ../view/login.html");
        }
        exit();
    } else {
        header("Location: ../view/login.html");
        exit();
    }
