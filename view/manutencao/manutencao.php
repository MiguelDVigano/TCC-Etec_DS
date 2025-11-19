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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Chamados de Manutenção</title>
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
    .btn-warning {
      background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%) !important;
      border: none !important;
      border-radius: 7px !important;
      font-weight: 600;
    }
    .btn-warning:hover {
      background: linear-gradient(135deg, #e67e22 0%, #d35400 100%) !important;
    }
    .alert-info {
      background: rgba(234, 241, 251, 0.9);
      color: #23395d;
      border: 1px solid rgba(35, 57, 93, 0.2);
      backdrop-filter: blur(5px);
    }
    .img-thumb {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 8px;
    }
  </style>
</head>

<body class="min-vh-100">
  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-gradient shadow-sm mb-4" style="background: linear-gradient(135deg, #23395d 0%, #4f6d7a 100%)">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <i class="bi bi-mortarboard-fill me-2 fs-3" style="color: #f7c948;"></i>
        <span class="fw-bold">Sistema Escolar Etec</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <button class="btn btn-danger ms-lg-2 mt-2 mt-lg-0" onclick="window.location.href='../../src/logout.php'">
              <i class="bi bi-box-arrow-right me-1"></i>Sair
            </button>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- CONTEÚDO -->
  <div class="container py-4">
    <h3 class="text-center mb-4 text-white">
      <i class="bi bi-tools me-2" style="color: #f7c948;"></i>
      Chamados de Manutenção
    </h3>
    <div class="row g-4">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php foreach ($result as $row): ?>
          <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm">
              <div class="card-body d-flex flex-column">
                <img src="<?php echo $row['url_foto'] ? htmlspecialchars($row['url_foto']) : 'https://via.placeholder.com/300x200'; ?>" 
                     alt="Foto do defeito" class="img-thumb mb-3">
                <h5 class="card-title"><?php echo htmlspecialchars($row['titulo_chamado']); ?></h5>
                <p class="text-muted mb-3">
                  <i class="bi bi-door-closed me-1"></i>
                  <strong>Sala:</strong> <?php echo htmlspecialchars($row['titulo_sala']); ?>
                </p>
                <div class="mt-auto">
                  <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modal<?php echo $row['id_chamado']; ?>">
                    <i class="bi bi-eye me-1"></i>Visualizar Chamado
                  </button>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12">
          <div class="alert alert-info text-center">
            <i class="bi bi-info-circle me-2"></i>Nenhum chamado encontrado.
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- MODAIS DOS CHAMADOS -->
  <?php if ($result && $result->num_rows > 0): ?>
    <?php foreach ($result as $row): ?>
      <div class="modal fade" id="modal<?php echo $row['id_chamado']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">
                <i class="bi bi-tools me-2"></i><?php echo htmlspecialchars($row['titulo_chamado']); ?>
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="text-center mb-3">
                <img src="<?php echo $row['url_foto'] ? htmlspecialchars($row['url_foto']) : 'https://via.placeholder.com/400x250'; ?>" 
                     class="img-fluid rounded" alt="Foto defeito" style="max-height: 300px;">
              </div>
              <div class="mb-3">
                <h6><i class="bi bi-door-closed me-2"></i>Sala:</h6>
                <p><?php echo htmlspecialchars($row['titulo_sala']); ?></p>
              </div>
              <div class="mb-3">
                <h6><i class="bi bi-file-text me-2"></i>Descrição:</h6>
                <p><?php echo nl2br(htmlspecialchars($row['descricao'])); ?></p>
              </div>
              <?php if ($row['status_chamado'] === 'Aberto'): ?>
                <form action="../../src/marcar_chamado_andamento.php" method="POST" class="d-grid">
                  <input type="hidden" name="id_chamado" value="<?php echo $row['id_chamado']; ?>">
                  <button type="submit" class="btn btn-warning btn-lg">
                    <i class="bi bi-arrow-repeat me-1"></i>Marcar como em andamento
                  </button>
                </form>
              <?php elseif ($row['status_chamado'] === 'Em Andamento'): ?>
                <form action="../../src/marcar_chamado_concluido.php" method="POST" class="d-grid">
                  <input type="hidden" name="id_chamado" value="<?php echo $row['id_chamado']; ?>">
                  <button type="submit" class="btn btn-success btn-lg">
                    <i class="bi bi-check-circle me-1"></i>Marcar como concluído
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