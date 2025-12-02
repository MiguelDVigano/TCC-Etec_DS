<?php
session_start();

if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo_usuario"] !== "Professor") {
    header("Location: ../Login.html");
    exit();
}

include '../../controller/conexao.php';

// Obter informações do professor logado
$id_professor_logado = $_SESSION['id_usuario'];
$nome_professor_logado = $_SESSION['nome'] ?? ''; // Supondo que o nome esteja na sessão

/* ===============================
   ✅ GRADE OFICIAL POR PERÍODO
================================ */
$PERIODOS_HORARIOS = [
    '1' => ['07:10:00', '08:00:00'],
    '2' => ['08:00:00', '08:50:00'],
    '3' => ['08:50:00', '09:40:00'],
    // intervalo 09:40 às 10:00 (sem aula)
    '4' => ['10:00:00', '10:50:00'],
    '5' => ['10:50:00', '11:40:00'],
    '6' => ['11:40:00', '12:30:00'],
    // almoço 12:30 às 13:30
    '7' => ['13:30:00', '14:20:00'],
    '8' => ['14:20:00', '15:10:00'],
    '9' => ['15:10:00', '16:00:00'],
];

/* ===============================
   ✅ AJAX NO MESMO ARQUIVO
================================ */
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    header('Content-Type: application/json; charset=utf-8');

    $action = $_GET['action'] ?? '';
    $data = $_GET['data'] ?? '';
    $periodo = $_GET['periodo'] ?? '';
    $idProf = $_SESSION['id_usuario'];

    if ($periodo && isset($PERIODOS_HORARIOS[$periodo])) {
        [$horaInicio, $horaFim] = $PERIODOS_HORARIOS[$periodo];
    } else {
        $horaInicio = null;
        $horaFim = null;
    }

    try {

        /* ===== MINHAS RESERVAS ===== */
        if ($action === 'minhas_reservas') {

            $sql = "
                SELECT r.id_reserva, s.titulo_sala, r.data_reserva,
                       r.hora_inicio, r.hora_fim, r.periodo_inicio, r.periodo_fim
                FROM reserva r
                INNER JOIN sala s ON s.id_sala = r.id_sala
                WHERE r.id_professor = ?
                ORDER BY r.data_reserva, r.periodo_inicio
            ";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $idProf);
            $stmt->execute();
            $res = $stmt->get_result();

            $rows = [];
            while ($row = $res->fetch_assoc()) {
                $rows[] = $row;
            }

            echo json_encode(['ok' => true, 'data' => $rows]);
            exit;
        }

        echo json_encode(['ok' => false, 'error' => 'Ação inválida']);
        exit;
    } catch (Throwable $e) {
        echo json_encode(['ok' => false, 'error' => 'Erro interno: ' . $e->getMessage()]);
        exit;
    }
}

/* ===== LISTAGEM NORMAL ===== */
$result = $conn->query("SELECT * FROM sala");
$turmas = $conn->query("SELECT id_turma, nome_turma FROM turma");

// Buscar dados do professor logado
$sql_professor = "SELECT id_usuario, nome FROM usuario WHERE id_usuario = ?";
$stmt_prof = $conn->prepare($sql_professor);
$stmt_prof->bind_param("i", $id_professor_logado);
$stmt_prof->execute();
$result_prof = $stmt_prof->get_result();
$professor_logado = $result_prof->fetch_assoc();

// Se não encontrou, buscar todos os professores
if (!$professor_logado) {
    $professores = $conn->query("SELECT id_usuario AS id_professor, nome FROM usuario WHERE tipo_usuario = 'Professor'");
} else {
    // Criar um resultado simulado para manter compatibilidade
    $professores = new stdClass();
    $professores->data_seek = function () {};
    $professores->fetch_assoc = function () use ($professor_logado) {
        static $called = false;
        if (!$called) {
            $called = true;
            return [
                'id_professor' => $professor_logado['id_usuario'],
                'nome' => $professor_logado['nome']
            ];
        }
        return false;
    };
}
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
        .prefiltro-card {
            background: rgba(247, 249, 250, 0.95) !important;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(35, 57, 93, 0.2) !important;
            border: none;
        }

        .prefiltro-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .hidden {
            display: none !important;
        }

        .badge-periodo {
            background: #23395d;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8em;
        }
    </style>
</head>

<body class="min-vh-100">
    <!-- Navbar Bootstrap -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-gradient shadow-sm mb-4"
        style="background: linear-gradient(135deg, #23395d 0%, #4f6d7a 100%)">
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
                        <a class="nav-link active fw-bold" href="#"><i
                                class="bi bi-pc-display-horizontal me-1"></i>Laboratórios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="mensagem.php"><i class="bi bi-chat-dots me-1"></i>Mensagens</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="problema_professor.php"><i class="bi bi-tools me-1"></i>Enviar
                            Problema</a>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-danger ms-lg-2 mt-2 mt-lg-0"
                            onclick="window.location.href='../../src/logout.php'"><i
                                class="bi bi-box-arrow-right me-1"></i>Sair</button>
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
                                <option value="1">1º período — 07:10 às 08:00</option>
                                <option value="2">2º período — 08:00 às 08:50</option>
                                <option value="3">3º período — 08:50 às 09:40</option>
                                <option value="4">4º período — 10:00 às 10:50</option>
                                <option value="5">5º período — 10:50 às 11:40</option>
                                <option value="6">6º período — 11:40 às 12:30</option>
                                <option value="7">7º período — 13:30 às 14:20</option>
                                <option value="8">8º período — 14:20 às 15:10</option>
                                <option value="9">9º período — 15:10 às 16:00</option>
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
                                        data-id="<?= $lab['id_sala'] ?>"
                                        data-titulo="<?= htmlspecialchars($lab['titulo_sala']) ?>"
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
                    <input type="hidden" name="hora_inicio" id="hiddenHoraInicio" value="">
                    <input type="hidden" name="hora_fim" id="hiddenHoraFim" value="">
                    <!-- Campo hidden para o professor -->
                    <input type="hidden" name="id_professor" id="hiddenIdProfessor" value="<?= $id_professor_logado ?>">

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
                            <label class="form-label fw-semibold">Período Início</label>
                            <select class="form-select" name="periodo_inicio" id="inputPeriodoInicio" required>
                                <option value="">Selecione</option>
                                <option value="1">1º período — 07:10 às 08:00</option>
                                <option value="2">2º período — 08:00 às 08:50</option>
                                <option value="3">3º período — 08:50 às 09:40</option>
                                <option value="4">4º período — 10:00 às 10:50</option>
                                <option value="5">5º período — 10:50 às 11:40</option>
                                <option value="6">6º período — 11:40 às 12:30</option>
                                <option value="7">7º período — 13:30 às 14:20</option>
                                <option value="8">8º período — 14:20 às 15:10</option>
                                <option value="9">9º período — 15:10 às 16:00</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Período Fim</label>
                            <select class="form-select" name="periodo_fim" id="inputPeriodoFim" required>
                                <option value="">Selecione início primeiro</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="inputTurma" class="form-label fw-semibold">Turma</label>
                            <select class="form-select" name="id_turma" id="inputTurma" required>
                                <option value="">Selecione</option>
                                <?php
                                $turmas->data_seek(0);
                                while ($turma = $turmas->fetch_assoc()): ?>
                                    <option value="<?= $turma['id_turma'] ?>"><?= htmlspecialchars($turma['nome_turma']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="inputProfessor" class="form-label fw-semibold">Professor</label>
                            <input type="text" class="form-control" id="inputProfessorDisplay"
                                value="<?= htmlspecialchars($professor_logado['nome'] ?? '') ?>"
                                readonly>
                            <small class="text-muted">(preenchido automaticamente)</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="inputObs" class="form-label fw-semibold">Observação</label>
                        <textarea class="form-control" name="observacao" id="inputObs" rows="3"
                            placeholder="Observações sobre a reserva..."></textarea>
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
    <div class="modal fade" id="modalMinhasReservas" tabindex="-1" aria-labelledby="modalMinhasReservasLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalMinhasReservasLabel">
                        <i class="bi bi-person-check me-2"></i>Minhas reservas
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body" id="minhas-reservas-body">
                    <div class="text-muted">Clique em "Minhas reservas" para ver todas as suas reservas.</div>
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
        // Definição dos períodos
        const PERIODOS_HORARIOS = {
            '1': ['07:10:00', '08:00:00'],
            '2': ['08:00:00', '08:50:00'],
            '3': ['08:50:00', '09:40:00'],
            '4': ['10:00:00', '10:50:00'],
            '5': ['10:50:00', '11:40:00'],
            '6': ['11:40:00', '12:30:00'],
            '7': ['13:30:00', '14:20:00'],
            '8': ['14:20:00', '15:10:00'],
            '9': ['15:10:00', '16:00:00']
        };

        // Variáveis do modal de reserva
        var modalReserva = document.getElementById('modalReserva');
        var inputIdSala = document.getElementById('inputIdSala');
        var inputTituloSala = document.getElementById('inputTituloSala');
        var inputPeriodoInicio = document.getElementById('inputPeriodoInicio');
        var inputPeriodoFim = document.getElementById('inputPeriodoFim');
        var hiddenHoraInicio = document.getElementById('hiddenHoraInicio');
        var hiddenHoraFim = document.getElementById('hiddenHoraFim');

        // Configuração do modal de reserva
        modalReserva.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var idSala = button.getAttribute('data-id');
            var tituloSala = button.getAttribute('data-titulo');
            inputIdSala.value = idSala;
            inputTituloSala.value = tituloSala;

            // Limpar selects de período
            inputPeriodoInicio.value = '';
            inputPeriodoFim.innerHTML = '<option value="">Selecione início primeiro</option>';
            hiddenHoraInicio.value = '';
            hiddenHoraFim.value = '';

            // Limpar data
            document.getElementById('inputDataReserva').value = '';
        });

        // Atualizar o select de período fim quando selecionar período início
        inputPeriodoInicio.addEventListener('change', function() {
            const inicio = parseInt(this.value);
            const fimSelect = document.getElementById('inputPeriodoFim');

            fimSelect.innerHTML = '<option value="">Selecione</option>';
            hiddenHoraFim.value = '';

            if (inicio >= 1 && inicio <= 9) {
                // Preencher opções do período fim (do início até o 9)
                for (let i = inicio; i <= 9; i++) {
                    const [horaIni, horaFim] = PERIODOS_HORARIOS[i];
                    fimSelect.innerHTML += `<option value="${i}">${i}º período — ${horaIni.slice(0,5)} às ${horaFim.slice(0,5)}</option>`;
                }

                // Setar o horário inicial
                hiddenHoraInicio.value = PERIODOS_HORARIOS[inicio][0];
            }
        });

        // Atualizar horário fim quando selecionar período fim
        inputPeriodoFim.addEventListener('change', function() {
            const fim = parseInt(this.value);

            if (fim >= 1 && fim <= 9) {
                hiddenHoraFim.value = PERIODOS_HORARIOS[fim][1];
            }
        });

        // Lógica do pré-filtro
        const pfData = document.getElementById('pf-data');
        const pfPeriodo = document.getElementById('pf-periodo');
        const pfVerDisp = document.getElementById('pf-ver-disponiveis');
        const pfMinhasReservas = document.getElementById('pf-minhas-reservas');
        const pfLimpar = document.getElementById('pf-limpar');
        const pfResultado = document.getElementById('pf-resultado');
        const gridLabs = document.getElementById('grid-labs');

        function resetFiltro() {
            gridLabs.classList.add('hidden');
            pfResultado.textContent = '';
            pfData.value = '';
            pfPeriodo.value = '';
            document.querySelectorAll('.lab-card').forEach(card => card.classList.remove('hidden'));
        }

        pfLimpar.addEventListener('click', resetFiltro);

        // Ver laboratórios disponíveis
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
                // Enviar requisição para buscar disponíveis
                const formData = new FormData();
                formData.append('action', 'disponiveis');
                formData.append('data', data);
                formData.append('periodo_inicio', periodo);
                formData.append('periodo_fim', periodo);

                console.log('Enviando requisição para buscar disponíveis...');
                console.log('Data:', data, 'Período:', periodo);

                const res = await fetch('../../controller/buscar_laboratorios_disponiveis.php', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                // Primeiro, verifique se a resposta é JSON
                const responseText = await res.text();
                console.log('Resposta do servidor:', responseText.substring(0, 200)); // Mostra os primeiros 200 caracteres

                try {
                    const json = JSON.parse(responseText);

                    if (!json.ok) {
                        throw new Error(json.erro || 'Falha ao consultar');
                    }

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
                } catch (parseError) {
                    console.error('Erro ao parsear JSON:', parseError);
                    gridLabs.classList.add('hidden');
                    pfResultado.textContent = 'Erro: Resposta inválida do servidor. Verifique o console.';
                }
            } catch (e) {
                console.error('Erro na requisição:', e);
                gridLabs.classList.add('hidden');
                pfResultado.textContent = 'Erro ao carregar disponíveis: ' + e.message;
            }
        });

        // Minhas reservas
        pfMinhasReservas.addEventListener('click', async () => {
            const data = pfData.value;
            pfResultado.textContent = 'Carregando suas reservas...';

            const qs = new URLSearchParams({
                ajax: '1',
                action: 'minhas_reservas'
            });

            if (data) qs.append('data', data);

            try {
                const res = await fetch(`laboratorios.php?${qs.toString()}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const json = await res.json();
                const body = document.getElementById('minhas-reservas-body');

                if (!json.ok) {
                    body.innerHTML = `<div class="alert alert-danger">${json.error || 'Falha ao consultar'}</div>`;
                } else if (!json.data || json.data.length === 0) {
                    body.innerHTML = '<div class="alert alert-info">Nenhuma reserva encontrada.</div>';
                } else {
                    let tableHtml = `
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Laboratório</th>
                                        <th>Data</th>
                                        <th>Período</th>
                                        <th>Horário</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    json.data.forEach(reserva => {
                        const periodoText = reserva.periodo_inicio === reserva.periodo_fim ?
                            `${reserva.periodo_inicio}º` :
                            `${reserva.periodo_inicio}º ao ${reserva.periodo_fim}º`;

                        tableHtml += `
                            <tr>
                                <td><strong>${reserva.titulo_sala}</strong></td>
                                <td>${reserva.data_reserva}</td>
                                <td><span class="badge-periodo">${periodoText}</span></td>
                                <td>${reserva.hora_inicio.slice(0,5)} às ${reserva.hora_fim.slice(0,5)}</td>
                            </tr>
                        `;
                    });

                    tableHtml += `
                                </tbody>
                            </table>
                        </div>
                    `;

                    body.innerHTML = tableHtml;
                }

                new bootstrap.Modal(document.getElementById('modalMinhasReservas')).show();
                pfResultado.textContent = '';
            } catch (e) {
                pfResultado.textContent = 'Erro ao carregar suas reservas: ' + e.message;
            }
        });

        // Inicial: garantir grid oculta
        gridLabs.classList.add('hidden');

        // Configurar data mínima como hoje
        const today = new Date().toISOString().split('T')[0];
        pfData.min = today;
        document.getElementById('inputDataReserva').min = today;

        // Função de debug para testar a conexão
        async function testarConexao() {
            console.log('=== Testando conexão com buscar_laboratorios_disponiveis.php ===');

            const formData = new FormData();
            formData.append('action', 'disponiveis');
            formData.append('data', '2024-12-15');
            formData.append('periodo_inicio', '1');
            formData.append('periodo_fim', '1');

            try {
                const res = await fetch('../../src/buscar_laboratorios_disponiveis.php', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const text = await res.text();
                console.log('Status:', res.status);
                console.log('Headers:', res.headers.get('content-type'));
                console.log('Resposta:', text.substring(0, 500));

                try {
                    const json = JSON.parse(text);
                    console.log('JSON parseado:', json);
                } catch (e) {
                    console.log('Não é JSON válido');
                }
            } catch (error) {
                console.error('Erro na requisição:', error);
            }
        }

        // Para testar, você pode chamar esta função no console do navegador:
        // testarConexao();
    </script>
</body>

</html>
<!-- Botão "+" fixo no canto inferior esquerdo -->
<a href="adicionar_lab.php"
    style="position: fixed; left: 32px; bottom: 32px; z-index: 9999; background: #f7c948; color: #fff; border-radius: 50%; width: 60px; height: 60px; display: flex; align-items: flex-start; justify-content: center; font-size: 2.5rem; box-shadow: 0 4px 24px rgba(35,57,93,0.2); text-decoration: none; border: 1px solid #23395d;"
    title="Adicionar Laboratório">
    <span
        style="display: flex; align-items: flex-start; justify-content: center; width: 100%; height: 100%; font-size: 2.2rem; line-height: 1; margin-top: 7px;">+</span>
</a>