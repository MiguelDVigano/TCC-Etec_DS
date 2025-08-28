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
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Laboratórios</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>
    <?php if (isset($_GET['erro_reserva'])): ?>
        <div id="alertReservaErroTopo" class="alert alert-danger text-center position-fixed w-100"
            style="top:0;left:0;z-index:1055;">
            <?= htmlspecialchars($_GET['erro_reserva']) ?>
        </div>
    <?php endif; ?>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand">Navbar</a>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link active" aria-current="page" href="#">Notificações</a>
                    <a class="nav-link disabled" aria-disabled="true">Laboratórios</a>
                </div>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>Laboratórios Cadastrados</h2>
        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($lab = $result->fetch_assoc()): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($lab['titulo_sala']) ?></h5>
                                <p class="card-text">
                                    <strong>Número:</strong> <?= $lab['numero_sala'] ?><br>
                                    <strong>Capacidade:</strong> <?= $lab['capacidade'] ?><br>
                                    <strong>Status:</strong>
                                    <?php
                                    if ($lab['status_sala'] === 'Ativa') {
                                        echo 'Ativo';
                                    } else if ($lab['status_sala'] === 'Ocupado') {
                                        echo 'Ocupado';
                                    } else {
                                        echo 'Inativo';
                                    }
                                    ?>
                                </p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalReserva"
                                    data-id="<?= $lab['id_sala'] ?>" data-titulo="<?= htmlspecialchars($lab['titulo_sala']) ?>"
                                    data-status="<?= $lab['status_sala'] ?>">
                                    Reservar
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-center text-muted">Nenhum laboratório encontrado.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Reserva -->
    <div class="modal fade" id="modalReserva" tabindex="-1" aria-labelledby="modalReservaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <!-- Corrigido o action do formulário -->
            <form class="modal-content" method="POST" action="..controler/reservar_laboratorio.php" id="formReserva">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalReservaLabel">Reservar Laboratório</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_sala" id="inputIdSala">
                    <div class="mb-2">
                        <label for="inputTituloSala" class="form-label">Laboratório</label>
                        <input type="text" class="form-control" id="inputTituloSala" readonly>
                    </div>
                    <div class="mb-2">
                        <label for="inputData" class="form-label">Data</label>
                        <input type="date" class="form-control" name="data_reserva" id="inputDataReserva" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Horário de Início</label>
                        <select class="form-select" name="hora_inicio" id="inputHoraInicio" required>
                            <option value="">Selecione a data primeiro</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Horário de Fim</label>
                        <select class="form-select" name="hora_fim" id="inputHoraFim" required>
                            <option value="">Selecione o início primeiro</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label for="inputTurma" class="form-label">Turma</label>
                        <select class="form-select" name="id_turma" required>
                            <option value="">Selecione</option>
                            <?php
                            // Reset pointer for turmas
                            $turmas->data_seek(0);
                            while ($turma = $turmas->fetch_assoc()): ?>
                                <option value="<?= $turma['id_turma'] ?>"><?= htmlspecialchars($turma['nome_turma']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label for="inputProfessor" class="form-label">Professor</label>
                        <select class="form-select" name="id_professor" required>
                            <option value="">Selecione</option>
                            <?php
                            // Reset pointer for professores
                            $professores->data_seek(0);
                            while ($prof = $professores->fetch_assoc()): ?>
                                <option value="<?= $prof['id_professor'] ?>"><?= htmlspecialchars($prof['nome']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label for="inputObs" class="form-label">Observação</label>
                        <textarea class="form-control" name="observacao"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Reservar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
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
            fetch('..controler/reservar_laboratorio_horarios.php?id_sala=' + idSala + '&data_reserva=' + dataReserva)
                .then(response => response.json())
                .then(data => {
                    inputHoraInicio.innerHTML = '';
                    inputHoraFim.innerHTML = '<option value="">Selecione o início primeiro</option>';
                    if (data.length === 0) {
                        inputHoraInicio.innerHTML = '<option value="">Todos os horários reservados</option>';
                    } else {
                        inputHoraInicio.innerHTML = '<option value="">Selecione</option>';
                        data.forEach(function (slot) {
                            // slot é "07:10-08:00", mas precisamos enviar só o início
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
            fetch('..controler/reservar_laboratorio_horarios.php?id_sala=' + idSala + '&data_reserva=' + dataReserva)
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

        // Remove alerta do topo após 4 segundos
        document.addEventListener('DOMContentLoaded', function () {
            var alertTopo = document.getElementById('alertReservaErroTopo');
            if (alertTopo) {
                setTimeout(function () {
                    alertTopo.remove();
                }, 4000);
            }
        });
    </script>
</body>

</html>