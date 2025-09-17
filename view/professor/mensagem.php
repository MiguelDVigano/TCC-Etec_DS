<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../Login.html");
    exit();
}

include '../../conexao.php';

// Busca as turmas para o dropdown de seleção
$sql_turmas = "SELECT id_turma, nome_turma FROM turma ORDER BY nome_turma";
$result_turmas = $conn->query($sql_turmas);

// Busca as mensagens enviadas pelo professor logado
$id_remetente = $_SESSION["id_usuario"];
$sql_mensagens_enviadas = "
    SELECT m.assunto, m.mensagem, m.data_envio,
           (SELECT GROUP_CONCAT(t.nome_turma SEPARATOR ', ')
            FROM mensagem_turma mt
            JOIN turma t ON mt.id_mensagem = m.id_mensagem
            WHERE mt.id_mensagem = m.id_mensagem) AS turmas_destinatarias
    FROM mensagem m
    WHERE m.id_remetente = $id_remetente
    ORDER BY m.data_envio DESC
";
$result_mensagens_enviadas = $conn->query($sql_mensagens_enviadas);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Enviar Mensagem - Professor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #23395d 0%, #4f6d7a 100%);
            min-height: 100vh;
        }

        .navbar {
            background: linear-gradient(135deg, #23395d 0%, #4f6d7a 100%) !important;
            box-shadow: 0 2px 12px #23395d22 !important;
            border-radius: 12px !important;
            margin-bottom: 32px;
        }

        .navbar .navbar-brand,
        .navbar .nav-link,
        .navbar .navbar-toggler {
            color: #fff !important;
        }

        .navbar .nav-link.active,
        .navbar .nav-link:focus {
            color: #f7c948 !important;
        }

        .navbar .nav-link.disabled {
            color: #bfc9d1 !important;
        }

        .card {
            background: #f7f9fa !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 24px #23395d33 !important;
            border: none !important;
        }

        .card-title,
        h3,
        h4 {
            color: #23395d !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-weight: 700;
        }

        .form-label {
            color: #23395d !important;
            font-weight: 500;
        }

        .form-control,
        .form-select,
        textarea {
            background-color: #f7f9fa !important;
            border: 1.5px solid #bfc9d1 !important;
            border-radius: 7px !important;
            font-size: 15px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-control:focus,
        .form-select:focus,
        textarea:focus {
            outline: none;
            border-color: #23395d !important;
            box-shadow: 0 0 0 2px #23395d22 !important;
            background-color: #fff !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, #23395d 0%, #4f6d7a 100%) !important;
            color: #fff !important;
            border: none !important;
            border-radius: 7px !important;
            font-size: 16px;
            font-weight: 600;
            box-shadow: 0 2px 8px #23395d22;
            transition: background 0.2s, box-shadow 0.2s;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1a2940 0%, #23395d 100%) !important;
            box-shadow: 0 4px 16px #23395d33;
        }

        .btn-primary:active {
            background: #23395d !important;
        }

        .btn-danger {
            background: #c0392b !important;
            color: #fff !important;
            border: none !important;
            border-radius: 7px !important;
            font-size: 16px;
            font-weight: 600;
            box-shadow: 0 2px 8px #23395d22;
            transition: background 0.2s, color 0.2s;
        }

        .btn-danger:hover {
            background: #a93226 !important;
            color: #fff !important;
        }

        /* Responsividade extra para telas pequenas */
        @media (max-width: 991.98px) {
            .navbar {
                border-radius: 0 !important;
                margin-bottom: 16px;
            }
        }

        @media (max-width: 767.98px) {
            .container.py-5 {
                padding-top: 2rem !important;
                padding-bottom: 2rem !important;
            }

            .card {
                margin-bottom: 2rem;
            }
        }

        @media (max-width: 575.98px) {
            .navbar .navbar-brand span {
                font-size: 1rem;
            }

            .navbar .navbar-brand i {
                font-size: 1.2rem !important;
            }

            .card-body {
                padding: 1rem !important;
            }

            h3,
            h4 {
                font-size: 1.2rem !important;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="bi bi-mortarboard-fill me-2 fs-3" style="color: #f7c948;"></i>
                <span class="fw-bold">Sistema Escolar Etec</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarProfessor"
                aria-controls="navbarProfessor" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarProfessor">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="reservar_laboratorio.php"><i class="bi bi-pc-display-horizontal me-1"></i>Laboratórios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active fw-bold" href="mensagem.php"><i class="bi bi-chat-dots me-1"></i>Mensagens
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="problema.php"><i class="bi bi-tools me-1"></i>Enviar Problema</a>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-danger" onclick="window.location.href='../Login.html'"
                            style="margin-left:12px;"><i class="bi bi-box-arrow-right me-1"></i>Sair</button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-12 col-md-6 mb-4">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4"><i class="bi bi-chat-dots me-2"></i>Enviar Mensagem</h3>
                        <form action="../../src/enviar_mensagem.php" method="POST">
                            <div class="mb-3">
                                <label for="assunto" class="form-label">Assunto</label>
                                <input type="text" class="form-control" id="assunto" name="assunto" required>
                            </div>
                            <div class="mb-3">
                                <label for="mensagem" class="form-label">Mensagem</label>
                                <textarea class="form-control" id="mensagem" name="mensagem" rows="5" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="turmas" class="form-label">Turmas Destinatárias</label>
                                <select class="form-select" id="turmas" name="turmas[]" multiple>
                                    <?php
                                    if ($result_turmas && $result_turmas->num_rows > 0) {
                                        while ($turma = $result_turmas->fetch_assoc()) {
                                            echo '<option value="' . $turma['id_turma'] . '">' . htmlspecialchars($turma['nome_turma']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="form-text mt-2">Segure Ctrl (ou Cmd no Mac) para selecionar múltiplas turmas.
                                </div>
                            </div>
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" name="enviar_para_todas" value="1"
                                    id="enviarParaTodas">
                                <label class="form-check-label" for="enviarParaTodas">
                                    Enviar para todas as turmas
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 rounded-3">
                                <i class="bi bi-send me-1"></i>Enviar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-4">
                        <h4 class="text-center mb-4"><i class="bi bi-clock-history me-2"></i>Mensagens Enviadas</h4>
                        <div class="list-group list-group-flush">
                            <?php if ($result_mensagens_enviadas && $result_mensagens_enviadas->num_rows > 0): ?>
                            <?php while ($msg = $result_mensagens_enviadas->fetch_assoc()): ?>
                            <div class="list-group-item list-group-item-action mb-2 rounded-3">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1 fw-bold"><?php echo htmlspecialchars($msg['assunto']); ?></h5>
                                    <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($msg['data_envio'])); ?></small>
                                </div>
                                <p class="mb-1 text-muted"><?php echo nl2br(htmlspecialchars($msg['mensagem'])); ?></p>
                                <small class="text-secondary">
                                    **Destinatários:** <?php echo !empty($msg['turmas_destinatarias']) ? htmlspecialchars($msg['turmas_destinatarias']) : 'Todas as turmas'; ?>
                                </small>
                            </div>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <div class="alert alert-info text-center">Você ainda não enviou mensagens.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
$conn->close();
?>