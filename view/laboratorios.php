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
                                    <strong>Status:</strong> <?= $lab['status_sala'] === 'Ativa' ? 'Ativo' : 'Inativo' ?>
                                </p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalReserva"
                                    data-id="<?= $lab['id_sala'] ?>" data-titulo="<?= htmlspecialchars($lab['titulo_sala']) ?>">
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
            <form class="modal-content" method="POST" action="reservar_laboratorio.php">
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
                        <input type="date" class="form-control" name="data_reserva" required>
                    </div>
                    <div class="mb-2">
                        <label for="inputTimeSlot" class="form-label">Horário</label>
                        <select class="form-select" name="time_slot" required>
                            <option value="">Selecione</option>
                            <?php
                            $timeSlots = [
                                "07:10-08:00",
                                "08:00-08:50",
                                "08:50-09:40",
                                "10:00-10:50",
                                "10:50-11:40",
                                "11:40-12:30",
                                "13:30-14:20",
                                "14:20-15:10",
                                "15:10-16:00",
                                "19:00-19:50",
                                "19:50-20:40",
                                "20:40-21:30",
                                "21:30-22:20",
                                "22:20-23:10"
                            ];
                            $reservedSlots = [];
                            $stmt = $conn->prepare("
                                SELECT hora_inicio, hora_fim 
                                FROM reserva 
                                WHERE id_sala = ? 
                                AND data_reserva = ?
                            ");
                            $stmt->bind_param("is", $lab['id_sala'], date('Y-m-d'));
                            $stmt->execute();
                            $result = $stmt->get_result();
                            while ($row = $result->fetch_assoc()) {
                                $reservedSlots[] = $row['hora_inicio'] . '-' . $row['hora_fim'];
                            }
                            foreach ($timeSlots as $slot) {
                                if (!in_array($slot, $reservedSlots)) {
                                    echo "<option value=\"$slot\">$slot</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label for="inputTurma" class="form-label">Turma</label>
                        <select class="form-select" name="id_turma" required>
                            <option value="">Selecione</option>
                            <?php while ($turma = $turmas->fetch_assoc()): ?>
                                <option value="<?= $turma['id_turma'] ?>"><?= htmlspecialchars($turma['nome_turma']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label for="inputProfessor" class="form-label">Professor</label>
                        <select class="form-select" name="id_professor" required>
                            <option value="">Selecione</option>
                            <?php while ($prof = $professores->fetch_assoc()): ?>
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
        modalReserva.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var idSala = button.getAttribute('data-id');
            var tituloSala = button.getAttribute('data-titulo');
            document.getElementById('inputIdSala').value = idSala;
            document.getElementById('inputTituloSala').value = tituloSala;
        });
    </script>
</body>

</html>