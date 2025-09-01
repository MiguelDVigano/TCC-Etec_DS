<?php
    session_start();
    if (!isset($_SESSION["id_usuario"])) {
        header("Location: login.html");
        exit();
    }
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
            padding: 20px;
        }

        nav {
            width: 100%;
            background: linear-gradient(135deg, #23395d 0%, #4f6d7a 100%);
            padding: 16px 0;
            box-shadow: 0 2px 8px #23395d12;
            position: fixed;
            top: 0;
            left: 0;
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
            font-size: 20px;
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
            transition: color 0.2s;
        }

        .navbar-links a:last-child,
        .navbar-links button:last-child {
            margin-right: 0;
        }

        .navbar-links button {
            background: #c0392b;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: 600;
            transition: background 0.2s;
        }

        .navbar-links button:hover {
            background: #a93226;
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
    <nav>
        <div class="navbar-content">
            <span class="navbar-title">Sistema TCC Etec</span>
            <div class="navbar-links">
                <a href="reservar_laboratorio.html">Reservar Laboratório</a>
                <a href="mensagem.html">Mensagens</a>
                <button onclick="window.location.href='Login.html'">Sair</button>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="header">
            <h1>Enviar Mensagem</h1>
            <p>Comunique-se com suas turmas de forma eficiente</p>
        </div>

        <form id="mensagemForm" method="post" action="../controler/save_message.php">
            <div class="form-row">
                <div class="form-group">
                    <label for="turma">
                        Selecionar Turma <span class="required">*</span>
                    </label>
                    <select id="turma" name="turma" class="form-control" required>
                        <option value="">Escolha uma turma...</option>
                        <option value="1ano-a">1º Ano A</option>
                        <option value="1ano-b">1º Ano B</option>
                        <option value="2ano-a">2º Ano A</option>
                        <option value="2ano-b">2º Ano B</option>
                        <option value="3ano-a">3º Ano A</option>
                        <option value="3ano-b">3º Ano B</option>
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

        document.getElementById('mensagemForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const turma = document.getElementById('turma').value;
            const dataEnvio = document.getElementById('dataEnvio').value;
            const assunto = document.getElementById('assunto').value;
            const mensagem = document.getElementById('mensagem').value;

            if (!turma || !dataEnvio || !assunto || !mensagem) {
                alert('Por favor, preencha todos os campos obrigatórios.');
                return;
            }

            const btnEnviar = document.querySelector('.btn-enviar');
            btnEnviar.textContent = 'Enviando...';
            btnEnviar.disabled = true;

            setTimeout(() => {
                alert('Mensagem enviada com sucesso!');
                btnEnviar.textContent = 'Enviar Mensagem';
                btnEnviar.disabled = false;
                document.getElementById('mensagemForm').reset();
                charCount.textContent = '0';
                dataEnvioInput.value = `${year}-