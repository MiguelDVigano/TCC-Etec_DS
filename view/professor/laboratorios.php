<?php
session_start();

if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo_usuario"] !== "Professor") {
    header("Location: ../Login.html");
    exit();
}

include '../../conexao.php';

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
        <div class="row g-4 justify-content-center">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($lab = $result->fetch_assoc()): ?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
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
    </script>
</body>

</html>
<!-- Botão "+" fixo no canto inferior esquerdo -->
<a href="adicionar_lab.php" 
   style="position: fixed; left: 32px; bottom: 32px; z-index: 9999; background: #f7c948; color: #fff; border-radius: 50%; width: 60px; height: 60px; display: flex; align-items: flex-start; justify-content: center; font-size: 2.5rem; box-shadow: 0 4px 24px rgba(35,57,93,0.2); text-decoration: none; border: 1px solid #23395d;"
   title="Adicionar Laboratório">
    <span style="display: flex; align-items: flex-start; justify-content: center; width: 100%; height: 100%; font-size: 2.2rem; line-height: 1; margin-top: 7px;">+</span>
</a>