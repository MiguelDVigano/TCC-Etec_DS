<?php
    include("../conexao.php");

    // coletar os campos do formulário
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];
    $tipoUsuario = $_POST['tipoUsuario'];

    // Aqui você pode adicionar a lógica para autenticar o usuário
    $sql = "SELECT * FROM usuario WHERE usuario = '$usuario' AND senha = '$senha' AND tipo_usuario = '$tipoUsuario'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Login bem-sucedido
        session_start();
        $_SESSION["id_usuario"] = $result->fetch_assoc()["id_usuario"];
        if ($tipoUsuario == "Professor") {
            header("Location: ../view/dashboard_professor.php");
        } else if ($tipoUsuario == "Aluno") {
            header("Location: ../view/dashboard_aluno.php");
        } else if ($tipoUsuario == "Diretor") {
            header("Location: ../view/dashboard_diretor.php");
        } else if ($tipoUsuario == "Técnico") {
            header("Location: ../view/dashboard_tecnico.php");
        }
        exit();
    } else {
        // Login falhou
        header("Location: ../view/Login.html?erro=1");
        exit();
    }
?>