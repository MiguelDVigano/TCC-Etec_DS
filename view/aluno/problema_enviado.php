<?php
session_set_cookie_params([
    'lifetime' => 3600, // 1 hora
    'path' => '/',
    'domain' => '', // Deixe vazio para usar o domínio atual
    'secure' => false, // Use true se estiver usando HTTPS
    'httponly' => true,
    'samesite' => 'Lax' // Ou 'Strict' dependendo do caso
]);
session_start();
if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo_usuario"] !== "Aluno") {
    header("Location: ../../src/logout.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Problema Enviado</title>
    <style>
        body {
            background: #f5f5f5;
            min-height: 100vh;
            margin: 0;
        }

        /* Estilo do pop-up */
        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            color: #333;
            padding: 32px 40px;
            border-radius: 12px;
            border: 1px solid #222;
            box-shadow: 0 4px 24px rgba(44, 62, 80, 0.18);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.5s ease, visibility 0.5s ease;
            z-index: 9999;
            font-family: Arial, sans-serif;
            font-size: 1.2rem;
            text-align: center;
            min-width: 320px;
        }

        .popup.show {
            opacity: 1;
            visibility: visible;
        }

        .popup .icon {
            font-size: 2.5rem;
            color: #4CAF50;
            margin-bottom: 12px;
            display: block;
        }

        .popup .title {
            font-weight: bold;
            font-size: 1.3rem;
            margin-bottom: 8px;
        }

        .popup .subtitle {
            color: #555;
            font-size: 1rem;
        }
    </style>
</head>

<body>

    <!-- Pop-up -->
    <div id="popup" class="popup">
        <span class="icon">&#10003;</span>
        <div class="title">Problema enviado com sucesso!</div>
        <div class="subtitle">Em breve sua solicitação será analisada e resolvida.</div>
    </div>

    <script>
        window.onload = function () {
            var popup = document.getElementById("popup");
            popup.classList.add("show");

            // Esconder depois de 3 segundos
            setTimeout(function () {
                popup.classList.remove("show");

                // Redirecionar para problema_aluno.php (opcional)
                window.location.href = "problema_aluno.php";
            }, 3000);
        }
    </script>

</body>

</html>