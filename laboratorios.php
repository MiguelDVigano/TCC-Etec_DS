<?php
include 'conexao.php';

// Buscar laboratórios
$sql = "SELECT * FROM sala";
$result = $conn->query($sql);

// Buscar turmas e professores para o formulário
$turmas = $conn->query("SELECT id_turma, nome_turma FROM turma");
$professores = $conn->query("SELECT id_prof, nome_prof FROM professor");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Laboratórios</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Laboratórios Cadastrados</h2>
    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while($lab = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($lab['titulo_sala']) ?></h5>
                            <p class="card-text">
                                <strong>Número:</strong> <?= $lab['numeo_sala'] ?><br>
                                <strong>Capacidade:</strong> <?= $lab['capacidade'] ?><br>
                                <strong>Status:</strong> <?= $lab['status_sala'] ? 'Ativo' : 'Inativo' ?>
                            </p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalReserva" 
                                data-id="<?= $lab['id_sala'] ?>" 
                                data-titulo="<?= htmlspecialchars($lab['titulo_sala']) ?>">
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
            <label for="inputHoraInicio" class="form-label">Hora Início</label>
            <input type="time" class="form-control" name="hora_inicio" required>
        </div>
        <div class="mb-2">
            <label for="inputHoraFim" class="form-label">Hora Fim</label>
            <input type="time" class="form-control" name="hora_fim" required>
        </div>
        <div class="mb-2">
            <label for="inputTurma" class="form-label">Turma</label>
            <select class="form-select" name="id_turma" required>
                <option value="">Selecione</option>
                <?php while($turma = $turmas->fetch_assoc()): ?>
                    <option value="<?= $turma['id_turma'] ?>"><?= htmlspecialchars($turma['nome_turma']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-2">
            <label for="inputProfessor" class="form-label">Professor</label>
            <select class="form-select" name="id_professor" required>
                <option value="">Selecione</option>
                <?php while($prof = $professores->fetch_assoc()): ?>
                    <option value="<?= $prof['id_prof'] ?>"><?= htmlspecialchars($prof['nome_prof']) ?></option>
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
