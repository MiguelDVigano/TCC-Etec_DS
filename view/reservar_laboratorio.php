<?php
include '../conexao.php';

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
    <title>Reserva de Laboratórios</title>
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
        .card, .modal-content {
            background: #f7f9fa !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 24px #23395d33 !important;
            border: none !important;
        }
        .card-title, .modal-title, h2 {
            color: #23395d !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .form-label, .modal-title {
            color: #23395d !important;
            font-weight: 500;
        }
        .form-control, .form-select, textarea {
            background-color: #f7f9fa !important;
            border: 1.5px solid #bfc9d1 !important;
            border-radius: 7px !important;
            font-size: 15px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .form-control:focus, .form-select:focus, textarea:focus {
            outline: none;
            border-color: #23395d !important;
            box-shadow: 0 0 0 2px #23395d22 !important;
            background-color: #fff !important;
        }
        .btn-primary, .btn-success {
            background: linear-gradient(135deg, #23395d 0%, #4f6d7a 100%) !important;
            color: #fff !important;
            border: none !important;
            border-radius: 7px !important;
            font-size: 16px;
            font-weight: 600;
            box-shadow: 0 2px 8px #23395d22;
            transition: background 0.2s, box-shadow 0.2s;
        }
        .btn-primary:hover, .btn-success:hover {
            background: linear-gradient(135deg, #1a2940 0%, #23395d 100%) !important;
            box-shadow: 0 4px 16px #23395d33;
        }
        .btn-secondary {
            background: #bfc9d1 !important;
            color: #23395d !important;
            border: none !important;
            border-radius: 7px !important;
            font-size: 16px;
            font-weight: 600;
            box-shadow: 0 2px 8px #23395d22;
            transition: background 0.2s, box-shadow 0.2s;
        }
        .btn-secondary:hover {
            background: #4f6d7a !important;
            color: #fff !important;
        }
        .alert {
            border-radius: 7px !important;
            font-size: 15px;
            box-shadow: 0 2px 8px #23395d22;
        }
    </style>
</head>
<body>
    <!-- Navbar Bootstrap -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="bi bi-mortarboard-fill me-2 fs-3 text-warning"></i>
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
                        <a class="nav-link" href="problema.html"><i class="bi bi-tools me-1"></i>Enviar Problema</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="row justify-content-center">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($lab = $result->fetch_assoc()): ?>
                    <div class="col-md-5 col-lg-4 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title text-dark fw-bold">
                                    <i class="bi bi-door-closed me-2"></i><?= htmlspecialchars($lab['titulo_sala']) ?>
                                </h5>
                                <ul class="list-group list-group-flush mb-3">
                                    <li class="list-group-item"><strong>Número:</strong> <?= $lab['numero_sala'] ?></li>
                                    <li class="list-group-item"><strong>Capacidade:</strong> <?= $lab['capacidade'] ?> alunos</li>
                                    <li class="list-group-item">
                                        <strong>Status:</strong>
                                        <?php
                                        if ($lab['status_sala'] === 'Ativa') {
                                            echo '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Ativo</span>';
                                        } else if ($lab['status_sala'] === 'Ocupado') {
                                            echo '<span class="badge bg-danger"><i class="bi bi-exclamation-circle me-1"></i>Ocupado</span>';
                                        } else {
                                            echo '<span class="badge bg-secondary"><i class="bi bi-x-circle me-1"></i>Inativo</span>';
                                        }
                                        ?>
                                    </li>
                                </ul>
                                <button class="btn btn-primary w-100 fw-bold" data-bs-toggle="modal" data-bs-target="#modalReserva"
                                    data-id="<?= $lab['id_sala'] ?>" data-titulo="<?= htmlspecialchars($lab['titulo_sala']) ?>"
                                    data-status="<?= $lab['status_sala'] ?>">
                                    <i class="bi bi-calendar-plus me-1"></i>Reservar
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning text-center">
                        <i class="bi bi-info-circle me-2"></i>Nenhum laboratório encontrado.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Modal Reserva -->
    <div class="modal fade" id="modalReserva" tabindex="-1" aria-labelledby="modalReservaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content border-0 shadow" method="POST" action="../src/reservar_laboratorio.php" id="formReserva">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalReservaLabel"><i class="bi bi-calendar-plus me-2"></i>Reservar Laboratório</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_sala" id="inputIdSala">
                    <div class="mb-3">
                        <label for="inputTituloSala" class="form-label fw-bold">Laboratório</label>
                        <input type="text" class="form-control" id="inputTituloSala" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="inputData" class="form-label fw-bold">Data</label>
                        <input type="date" class="form-control" name="data_reserva" id="inputDataReserva" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Horário de Início</label>
                        <select class="form-select" name="hora_inicio" id="inputHoraInicio" required>
                            <option value="">Selecione a data primeiro</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Horário de Fim</label>
                        <select class="form-select" name="hora_fim" id="inputHoraFim" required>
                            <option value="">Selecione o início primeiro</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="inputTurma" class="form-label fw-bold">Turma</label>
                        <select class="form-select" name="id_turma" required>
                            <option value="">Selecione</option>
                            <?php
                            $turmas->data_seek(0);
                            while ($turma = $turmas->fetch_assoc()): ?>
                                <option value="<?= $turma['id_turma'] ?>"><?= htmlspecialchars($turma['nome_turma']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="inputProfessor" class="form-label fw-bold">Professor</label>
                        <select class="form-select" name="id_professor" required>
                            <option value="">Selecione</option>
                            <?php
                            $professores->data_seek(0);
                            while ($prof = $professores->fetch_assoc()): ?>
                                <option value="<?= $prof['id_professor'] ?>"><?= htmlspecialchars($prof['nome']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="inputObs" class="form-label fw-bold">Observação</label>
                        <textarea class="form-control" name="observacao" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success fw-bold"><i class="bi bi-check-lg me-1"></i>Reservar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg me-1"></i>Cancelar</button>
                </div>
            </form>
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

        modalReserva.addEventListener('show.bs.modal', function (event) {
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

        inputDataReserva.addEventListener('change', function () {
            var idSala = inputIdSala.value;
            var dataReserva = inputDataReserva.value;
            if (!idSala || !dataReserva) {
                inputHoraInicio.innerHTML = '<option value="">Selecione a data primeiro</option>';
                inputHoraFim.innerHTML = '<option value="">Selecione o início primeiro</option>';
                return;
            }
            fetch('../src/reservar_laboratorio_horarios.php?id_sala=' + idSala + '&data_reserva=' + dataReserva)
                .then(response => response.json())
                .then(data => {
                    inputHoraInicio.innerHTML = '';
                    inputHoraFim.innerHTML = '<option value="">Selecione o início primeiro</option>';
                    if (data.length === 0) {
                        inputHoraInicio.innerHTML = '<option value="">Todos os horários reservados</option>';
                    } else {
                        inputHoraInicio.innerHTML = '<option value="">Selecione</option>';
                        data.forEach(function (slot) {
                            var inicio = slot.split('-')[0];
                            inputHoraInicio.innerHTML += '<option value="' + inicio + '">' + slot + '</option>';
                        });
                    }
                });
        });

        inputHoraInicio.addEventListener('change', function () {
            var idSala = inputIdSala.value;
            var dataReserva = inputDataReserva.value;
            var horaInicio = inputHoraInicio.value;
            if (!idSala || !dataReserva || !horaInicio) {
                inputHoraFim.innerHTML = '<option value="">Selecione o início primeiro</option>';
                return;
            }
            fetch('../src/reservar_laboratorio_horarios.php?id_sala=' + idSala + '&data_reserva=' + dataReserva)
                .then(response => response.json())
                .then(data => {
                    inputHoraFim.innerHTML = '';
                    var found = false;
                    data.forEach(function (slot, idx) {
                        var inicio = slot.split('-')[0];
                        var fim = slot.split('-')[1];
                        if (inicio === horaInicio) found = true;
                        if (found) {
                            inputHoraFim.innerHTML += '<option value="' + fim + '">' + slot + '</option>';
                        }
                    });
                });
        });
    </script>
</body>
</html>
