<?php
session_start();
if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo_usuario"] !== "Manutencao") {
    header("Location: ../Login.html");
    exit();
}


require_once '../../conexao.php';

// Busca todos os chamados com informações da sala e status Aberto
$sql = "SELECT c.id_chamado, c.titulo_chamado, c.descricao, c.url_foto, s.titulo_sala, c.status_chamado 
        FROM chamado c
        INNER JOIN sala s ON c.id_sala = s.id_sala
        WHERE c.status_chamado = 'Aberto' OR c.status_chamado = 'Em Andamento'
        ORDER BY c.data_chamado DESC, c.id_chamado DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <title>Chamados de Manutenção</title>
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

    .card {
      background: #f7f9fa !important;
      border-radius: 12px !important;
      box-shadow: 0 4px 24px #23395d33 !important;
      border: none !important;
      transition: transform 0.2s;
    }

    .card:hover {
      transform: translateY(-4px);
    }

    .card-title {
      color: #23395d !important;
      font-weight: 700;
    }

    .btn-primary,
    .btn-success,
    .btn-warning {
      border-radius: 7px !important;
      font-weight: 600;
    }

    .btn-danger {
      border-radius: 7px !important;
    }

    h3 {
      color: #23395d !important;
      font-weight: 700;
    }

    .img-thumb {
      width: 100%;
      height: 160px;
      object-fit: cover;
      border-radius: 8px;
      border: 1px solid #ccc;
      margin-bottom: 10px;
    }
  </style>
</head>

<body>
  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg navbar-custom shadow-sm mb-4">
    <div class="container d-flex justify-content-between">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <i class="bi bi-mortarboard-fill me-2 fs-3" style="color: #fff;"></i>
        <span class="fw-bold">Sistema Escolar Etec</span>
      </a>
      <button class="btn btn-danger ms-2" onclick="window.location.href='../../src/logout.php'">
        <i class="bi bi-box-arrow-right me-1"></i> Sair
      </button>
    </div>
  </nav>

  <!-- CONTEÚDO -->
  <div class="container py-4">
    <h3 class="text-center mb-5">
      <i class="bi bi-tools me-2" style="color: #f7c948;"></i>
      Chamados de Manutenção
    </h3>
    <div class="row g-4">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php foreach ($result as $row): ?>
          <div class="col-md-4">
            <div class="card p-3">
              <img src="<?php echo $row['url_foto'] ? htmlspecialchars($row['url_foto']) : 'https://via.placeholder.com/300x160'; ?>" alt="Foto do defeito" class="img-thumb">
              <h5 class="card-title"><?php echo htmlspecialchars($row['titulo_chamado']); ?></h5>
              <p><strong>Sala:</strong> <?php echo htmlspecialchars($row['titulo_sala']); ?></p>
              <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modal<?php echo $row['id_chamado']; ?>">
                Visualizar Chamado
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12">
          <div class="alert alert-info text-center rounded-3">Nenhum chamado encontrado.</div>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- MODAIS DOS CHAMADOS -->
  <?php if ($result && $result->num_rows > 0): ?>
    <?php foreach ($result as $row): ?>
      <div class="modal fade" id="modal<?php echo $row['id_chamado']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><i class="bi bi-tools me-2"></i><?php echo htmlspecialchars($row['titulo_chamado']); ?></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <img src="<?php echo $row['url_foto'] ? htmlspecialchars($row['url_foto']) : 'https://via.placeholder.com/400x250'; ?>" class="img-fluid rounded mb-3" alt="Foto defeito">
              <p><strong>Sala:</strong> <?php echo htmlspecialchars($row['titulo_sala']); ?></p>
              <p><strong>Descrição:</strong> <?php echo nl2br(htmlspecialchars($row['descricao'])); ?></p>
              <?php if ($row['status_chamado'] === 'Aberto'): ?>
                <form action="../../src/marcar_chamado_andamento.php" method="POST" class="mt-3">
                  <input type="hidden" name="id_chamado" value="<?php echo $row['id_chamado']; ?>">
                  <button type="submit" class="btn btn-warning w-100">
                    <i class="bi bi-arrow-repeat me-1"></i> Marcar como em andamento
                  </button>
                </form>
              <?php elseif ($row['status_chamado'] === 'Em Andamento'): ?>
                <form action="../../src/marcar_chamado_concluido.php" method="POST" class="mt-3">
                  <input type="hidden" name="id_chamado" value="<?php echo $row['id_chamado']; ?>">
                  <button type="submit" class="btn btn-success w-100">
                    <i class="bi bi-check-circle me-1"></i> Marcar como feito
                  </button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
$conn->close();
?>