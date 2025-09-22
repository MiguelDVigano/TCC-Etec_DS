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

    .navbar-custom .navbar-toggler {
      border: none;
      padding: 0.25rem 0.5rem;
    }

    .navbar-custom .navbar-toggler:focus {
      box-shadow: none;
    }

    .navbar-custom .navbar-toggler-icon {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }

    .navbar-custom .nav-link.active,
    .navbar-custom .nav-link:focus {
      color: #f7c948 !important;
    }

    .navbar-custom .nav-link.disabled {
      color: #bfc9d1 !important;
    }

    .navbar-nav {
      align-items: center;
    }

    .navbar-collapse {
      flex-grow: 0;
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
      font-size: 1.1rem;
      margin-bottom: 0.75rem;
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
      height: 200px;
      object-fit: cover;
      border-radius: 8px;
      border: 1px solid #ddd;
      margin-bottom: 15px;
    }

    .card-info {
      color: #666;
      font-size: 0.95rem;
      margin-bottom: 15px;
    }

    .card-info strong {
      color: #23395d;
    }

    /* Responsive breakpoints */
    @media (min-width: 1400px) {
      .container {
        max-width: 1320px;
      }
    }

    @media (max-width: 1199.98px) {
      .img-thumb {
        height: 180px;
      }
      
      .navbar-brand span {
        font-size: 1.1rem;
      }
    }

    @media (max-width: 991.98px) {
      .img-thumb {
        height: 170px;
      }
      
      h3 {
        font-size: 1.75rem;
      }
      
      .navbar-brand span {
        font-size: 1rem;
      }
      
      .navbar-nav {
        text-align: center;
        margin-top: 0.5rem;
      }
      
      .btn-danger {
        margin-top: 0.5rem;
        width: 100%;
      }
    }

    @media (max-width: 767.98px) {
      .container {
        padding-left: 15px;
        padding-right: 15px;
      }
      
      .navbar {
        border-radius: 8px;
        margin-bottom: 1rem;
      }
      
      .navbar-brand {
        font-size: 0.95rem;
      }
      
      .navbar-brand .bi {
        font-size: 1.8rem !important;
      }
      
      .btn-danger {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
        width: 100%;
        margin-top: 0.75rem;
      }
      
      h3 {
        font-size: 1.5rem;
      }
      
      .py-4 {
        padding-top: 2rem !important;
        padding-bottom: 2rem !important;
      }
      
      .mb-5 {
        margin-bottom: 2rem !important;
      }
      
      .img-thumb {
        height: 160px;
      }

      .row.g-4 {
        --bs-gutter-x: 1rem;
        --bs-gutter-y: 1rem;
      }
      
      .modal-lg {
        max-width: 90%;
      }
    }
    
    @media (max-width: 575.98px) {
      .navbar {
        border-radius: 6px;
        margin-bottom: 0.75rem;
      }
      
      .navbar-brand {
        font-size: 0.85rem;
      }
      
      .navbar-brand .bi {
        font-size: 1.5rem !important;
      }
      
      .btn-danger {
        font-size: 0.8rem;
        padding: 0.4rem 0.6rem;
        width: 100%;
        margin-top: 0.5rem;
      }
      
      h3 {
        font-size: 1.25rem;
        margin-bottom: 1.5rem !important;
      }
      
      .card {
        margin-bottom: 1rem;
      }
      
      .card-title {
        font-size: 1rem;
      }
      
      .img-thumb {
        height: 180px;
      }
      
      .modal-dialog {
        margin: 0.5rem;
      }
      
      .modal-body {
        padding: 1rem;
      }
      
      .btn {
        padding: 0.75rem 1rem;
        font-size: 1rem;
      }

      .row.g-4 {
        --bs-gutter-x: 0.75rem;
        --bs-gutter-y: 1rem;
      }

      .container {
        padding-left: 12px;
        padding-right: 12px;
      }
      
      .py-4 {
        padding-top: 1.5rem !important;
        padding-bottom: 1.5rem !important;
      }
    }
    
    @media (max-width: 399.98px) {
      .navbar-brand span {
        display: none;
      }
      
      .navbar-brand .bi {
        font-size: 1.25rem !important;
      }
      
      .py-4 {
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
      }
      
      .container {
        padding-left: 8px;
        padding-right: 8px;
      }
      
      h3 {
        font-size: 1.1rem;
      }
      
      .modal-dialog {
        margin: 0.25rem;
      }

      .row.g-4 {
        --bs-gutter-x: 0.5rem;
        --bs-gutter-y: 0.75rem;
      }
      
      .card-title {
        font-size: 0.95rem;
      }
      
      .card-info {
        font-size: 0.85rem;
      }
      
      .btn {
        font-size: 0.9rem;
      }
    }
  </style>
</head>

<body>
  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg navbar-custom shadow-sm mb-4">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <i class="bi bi-mortarboard-fill me-2 fs-3" style="color: #fff;"></i>
        <span class="fw-bold">Sistema Escolar Etec</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <button class="btn btn-danger" onclick="window.location.href='../../src/logout.php'">
              <i class="bi bi-box-arrow-right me-1"></i> Sair
            </button>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- CONTEÚDO -->
  <div class="container py-4">
    <h3 class="text-center mb-5" style="color: #fff !important;">
      <i class="bi bi-tools me-2" style="color: #f7c948;"></i>
      Chamados de Manutenção
    </h3>
    <div class="row g-4">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php foreach ($result as $row): ?>
          <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3">
            <div class="card h-100">
              <div class="card-body p-3 d-flex flex-column">
                <img src="<?php echo $row['url_foto'] ? htmlspecialchars($row['url_foto']) : 'https://via.placeholder.com/300x200'; ?>" alt="Foto do defeito" class="img-thumb">
                <h5 class="card-title"><?php echo htmlspecialchars($row['titulo_chamado']); ?></h5>
                <p class="card-info"><strong>Sala:</strong> <?php echo htmlspecialchars($row['titulo_sala']); ?></p>
                <button class="btn btn-primary w-100 mt-auto" data-bs-toggle="modal" data-bs-target="#modal<?php echo $row['id_chamado']; ?>">
                  <i class="bi bi-eye me-1"></i>Visualizar Chamado
                </button>
              </div>
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
        <div class="modal-dialog modal-dialog-centered modal-lg">
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