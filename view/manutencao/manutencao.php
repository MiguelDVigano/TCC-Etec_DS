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

require_once '../../controller/conexao.php';

// Verifica se a coluna data_conclusao existe na tabela chamado
$hasDataConclusao = false;
$colCheck = $conn->query("SHOW COLUMNS FROM chamado LIKE 'data_conclusao'");
if ($colCheck && $colCheck->num_rows > 0) {
    $hasDataConclusao = true;
}

// Recebe filtro via GET: todos | aberto | andamento | concluido
$status = $_GET['status'] ?? 'todos';

// Monta cláusula WHERE conforme filtro, adaptando se houver data_conclusao
if ($status === 'aberto') {
    $where = "c.status_chamado = 'Aberto'";
} elseif ($status === 'andamento') {
    $where = "c.status_chamado = 'Em Andamento'";
} elseif ($status === 'concluido') {
    if ($hasDataConclusao) {
        // Exibir apenas concluídos dentro de 3 meses após a conclusão
        $where = "c.status_chamado = 'Concluido' AND c.data_conclusao IS NOT NULL AND DATE_ADD(c.data_conclusao, INTERVAL 3 MONTH) > NOW()";
    } else {
        // Sem coluna de data_conclusao, exibe todos concluidos
        $where = "c.status_chamado = 'Concluido'";
    }
} else {
    if ($hasDataConclusao) {
        $where = "(
            c.status_chamado = 'Aberto' OR
            c.status_chamado = 'Em Andamento' OR
            (c.status_chamado = 'Concluido' AND (c.data_conclusao IS NULL OR DATE_ADD(c.data_conclusao, INTERVAL 3 MONTH) > NOW()))
        )";
    } else {
        $where = "(c.status_chamado = 'Aberto' OR c.status_chamado = 'Em Andamento' OR c.status_chamado = 'Concluido')";
    }
}

// Monta lista de colunas a selecionar (inclui data_conclusao somente se existir)
$selectCols = "c.id_chamado, c.titulo_chamado, c.descricao, c.url_foto, s.titulo_sala, c.status_chamado, c.data_chamado";
if ($hasDataConclusao) {
    $selectCols .= ", c.data_conclusao";
}

// Busca chamados conforme filtro, juntando sala
$sql = "SELECT " . $selectCols . "
        FROM chamado c
        INNER JOIN sala s ON c.id_sala = s.id_sala
        WHERE " . $where . "
        ORDER BY c.data_chamado DESC, c.id_chamado DESC";

$result = $conn->query($sql);
$chamados = [];
if ($result) {
    $chamados = $result->fetch_all(MYSQLI_ASSOC);
}
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
      backdrop-filter: blur(6px);
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
    .btn-primary {
      background: linear-gradient(135deg, #23395d 0%, #4f6d7a 100%) !important;
      border: none !important;
      border-radius: 7px !important;
      font-weight: 600;
    }
    .badge-status { font-weight:700; }
    .img-thumb {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 8px;
    }
    /* filtro centralizado com borda */
    .filter-box {
      background: #f7f9fa;
      border: 1.5px solid #bfc9d1;
      border-radius: 999px;
      padding: 6px 12px;
      display: inline-flex;
      justify-content: center;
      align-items: center;
      box-shadow: 0 2px 12px rgba(35,57,93,0.08);
    }
    .filter-wrapper {
      display:flex;
      justify-content:center;
      margin-bottom:18px;
    }
    .nav-pills .nav-link {
      border-radius: 999px;
      padding: .35rem .9rem;
      margin: 0 .2rem;
      color: #23395d;
      font-weight:600;
      font-size:0.9rem;
    }
    .nav-pills .nav-link i { margin-right:4px; }
    .nav-pills .nav-link.active {
      background: linear-gradient(135deg, #23395d 0%, #4f6d7a 100%);
      color: #fff;
    }
  </style>
</head>

<body class="min-vh-100">
  <!-- NAVBAR padronizada -->
  <nav class="navbar navbar-expand-lg bg-body-tertiary shadow-sm mb-4" style="background: linear-gradient(135deg, #23395d 0%, #4f6d7a 100%); border-radius:12px;">
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
            <button class="btn btn-danger ms-3" onclick="window.location.href='../../src/logout.php'"><i class="bi bi-box-arrow-right me-1"></i>Sair</button>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- CONTEÚDO -->
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="mb-0 text-white"><i class="bi bi-tools me-2" style="color: #f7c948;"></i>Chamados de Manutenção</h3>
      <div>
        <span class="badge bg-warning text-dark badge-status">Total: <?php echo count($chamados); ?></span>
      </div>
    </div>

    <!-- filtro centralizado com borda -->
    <div class="filter-wrapper">
      <div class="filter-box">
        <ul class="nav nav-pills">
          <li class="nav-item"><a class="nav-link <?php echo ($status === 'aberto') ? 'active' : ''; ?>" href="manutencao.php?status=aberto"><i class="bi bi-exclamation-circle"></i>Não iniciados</a></li>
          <li class="nav-item"><a class="nav-link <?php echo ($status === 'andamento') ? 'active' : ''; ?>" href="manutencao.php?status=andamento"><i class="bi bi-hourglass-split"></i>Em Andamento</a></li>
          <li class="nav-item"><a class="nav-link <?php echo ($status === 'concluido') ? 'active' : ''; ?>" href="manutencao.php?status=concluido"><i class="bi bi-check-circle"></i>Concluídos</a></li>
        </ul>
      </div>
    </div>

    <div class="row g-4">
      <?php if (!empty($chamados)): ?>
        <?php foreach ($chamados as $row): ?>
          <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm">
              <div class="card-body d-flex flex-column">
                <img src="<?php echo $row['url_foto'] ? htmlspecialchars($row['url_foto']) : 'https://via.placeholder.com/300x200'; ?>" 
                     alt="Foto do defeito" class="img-thumb mb-3">
                <h5 class="card-title"><?php echo htmlspecialchars($row['titulo_chamado']); ?></h5>
                <p class="text-muted mb-2">
                  <i class="bi bi-door-closed me-1"></i>
                  <strong>Sala:</strong> <?php echo htmlspecialchars($row['titulo_sala']); ?>
                </p>
                <p class="card-text flex-grow-1"><?php echo nl2br(htmlspecialchars($row['descricao'])); ?></p>
                <div class="mt-3 d-flex gap-2 align-items-center">
                  <span class="badge <?php echo ($row['status_chamado'] === 'Em Andamento') ? 'bg-info text-dark' : (($row['status_chamado'] === 'Concluido') ? 'bg-success' : 'bg-secondary'); ?> badge-status"><?php echo htmlspecialchars($row['status_chamado']); ?></span>
                  <?php if(!empty($row['data_conclusao'])): ?>
                    <small class="text-muted ms-2">Concluído: <?php echo date('d/m/Y', strtotime($row['data_conclusao'])); ?></small>
                  <?php endif; ?>
                  <a href="#" data-bs-toggle="modal" data-bs-target="#modal<?php echo $row['id_chamado']; ?>" class="btn btn-primary ms-auto">Ver detalhes</a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12">
          <div class="alert alert-info text-center">
            <i class="bi bi-info-circle me-2"></i>Nenhum chamado encontrado para o filtro selecionado.
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- MODAIS DOS CHAMADOS -->
  <?php if (!empty($chamados)): ?>
    <?php foreach ($chamados as $row): ?>
      <div class="modal fade" id="modal<?php echo $row['id_chamado']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><i class="bi bi-tools me-2"></i><?php echo htmlspecialchars($row['titulo_chamado']); ?></h5>
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
                  <button type="submit" class="btn btn-warning btn-lg"><i class="bi bi-arrow-repeat me-1"></i>Marcar como em andamento</button>
                </form>
              <?php elseif ($row['status_chamado'] === 'Em Andamento'): ?>
                <form action="../../src/marcar_chamado_concluido.php" method="POST" class="d-grid">
                  <input type="hidden" name="id_chamado" value="<?php echo $row['id_chamado']; ?>">
                  <button type="submit" class="btn btn-success btn-lg"><i class="bi bi-check-circle me-1"></i>Marcar como concluído</button>
                </form>
              <?php elseif ($row['status_chamado'] === 'Concluido'): ?>
                <div class="alert alert-secondary">Concluído em <?php echo (!empty($row['data_conclusao']) ? date('d/m/Y', strtotime($row['data_conclusao'])) : '—'); ?></div>
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