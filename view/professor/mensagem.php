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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
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

     <!-- Navbar Bootstrap -->
        <nav class="navbar navbar-expand-lg bg-body-tertiary shadow-sm mb-4">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="#">
                    <i class="bi bi-mortarboard-fill me-2 fs-3" style="color: #f7c948;"></i>
                    <span class="fw-bold">Sistema Escolar Etec</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarProfessor" aria-controls="navbarProfessor" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarProfessor">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active fw-bold" href="reservar_laboratorio.php"><i class="bi bi-pc-display-horizontal me-1"></i>Laboratórios</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="mensagem.php"><i class="bi bi-chat-dots me-1"></i>Mensagens</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="problema.php"><i class="bi bi-tools me-1"></i>Enviar Problema</a>
                        </li>
                        <li class="nav-item">
                            <button class="btn btn-danger" onclick="window.location.href='../Login.html'" style="margin-left:12px;"><i class="bi bi-box-arrow-right me-1"></i>Sair</button>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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