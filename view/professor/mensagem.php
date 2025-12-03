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

if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo_usuario"] !== "Professor") {
    header("Location: ../../src/logout.php");
    exit();
}

include '../../controller/conexao.php';
include '../../src/funcao_professor.php';

// --- INÍCIO: Adicionar filtros de pesquisa ---
$result_turmas = buscarTurmas($conn);
list($filtro_assunto, $filtro_data) = obterFiltrosMensagem();
$result_mensagens_enviadas = buscarMensagensEnviadas($conn, $_SESSION["id_usuario"], $filtro_assunto, $filtro_data);
// --- FIM: Adicionar filtros de pesquisa ---
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
            background: linear-gradient(135deg, #23395d 0%, #4f6d7a 100%) !important;
            min-height: 100vh;
        }

        .navbar {
            background: linear-gradient(135deg, #23395d 0%, #4f6d7a 100%) !important;
            border-radius: 12px;
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
            background: rgba(247, 249, 250, 0.95) !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 24px rgba(35, 57, 93, 0.2) !important;
            border: none !important;
            backdrop-filter: blur(10px);
        }

        .card-title,
        h3 {
            color: #23395d !important;
            font-weight: 700;
        }

        h3.bg-title {
            background: linear-gradient(135deg, #23395d 0%, #4f6d7a 100%);
            color: #fff !important;
            border-radius: 10px;
            padding: 16px 0;
            margin-bottom: 32px;
        }

        .btn-danger {
            background: linear-gradient(135deg, #a93226 0%, #922b21 100%) !important;
            color: #fff !important;
            border: none !important;
            border-radius: 7px !important;
            font-weight: 600;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #922b21 0%, #7d251c 100%) !important;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20754a 100%) !important;
            border: none !important;
            border-radius: 7px !important;
            font-weight: 600;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #20754a 0%, #1a5f3a 100%) !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, #23395d 0%, #4f6d7a 100%) !important;
            border: none !important;
            border-radius: 7px !important;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1c2d47 0%, #425965 100%) !important;
        }

        .alert-info {
            background: rgba(234, 241, 251, 0.9);
            color: #23395d;
            border: 1px solid rgba(35, 57, 93, 0.2);
            backdrop-filter: blur(5px);
        }

        .container {
            position: relative;
            z-index: 1;
        }
    </style>
</head>

<body class="min-vh-100">
    <!-- Navbar Bootstrap -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-gradient shadow-sm mb-4" style="background: linear-gradient(135deg, #23395d 0%, #4f6d7a 100%)">
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
                        <a class="nav-link" href="laboratorios.php"><i class="bi bi-pc-display-horizontal me-1"></i>Laboratórios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active fw-bold" href="#"><i class="bi bi-chat-dots me-1"></i>Mensagens</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="problema_professor.php"><i class="bi bi-tools me-1"></i>Enviar Problema</a>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-danger ms-lg-2 mt-2 mt-lg-0" onclick="window.location.href='../../src/logout.php'"><i class="bi bi-box-arrow-right me-1"></i>Sair</button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row g-4">
            <!-- Formulário de Envio -->
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h3 class="text-center mb-4 bg-title">
                            <i class="bi bi-chat-dots me-2"></i>Enviar Mensagem
                        </h3>
                        <form action="../../src/enviar_mensagem.php" method="POST">
                            <div class="mb-3">
                                <label for="assunto" class="form-label fw-semibold">Assunto</label>
                                <input type="text" class="form-control" id="assunto" name="assunto" required>
                            </div>

                            <div class="mb-3">
                                <label for="mensagem" class="form-label fw-semibold">Mensagem</label>
                                <textarea class="form-control" id="mensagem" name="mensagem" rows="4" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="data_expiracao" class="form-label fw-semibold">Data de Expiração</label>
                                <input type="datetime-local" class="form-control" id="data_expiracao" name="data_expiracao" required>
                            </div>

                            <div class="mb-3">
                                <label for="turmas" class="form-label fw-semibold">Turmas Destinatárias</label>
                                <select class="form-select" id="turmas" name="turmas[]" multiple size="3">
                                    <?php
                                    if ($result_turmas && $result_turmas->num_rows > 0) {
                                        while ($turma = $result_turmas->fetch_assoc()) {
                                            echo '<option value="' . $turma['id_turma'] . '">' . htmlspecialchars($turma['nome_turma']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="form-text">Segure Ctrl (ou Cmd no Mac) para selecionar múltiplas turmas.</div>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="enviar_para_todas" value="1" id="enviarParaTodas">
                                <label class="form-check-label" for="enviarParaTodas">
                                    Enviar para todas as turmas
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-send me-2"></i>Enviar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Mensagens Enviadas -->
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h3 class="text-center mb-4 bg-title">
                            <i class="bi bi-clock-history me-2"></i>Mensagens Enviadas
                        </h3>

                        <!-- Filtros de pesquisa -->
                        <form class="row g-2 mb-4" method="get" action="">
                            <div class="col-12 col-sm-7">
                                <input type="text" class="form-control" name="filtro_assunto" placeholder="Pesquisar por assunto"
                                    value="<?php echo htmlspecialchars($filtro_assunto); ?>">
                            </div>
                            <div class="col-8 col-sm-3">
                                <input type="date" class="form-control" name="filtro_data"
                                    value="<?php echo htmlspecialchars($filtro_data); ?>">
                            </div>
                            <div class="col-4 col-sm-2">
                                <button type="submit" class="btn btn-primary w-100" title="Pesquisar">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>

                        <!-- Lista de mensagens -->
                        <div class="overflow-auto" style="max-height: 400px;">
                            <?php if ($result_mensagens_enviadas && $result_mensagens_enviadas->num_rows > 0): ?>
                                <?php while ($msg = $result_mensagens_enviadas->fetch_assoc()): ?>
                                    <div class="card mb-3 h-auto shadow-sm">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0 fw-bold text-primary-emphasis">
                                                    <?php echo htmlspecialchars($msg['assunto']); ?>
                                                </h6>
                                                <div class="d-flex align-items-center gap-2">
                                                    <button type="button" class="btn btn-outline-info btn-sm"
                                                        title="Ver informações de leitura"
                                                        onclick="abrirInfoLeitura(<?php echo $msg['id_mensagem']; ?>)">
                                                        <i class="bi bi-info-circle"></i>
                                                    </button>
                                                    <small class="text-muted">
                                                        <?php echo date('d/m/Y H:i', strtotime($msg['data_envio'])); ?>
                                                    </small>
                                                </div>
                                            </div>
                                            <p class="card-text text-muted mb-2">
                                                <?php echo nl2br(htmlspecialchars(substr($msg['mensagem'], 0, 100))); ?>
                                                <?php if (strlen($msg['mensagem']) > 100) echo '...'; ?>
                                            </p>
                                            <small class="text-secondary">
                                                <i class="bi bi-people me-1"></i><strong>Destinatários:</strong>
                                                <?php echo htmlspecialchars($msg['turmas_destinatarias']); ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="alert alert-info text-center rounded-3">
                                    <i class="bi bi-info-circle me-2"></i>Você ainda não enviou mensagens.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de informações de leitura -->
    <div class="modal fade" id="modalInfoLeitura" tabindex="-1" aria-labelledby="modalInfoLeituraLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalInfoLeituraLabel">
                        <i class="bi bi-info-circle me-2"></i>Informações de Leitura
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div id="infoLeituraConteudo">
                        <div class="text-center text-muted">
                            <i class="bi bi-arrow-repeat"></i> Carregando...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function abrirInfoLeitura(idMensagem) {
            const modal = new bootstrap.Modal(document.getElementById('modalInfoLeitura'));
            document.getElementById('infoLeituraConteudo').innerHTML = '<div class="text-center text-muted"><i class="bi bi-arrow-repeat"></i> Carregando...</div>';
            modal.show();
            fetch('../../src/info_leitura_mensagem.php?id_mensagem=' + idMensagem)
                .then(resp => resp.text())
                .then(html => {
                    document.getElementById('infoLeituraConteudo').innerHTML = html;
                })
                .catch(() => {
                    document.getElementById('infoLeituraConteudo').innerHTML = '<div class="alert alert-danger">Erro ao carregar informações.</div>';
                });
        }
    </script>
</body>

</html>
<?php
$conn->close();
?>