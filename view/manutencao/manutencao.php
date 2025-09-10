<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Chamados de Manutenção</title>
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
      margin-bottom: 32px;
    }
    .navbar .navbar-brand {
      color: #fff !important;
      font-weight: 600;
    }
    .btn-danger {
      background: #c0392b !important;
      border: none !important;
    }
    .btn-danger:hover {
      background: #a93226 !important;
    }
    h3 {
      color: #f7c948 !important;
      font-weight: 700;
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
    .img-thumb {
      width: 100%;
      height: 160px;
      object-fit: cover;
      border-radius: 8px;
      border: 1px solid #ccc;
      margin-bottom: 10px;
    }
    /* Botão Visualizar Chamado - padrão Bootstrap */
    .card .btn-primary {
      background: #23395d !important;
      color: #fff !important;
      border-radius: 7px;
      box-shadow: none !important;
      transition: background 0.2s;
    }
    .card .btn-primary:hover {
      background: #4f6d7a !important;
    }
  </style>
</head>
<body>
  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg shadow-sm mb-4">
    <div class="container d-flex justify-content-between">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <i class="bi bi-mortarboard-fill me-2 fs-3" style="color: #fff;"></i>
        <span class="fw-bold">Sistema Escolar Etec</span>
      </a>
      <button class="btn btn-danger" onclick="window.location.href='../Login.html'">
        <i class="bi bi-box-arrow-right me-1"></i> Sair
      </button>
    </div>
  </nav>

  <!-- CONTEÚDO -->
  <div class="container py-4">
    <h3 class="text-center mb-5">
      <i class="bi bi-tools me-2" style="color: #fff;"></i>
      Chamados de Manutenção
    </h3>
    <div class="row g-4">
      <!-- CARD 1 -->
      <div class="col-md-4">
        <div class="card p-3">
          <img src="" alt="teste.jfif" class="img-thumb">
          <h5 class="card-title">Computador não liga</h5>
          <p><strong>Sala:</strong> Laboratório 1</p>
          <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modal1">
            Visualizar Chamado
          </button>
        </div>
      </div>

      <!-- CARD 2 -->
      <div class="col-md-4">
        <div class="card p-3">
          <img src="https://via.placeholder.com/300x160" alt="Foto do defeito" class="img-thumb">
          <h5 class="card-title">Projetor com imagem fraca</h5>
          <p><strong>Sala:</strong> Laboratório 2</p>
          <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modal2">
            Visualizar Chamado
          </button>
        </div>
      </div>

      <!-- CARD 3 -->
      <div class="col-md-4">
        <div class="card p-3">
          <img src="https://via.placeholder.com/300x160" alt="Foto do defeito" class="img-thumb">
          <h5 class="card-title">Ar-condicionado não funciona</h5>
          <p><strong>Sala:</strong> Laboratório 3</p>
          <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modal3">
            Visualizar Chamado
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL 1 -->
  <div class="modal fade" id="modal1" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-tools me-2"></i>Computador não liga</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <img src="https://via.placeholder.com/400x250" class="img-fluid rounded mb-3" alt="Foto defeito">
          <p><strong>Sala:</strong> Laboratório 1</p>
          <p><strong>Descrição:</strong> O computador principal não liga mesmo conectado na energia.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL 2 -->
  <div class="modal fade" id="modal2" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-tools me-2"></i>Projetor com imagem fraca</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <img src="https://via.placeholder.com/400x250" class="img-fluid rounded mb-3" alt="Foto defeito">
          <p><strong>Sala:</strong> Laboratório 2</p>
          <p><strong>Descrição:</strong> O projetor apresenta imagem fraca e embaçada, mesmo após troca de cabos.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL 3 -->
  <div class="modal fade" id="modal3" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-tools me-2"></i>Ar-condicionado não funciona</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <img src="https://via.placeholder.com/400x250" class="img-fluid rounded mb-3" alt="Foto defeito">
          <p><strong>Sala:</strong> Laboratório 3</p>
          <p><strong>Descrição:</strong> O ar-condicionado não liga, mesmo após verificar o disjuntor.</p>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
