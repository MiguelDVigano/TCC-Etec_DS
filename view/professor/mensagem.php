<?php
session_start();

if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo_usuario"] !== "Professor") {
    header("Location: ../Login.html");
    exit();
}

include '../../conexao.php';
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
    <title>Enviar Mensagem - Professor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #23395d 0%, #4f6d7a 100%) !important;
            min-height: 100vh;
        }

        .navbar-custom {
            background: linear-gradient(135deg, #23395d 0%, #4f6d7a 100%) !important;
            border-radius: 12px;
        }

        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link,
        .navbar-custom .navbar-toggler {
            color: #fff !important;
        }

        .navbar-custom .nav-link.active,
        .navbar-custom .nav-link:focus {
            color: #f7c948 !important;
        }

        .navbar-custom .nav-link.disabled {
            color: #bfc9d1 !important;
        }

        .card,
        .modal-content {
            background: #f7f9fa !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 24px #23395d33 !important;
            border: none !important;
        }

        .card-title,
        .modal-title,
        h3,
        h4 {
            color: #23395d !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-weight: 700;
        }

        .btn-primary,
        .btn-success {
            border-radius: 7px !important;
            font-weight: 600;
        }

        .btn-danger {
            border-radius: 7px !important;
        }

        .form-label {
            color: #23395d !important;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-custom shadow-sm mb-4">
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
                        <a class="nav-link" href="laboratorios.php"><i class="bi bi-pc-display-horizontal me-1"></i>Laboratórios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active fw-bold" href="#"><i class="bi bi-chat-dots me-1"></i>Mensagens</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="problema_professor.php"><i class="bi bi-tools me-1"></i>Enviar Problema</a>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-danger ms-2" onclick="window.location.href='../../src/logout.php'"><i class="bi bi-box-arrow-right me-1"></i>Sair</button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container py-5">
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow-lg border-0 h-100">
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
                                <label for="data_expiracao" class="form-label">Data de Expiração</label>
                                <input type="datetime-local" class="form-control" id="data_expiracao" name="data_expiracao" required>
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
                                <input class="form-check-input" type="checkbox" name="enviar_para_todas" value="1" id="enviarParaTodas">
                                <label class="form-check-label" for="enviarParaTodas">
                                    Enviar para todas as turmas
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-send me-1"></i>Enviar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-lg border-0 h-100">
                    <div class="card-body p-4">
                        <h4 class="text-center mb-4"><i class="bi bi-clock-history me-2"></i>Mensagens Enviadas</h4>
                        <!-- INÍCIO: Formulário de pesquisa -->
                        <form class="row g-2 mb-3" method="get" action="">
                            <div class="col-7">
                                <input type="text" class="form-control" name="filtro_assunto" placeholder="Pesquisar por assunto"
                                    value="<?php echo htmlspecialchars($filtro_assunto); ?>">
                            </div>
                            <div class="col-4">
                                <input type="date" class="form-control" name="filtro_data" value="<?php echo htmlspecialchars($filtro_data); ?>">
                            </div>
                            <div class="col-1 d-grid">
                                <button type="submit" class="btn btn-primary" title="Pesquisar">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                        <!-- FIM: Formulário de pesquisa -->
                        <div class="list-group list-group-flush">
                            <?php if ($result_mensagens_enviadas && $result_mensagens_enviadas->num_rows > 0): ?>
                            <?php while ($msg = $result_mensagens_enviadas->fetch_assoc()): ?>
                            <div class="list-group-item list-group-item-action mb-2 rounded-3">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1 fw-bold"><?php echo htmlspecialchars($msg['assunto']); ?></h5>
                                    <div>
                                        <button type="button" class="btn btn-outline-info btn-sm me-2"
                                            title="Ver informações de leitura"
                                            onclick="abrirInfoLeitura(<?php echo $msg['id_mensagem']; ?>)">
                                            <i class="bi bi-info-circle"></i>
                                        </button>
                                        <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($msg['data_envio'])); ?></small>
                                    </div>
                                </div>
                                <p class="mb-1 text-muted"><?php echo nl2br(htmlspecialchars($msg['mensagem'])); ?></p>
                                <small class="text-secondary">
                                    <strong>Destinatários:</strong> <?php echo !empty($msg['turmas_destinatarias']) ? htmlspecialchars($msg['turmas_destinatarias']) : 'Todas as turmas'; ?>
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
    <!-- Modal de informações de leitura -->
    <div class="modal fade" id="modalInfoLeitura" tabindex="-1" aria-labelledby="modalInfoLeituraLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalInfoLeituraLabel"><i class="bi bi-info-circle me-2"></i>Informações de Leitura</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body">
            <div id="infoLeituraConteudo">
              <div class="text-center text-muted"><i class="bi bi-arrow-repeat"></i> Carregando...</div>
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