<?php
    session_start();
    if (!isset($_SESSION["id_usuario"])) {
        header("Location: ../Login.html");
        exit();
    }
    include '../../conexao.php';
    $result_turmas = $conn->query("SELECT id_turma, nome_turma FROM turma ORDER BY nome_turma");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Mensagem - Sistema Escolar</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #23395d 0%, #4f6d7a 100%);
            min-height: 100vh;
            /* Removido o padding-top para subir a navbar */
            padding: 20px;
        }
        .navbar {
            width: 100%;
            background: linear-gradient(135deg, #23395d 0%, #4f6d7a 100%) !important;
            box-shadow: 0 2px 12px #23395d22 !important;
            border-radius: 12px !important;
            margin-bottom: 16px;
            padding: 16px 0;
            position: relative;
            z-index: 10;
        }
        .navbar-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
        }
        .navbar-title {
            color: #fff;
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .navbar-links a,
        .navbar-links button {
            color: #fff;
            text-decoration: none;
            margin-right: 24px;
            font-size: 16px;
            font-weight: 500;
            background: none;
            border: none;
            cursor: pointer;
            transition: color 0.2s, background 0.2s;
            border-radius: 7px;
            padding: 8px 18px;
        }
        .navbar-links a.active {
            color: #f7c948;
            font-weight: 700;
        }
        .navbar-links a.disabled {
            color: #bfc9d1;
            pointer-events: none;
        }
        .navbar-links button {
            background: #bfc9d1;
            color: #23395d;
            font-weight: 600;
        }
        .navbar-links button:hover {
            background: #4f6d7a;
            color: #fff;
        }
        .navbar-links button.btn-sair {
            background: #c0392b;
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 7px;
            padding: 8px 18px;
            margin-right: 0;
            transition: background 0.2s, color 0.2s;
        }
        .navbar-links button.btn-sair:hover {
            background: #a93226;
            color: #fff;
        }
        .container {
            background: #f7f9fa;
            border-radius: 15px;
            box-shadow: 0 4px 24px #23395d33;
            padding: 40px;
            width: 100%;
            max-width: 600px;
            margin: 100px auto 0 auto;
            animation: fadeIn 0.7s;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px);}
            to { opacity: 1; transform: translateY(0);}
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #23395d;
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .header p {
            color: #4f6d7a;
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;
            color: #23395d;
            font-weight: 500;
            font-size: 15px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid #bfc9d1;
            border-radius: 8px;
            font-size: 16px;
            background-color: #f7f9fa;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #23395d;
            background-color: #fff;
            box-shadow: 0 0 0 2px #23395d22;
        }

        select.form-control {
            cursor: pointer;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
            font-family: inherit;
        }

        .btn-enviar {
            width: 100%;
            background: linear-gradient(135deg, #23395d 0%, #4f6d7a 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 2px 8px #23395d22;
        }

        .btn-enviar:hover {
            background: linear-gradient(135deg, #1a2940 0%, #23395d 100%);
            box-shadow: 0 4px 16px #23395d33;
        }

        .btn-enviar:active {
            background: #23395d;
        }

        .required {
            color: #c0392b;
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-row .form-group {
            flex: 1;
        }

        @media (max-width: 768px) {
            .container {
                padding: 25px;
                margin: 90px 10px 0 10px;
            }
            .header h1 {
                font-size: 22px;
            }
        }

        @media (max-width: 600px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }

        .char-counter {
            text-align: right;
            font-size: 12px;
            color: #4f6d7a;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <?php
    if (isset($_SESSION['error_message'])) {
        echo "<script>alert('" . $_SESSION['error_message'] . "');</script>";
        unset($_SESSION['error_message']);
    }
    ?>
    <nav class="navbar">
        <div class="navbar-content">
            <span class="navbar-title">
                <i class="bi bi-mortarboard-fill" style="color: #f7c948; font-size: 1.3em; margin-right: 12px;"></i>
                Sistema Escolar Etec
            </span>
            <div class="navbar-links">
                <a href="reservar_laboratorio.php"><i class="bi bi-pc-display-horizontal me-1"></i>Reservar Laboratório</a>
                <a href="mensagem.php" class="active"><i class="bi bi-chat-dots me-1"></i>Mensagens</a>
                <a href="problema.php"><i class="bi bi-tools me-1"></i>Enviar Problema</a>
                <button class="btn-sair" onclick="window.location.href='../Login.html'">
                    <i class="bi bi-box-arrow-right me-1"></i>Sair
                </button>
            </div>
        </div>
    </nav>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <div class="container">
        <div class="header">
            <h1>Enviar Mensagem</h1>
            <p>Comunique-se com suas turmas de forma eficiente</p>
        </div>

        <form id="mensagemForm" method="post" action="../../src/save_message.php">
            <div class="form-row">
                <div class="form-group">
                    <label for="turma">
                        Selecionar Turma <span class="required">*</span>
                    </label>
                    <select id="turma" name="turma" class="form-control" required>
                        <option value="">Escolha uma turma...</option>
                        <?php while($turma = $result_turmas->fetch_assoc()): ?>
                            <option value="<?= $turma['id_turma'] ?>"><?= htmlspecialchars($turma['nome_turma']) ?></option>
                        <?php endwhile; ?>
                        <option value="todas">Todas as Turmas</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="dataEnvio">
                        Data de Envio <span class="required">*</span>
                    </label>
                    <input type="datetime-local" id="dataEnvio" name="dataEnvio" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label for="assunto">
                    Assunto da Mensagem <span class="required">*</span>
                </label>
                <input type="text" id="assunto" name="assunto" class="form-control" 
                       placeholder="Digite o assunto da mensagem..." maxlength="100" required>
            </div>

            <div class="form-group">
                <label for="mensagem">
                    Mensagem <span class="required">*</span>
                </label>
                <textarea id="mensagem" name="mensagem" class="form-control" 
                          placeholder="Digite sua mensagem aqui..." 
                          maxlength="500" required></textarea>
                <div class="char-counter">
                    <span id="charCount">0</span>/500 caracteres
                </div>
            </div>

            <button type="submit" class="btn-enviar">
                Enviar Mensagem
            </button>
        </form>
    </div>

    <script>
        const mensagemTextarea = document.getElementById('mensagem');
        const charCount = document.getElementById('charCount');

        mensagemTextarea.addEventListener('input', function() {
            const count = this.value.length;
            charCount.textContent = count;
            if (count > 450) {
                charCount.style.color = '#c0392b';
            } else {
                charCount.style.color = '#4f6d7a';
            }
        });

        const dataEnvioInput = document.getElementById('dataEnvio');
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        dataEnvioInput.min = `${year}-${month}-${day}T${hours}:${minutes}`;
        dataEnvioInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
    </script>
</body>
</html>