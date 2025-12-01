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
    header("Location: ../Login.html");
    exit();
}

include '../../conexao.php';

// --- NOVO: mapeamento de períodos para horários (ajuste conforme sua grade) ---
$PERIODOS_HORARIOS = [
    '1' => ['07:00:00', '07:50:00'],
    '2' => ['07:50:00', '08:40:00'],
    '3' => ['08:40:00', '09:30:00'],
    '4' => ['09:50:00', '10:40:00'],
    '5' => ['10:40:00', '11:30:00'],
    '6' => ['11:30:00', '12:20:00'],
];

// --- NOVO: Endpoints AJAX no mesmo arquivo ---
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    header('Content-Type: application/json; charset=utf-8');

    // Ajuste nomes da tabela/colunas abaixo conforme seu banco.
    $tabelaReservas = 'reserva_laboratorio'; // ex.: reserva_laboratorio
    $colIdReserva   = 'id_reserva';
    $colIdSala      = 'id_sala';
    $colIdProf      = 'id_professor';
    $colData        = 'data_reserva';
    $colInicio      = 'hora_inicio';
    $colFim         = 'hora_fim';

    $action  = $_GET['action'] ?? '';
    $data    = $_GET['data'] ?? '';
    $periodo = $_GET['periodo'] ?? '';
    $idProf  = $_SESSION['id_usuario'];

    // Converte período em hora início/fim (se informado)
    $horaInicio = null;
    $horaFim = null;
    if ($periodo && isset($PERIODOS_HORARIOS[$periodo])) {
        [$horaInicio, $horaFim] = $PERIODOS_HORARIOS[$periodo];
    }

    try {
        if ($action === 'disponiveis') {
            if (!$data || !$horaInicio || !$horaFim) {
                echo json_encode(['ok' => false, 'error' => 'Data e período são obrigatórios.']);
                exit;
            }
            // Lista laboratórios sem sobreposição no horário solicitado
            $sql = "
                SELECT s.id_sala, s.titulo_sala
                FROM sala s
                WHERE NOT EXISTS (
                    SELECT 1
                    FROM {$tabelaReservas} r
                    WHERE r.{$colIdSala} = s.id_sala
                      AND r.{$colData} = ?
                      AND NOT (r.{$colFim} <= ? OR r.{$colInicio} >= ?)
                )
                ORDER BY s.titulo_sala ASC
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sss', $data, $horaInicio, $horaFim);
            $stmt->execute();
            $res = $stmt->get_result();
            $rows = [];
            while ($row = $res->fetch_assoc()) {
                $rows[] = [
                    'id_sala' => $row['id_sala'],
                    'titulo_sala' => $row['titulo_sala']
                ];
            }
            echo json_encode(['ok' => true, 'data' => $rows]);
            exit;
        }

        if ($action === 'minhas_reservas') {
            // NOVO: data agora é opcional; se não informada, retorna TODAS as reservas do professor
            $params = [$idProf];
            $types  = 'i';
            $where  = "WHERE r.{$colIdProf} = ?";

            if (!empty($data)) {
                $where .= " AND r.{$colData} = ?";
                $types .= 's';
                $params[] = $data;

                if ($horaInicio && $horaFim) {
                    $where .= " AND NOT (r.{$colFim} <= ? OR r.{$colInicio} >= ?)";
                    $types .= 'ss';
                    $params[] = $horaInicio;
                    $params[] = $horaFim;
                }
            }

            $sql = "
                SELECT r.{$colIdReserva} as id_reserva, s.titulo_sala, r.{$colData} as data_reserva,
                       r.{$colInicio} as hora_inicio, r.{$colFim} as hora_fim
                FROM {$tabelaReservas} r
                INNER JOIN sala s ON s.id_sala = r.{$colIdSala}
                {$where}
                ORDER BY r.{$colData} ASC, r.{$colInicio} ASC
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $res = $stmt->get_result();
            $rows = [];
            while ($row = $res->fetch_assoc()) {
                $rows[] = $row;
            }
            echo json_encode(['ok' => true, 'data' => $rows]);
            exit;
        }

        echo json_encode(['ok' => false, 'error' => 'Ação inválida.']);
    } catch (Throwable $e) {
        echo json_encode(['ok' => false, 'error' => 'Falha ao processar a solicitação.']);
    }
    exit;
}

// Buscar laboratórios
$sql = "SELECT * FROM sala";
$result = $conn->query($sql);

// Buscar turmas e professores para o formulário
$turmas = $conn->query("SELECT id_turma, nome_turma FROM turma");
$professores = $conn->query("SELECT id_usuario AS id_professor, nome FROM usuario WHERE tipo_usuario = 'Professor'");
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reserva de Laboratórios</title>
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
        .navbar .navbar-brand, .navbar .nav-link, .navbar .navbar-toggler {
            color: #fff !important;
        }
        .navbar .nav-link.active, .navbar .nav-link:focus {
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
        .card-title, h3 {
            color: #23395d !important;
            font-weight: 700;
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
        .container {
            position: relative;
            z-index: 1;
        }
        /* NOVO: estilos do pré-filtro */
        .prefiltro-card { background: rgba(247, 249, 250, 0.95) !important; border-radius: 12px; box-shadow: 0 4px 24px rgba(35, 57, 93, 0.2) !important; border: none; }
        .prefiltro-actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .hidden { display: none !important; }
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
                        <a class="nav-link active fw-bold" href="#"><i class="bi bi-pc-display-horizontal me-1"></i>Laboratórios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="mensagem.php"><i class="bi bi-chat-dots me-1"></i>Mensagens</a>
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
        <!-- NOVO: Pré-filtro -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card prefiltro-card p-3">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold">Data</label>
                            <input type="date" class="form-control" id="pf-data" />
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold">Período da aula</label>
                            <select class="form-select" id="pf-periodo">
                                <option value="">Selecione</option>
                                <option value="1">1ª aula</option>
                                <option value="2">2ª aula</option>
                                <option value="3">3ª aula</option>
                                <option value="4">4ª aula</option>
                                <option value="5">5ª aula</option>
                                <option value="6">6ª aula</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="prefiltro-actions">
                                <button type="button" class="btn btn-primary" id="pf-ver-disponiveis">
                                    <i class="bi bi-eye me-1"></i>Ver disponíveis
                                </button>
                                <button type="button" class="btn btn-success" id="pf-minhas-reservas">
                                    <i class="bi bi-person-check me-1"></i>Minhas reservas
                                </button>
                                <button type="button" class="btn btn-secondary" id="pf-limpar">
                                    <i class="bi bi-eraser me-1"></i>Limpar filtro
                                </button>
                            </div>
                        </div>
                        <div class="col-12">
                            <small id="pf-resultado" class="text-muted"></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid de Laboratórios (inicialmente OCULTA) -->
        <div class="row g-4 justify-content-center hidden" id="grid-labs">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($lab = $result->fetch_assoc()): ?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 lab-card" data-id-sala="<?= $lab['id_sala'] ?>">
                        <div class="card shadow-sm h-100">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title mb-3">
                                    <i class="bi bi-door-closed me-2"></i><?= htmlspecialchars($lab['titulo_sala']) ?>
                                </h5>
                                
                                <div class="mb-3 flex-grow-1">
                                    <div class="mb-2">
                                        <small class="text-muted">Número:</small>
                                        <span class="fw-semibold"><?= $lab['numero_sala'] ?></span>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted">Capacidade:</small>
                                        <span class="fw-semibold"><?= $lab['capacidade'] ?> alunos</span>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted">Status:</small>
                                        <?php
                                        if ($lab['status_sala'] === 'Ativa') {
                                            echo '<span class="badge bg-success ms-1"><i class="bi bi-check-circle me-1"></i>Ativo</span>';
                                        } else if ($lab['status_sala'] === 'Ocupado') {
                                            echo '<span class="badge bg-danger ms-1"><i class="bi bi-exclamation-circle me-1"></i>Ocupado</span>';
                                        } else {
                                            echo '<span class="badge bg-secondary ms-1"><i class="bi bi-x-circle me-1"></i>Inativo</span>';
                                        }
                                        ?>
                                    </div>
                                </div>
                                
                                <div class="d-grid mt-auto">
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalReserva"
                                        data-id="<?= $lab['id_sala'] ?>" data-titulo="<?= htmlspecialchars($lab['titulo_sala']) ?>"
                                        data-status="<?= $lab['status_sala'] ?>">
                                        <i class="bi bi-calendar-plus me-1"></i>Reservar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle me-2"></i>Nenhum laboratório encontrado.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Reserva -->
    <div class="modal fade" id="modalReserva" tabindex="-1" aria-labelledby="modalReservaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="../../src/reservar_laboratorio.php" id="formReserva">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalReservaLabel">
                        <i class="bi bi-calendar-plus me-2"></i>Reservar Laboratório
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_sala" id="inputIdSala">
                    
                    <div class="mb-3">
                        <label for="inputTituloSala" class="form-label fw-semibold">Laboratório</label>
                        <input type="text" class="form-control" id="inputTituloSala" readonly>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label for="inputDataReserva" class="form-label fw-semibold">Data</label>
                            <input type="date" class="form-control" name="data_reserva" id="inputDataReserva" required>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Horário de Início</label>
                            <select class="form-select" name="hora_inicio" id="inputHoraInicio" required>
                                <option value="">Selecione a data primeiro</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Horário de Fim</label>
                            <select class="form-select" name="hora_fim" id="inputHoraFim" required>
                                <option value="">Selecione o início primeiro</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="inputTurma" class="form-label fw-semibold">Turma</label>
                            <select class="form-select" name="id_turma" required>
                                <option value="">Selecione</option>
                                <?php
                                $turmas->data_seek(0);
                                while ($turma = $turmas->fetch_assoc()): ?>
                                    <option value="<?= $turma['id_turma'] ?>"><?= htmlspecialchars($turma['nome_turma']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="inputProfessor" class="form-label fw-semibold">Professor</label>
                            <select class="form-select" name="id_professor" required>
                                <option value="">Selecione</option>
                                <?php
                                $professores->data_seek(0);
                                while ($prof = $professores->fetch_assoc()): ?>
                                    <option value="<?= $prof['id_professor'] ?>"><?= htmlspecialchars($prof['nome']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="inputObs" class="form-label fw-semibold">Observação</label>
                        <textarea class="form-control" name="observacao" rows="3" placeholder="Observações sobre a reserva..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i>Confirmar Reserva
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- NOVO: Modal Minhas Reservas -->
    <div class="modal fade" id="modalMinhasReservas" tabindex="-1" aria-labelledby="modalMinhasReservasLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalMinhasReservasLabel">
                        <i class="bi bi-person-check me-2"></i>Minhas reservas
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body" id="minhas-reservas-body">
                    <!-- NOVO texto padrão -->
                    <div class="text-muted">Clique em "Minhas reservas" para ver todas as suas reservas. Use a data para filtrar, se desejar.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var modalReserva = document.getElementById('modalReserva');
        var inputIdSala = document.getElementById('inputIdSala');
        var inputTituloSala = document.getElementById('inputTituloSala');
        var inputDataReserva = document.getElementById('inputDataReserva');
        var inputHoraInicio = document.getElementById('inputHoraInicio');
        var inputHoraFim = document.getElementById('inputHoraFim');
        var reservaBtnStatus = null;

        modalReserva.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var idSala = button.getAttribute('data-id');
            var tituloSala = button.getAttribute('data-titulo');
            reservaBtnStatus = button.getAttribute('data-status');
            inputIdSala.value = idSala;
            inputTituloSala.value = tituloSala;
            inputHoraInicio.innerHTML = '<option value="">Selecione a data primeiro</option>';
            inputHoraFim.innerHTML = '<option value="">Selecione o início primeiro</option>';
            inputDataReserva.value = '';
        });

        inputDataReserva.addEventListener('change', function() {
            var idSala = inputIdSala.value;
            var dataReserva = inputDataReserva.value;
            if (!idSala || !dataReserva) {
                inputHoraInicio.innerHTML = '<option value="">Selecione a data primeiro</option>';
                inputHoraFim.innerHTML = '<option value="">Selecione o início primeiro</option>';
                return;
            }
            fetch('../../src/reservar_laboratorio_horarios.php?id_sala=' + idSala + '&data_reserva=' + dataReserva)
                .then(response => response.json())
                .then(data => {
                    inputHoraInicio.innerHTML = '';
                    inputHoraFim.innerHTML = '<option value="">Selecione o início primeiro</option>';
                    if (data.length === 0) {
                        inputHoraInicio.innerHTML = '<option value="">Todos os horários reservados</option>';
                    } else {
                        inputHoraInicio.innerHTML = '<option value="">Selecione</option>';
                        data.forEach(function(slot) {
                            var inicio = slot.split('-')[0];
                            inputHoraInicio.innerHTML += '<option value="' + inicio + '">' + slot + '</option>';
                        });
                    }
                });
        });

        inputHoraInicio.addEventListener('change', function() {
            var idSala = inputIdSala.value;
            var dataReserva = inputDataReserva.value;
            var horaInicio = inputHoraInicio.value;
            if (!idSala || !dataReserva || !horaInicio) {
                inputHoraFim.innerHTML = '<option value="">Selecione o início primeiro</option>';
                return;
            }
            fetch('../../src/reservar_laboratorio_horarios.php?id_sala=' + idSala + '&data_reserva=' + dataReserva)
                .then(response => response.json())
                .then(data => {
                    inputHoraFim.innerHTML = '';
                    var found = false;
                    data.forEach(function(slot, idx) {
                        var inicio = slot.split('-')[0];
                        var fim = slot.split('-')[1];
                        if (inicio === horaInicio) found = true;
                        if (found) {
                            inputHoraFim.innerHTML += '<option value="' + fim + '">' + slot + '</option>';
                        }
                    });
                });
        });

        // NOVO: Lógica do pré-filtro
        const pfData = document.getElementById('pf-data');
        const pfPeriodo = document.getElementById('pf-periodo');
        const pfVerDisp = document.getElementById('pf-ver-disponiveis');
        const pfMinhasReservas = document.getElementById('pf-minhas-reservas');
        const pfLimpar = document.getElementById('pf-limpar');
        const pfResultado = document.getElementById('pf-resultado');
        const gridLabs = document.getElementById('grid-labs');

        function resetFiltro() {
            // Oculta a grid e limpa mensagens/filtros
            gridLabs.classList.add('hidden');
            pfResultado.textContent = '';
            pfData.value = '';
            pfPeriodo.value = '';
            // Prepara todos os cards para próximo filtro
            document.querySelectorAll('.lab-card').forEach(card => card.classList.remove('hidden'));
        }

        pfLimpar.addEventListener('click', resetFiltro);

        pfVerDisp.addEventListener('click', async () => {
            const data = pfData.value;
            const periodo = pfPeriodo.value;
            if (!data || !periodo) {
                pfResultado.textContent = 'Informe data e período.';
                gridLabs.classList.add('hidden');
                return;
            }
            pfResultado.textContent = 'Carregando laboratórios disponíveis...';
            try {
                const qs = new URLSearchParams({ ajax: '1', action: 'disponiveis', data, periodo });
                const res = await fetch(`./laboratorios.php?${qs.toString()}`, { headers: { 'Accept': 'application/json' } });
                const json = await res.json();
                if (!json.ok) throw new Error(json.error || 'Falha ao consultar');
                const idsDisponiveis = new Set(json.data.map(i => String(i.id_sala)));
                let visiveis = 0;
                document.querySelectorAll('.lab-card').forEach(card => {
                    const idSala = card.getAttribute('data-id-sala');
                    if (idsDisponiveis.has(idSala)) {
                        card.classList.remove('hidden');
                        visiveis++;
                    } else {
                        card.classList.add('hidden');
                    }
                });
                if (visiveis > 0) {
                    gridLabs.classList.remove('hidden');
                    pfResultado.textContent = `${visiveis} laboratório(s) disponível(is) no período selecionado.`;
                } else {
                    gridLabs.classList.add('hidden');
                    pfResultado.textContent = 'Nenhum laboratório disponível no período selecionado.';
                }
            } catch (e) {
                gridLabs.classList.add('hidden');
                pfResultado.textContent = 'Erro ao carregar disponíveis.';
            }
        });

        pfMinhasReservas.addEventListener('click', async () => {
            // NOVO: data não é obrigatória; se informada, apenas filtra
            const data = pfData.value;
            const periodo = pfPeriodo.value;
            pfResultado.textContent = 'Carregando suas reservas...';
            const qs = new URLSearchParams({ ajax: '1', action: 'minhas_reservas' });
            if (data) qs.append('data', data);
            if (data && periodo) qs.append('periodo', periodo); // período só se tiver data
            try {
                const res = await fetch(`./laboratorios.php?${qs.toString()}`, { headers: { 'Accept': 'application/json' } });
                const json = await res.json();
                const body = document.getElementById('minhas-reservas-body');
                if (!json.ok) {
                    body.innerHTML = `<div class="text-danger">${json.error || 'Falha ao consultar'}</div>`;
                } else if (!json.data || json.data.length === 0) {
                    body.innerHTML = '<div class="text-muted">Nenhuma reserva encontrada.</div>';
                } else {
                    const items = json.data.map(r =>
                        `<li class="list-group-item">
                            <div><strong>${r.titulo_sala || 'Laboratório'}</strong></div>
                            <small class="text-muted">${r.data_reserva} — ${r.hora_inicio} às ${r.hora_fim}</small>
                         </li>`
                    ).join('');
                    body.innerHTML = `<ul class="list-group">${items}</ul>`;
                }
                new bootstrap.Modal(document.getElementById('modalMinhasReservas')).show();
                pfResultado.textContent = '';
            } catch (e) {
                pfResultado.textContent = 'Erro ao carregar suas reservas.';
            }
        });

        // Inicial: garantir grid oculta
        gridLabs.classList.add('hidden');
    </script>
</body>

</html>
<!-- Botão "+" fixo no canto inferior esquerdo -->
<a href="adicionar_lab.php" 
   style="position: fixed; left: 32px; bottom: 32px; z-index: 9999; background: #f7c948; color: #fff; border-radius: 50%; width: 60px; height: 60px; display: flex; align-items: flex-start; justify-content: center; font-size: 2.5rem; box-shadow: 0 4px 24px rgba(35,57,93,0.2); text-decoration: none; border: 1px solid #23395d;"
   title="Adicionar Laboratório">
    <span style="display: flex; align-items: flex-start; justify-content: center; width: 100%; height: 100%; font-size: 2.2rem; line-height: 1; margin-top: 7px;">+</span>
</a>