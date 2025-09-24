<?php
session_start();
if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo_usuario"] !== "Professor") {
    header("Location: ../Login.html");
    exit();
}

require_once '../../conexao.php';

$sql = "SELECT id_sala, titulo_sala FROM sala order by titulo_sala";
$result = $conn->query($sql);

// Lógica para exibir o pop-up após envio do formulário
$showPopup = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $showPopup = true;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reportar Defeito - Laboratório</title>
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
    .btn-primary {
      background: linear-gradient(135deg, #23395d 0%, #4f6d7a 100%) !important;
      border: none !important;
      border-radius: 7px !important;
      font-weight: 600;
    }
    .btn-primary:hover {
      background: linear-gradient(135deg, #1c2d47 0%, #425965 100%) !important;
    }
    .popup {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background-color: #fff;
      padding: 2rem;
      border-radius: 0.5rem;
      box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
      z-index: 9999;
      text-align: center;
      min-width: 300px;
    }
    .popup.show {
      opacity: 1;
      visibility: visible;
    }
    .popup .icon {
      font-size: 3rem;
      color: #28a745;
      margin-bottom: 1rem;
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
            <a class="nav-link" href="laboratorios.php"><i class="bi bi-pc-display-horizontal me-1"></i>Laboratórios</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="mensagem.php"><i class="bi bi-chat-dots me-1"></i>Mensagens</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active fw-bold" href="#"><i class="bi bi-tools me-1"></i>Enviar Problema</a>
          </li>
          <li class="nav-item">
            <button class="btn btn-danger ms-lg-2 mt-2 mt-lg-0" onclick="window.location.href='../../src/logout.php'"><i class="bi bi-box-arrow-right me-1"></i>Sair</button>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-12 col-md-8 col-lg-6">
        <div class="card shadow">
          <div class="card-body">
            <h3 class="card-title text-center mb-4">
              <i class="bi bi-tools me-2"></i>Reportar Defeito
            </h3>
            <form action="" method="POST" enctype="multipart/form-data">
              <div class="mb-3">
                <label for="laboratorio" class="form-label fw-semibold">Selecione o Laboratório</label>
                <select class="form-select" id="laboratorio" name="id_sala" required>
                  <option value="" selected disabled>Escolha um laboratório</option>
                  <?php
                  if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                      echo '<option value="' . $row['id_sala'] . '">' . htmlspecialchars($row['titulo_sala']) . '</option>';
                    }
                  }
                  ?>
                </select>
              </div>
              
              <div class="mb-3">
                <label for="imagemDefeito" class="form-label fw-semibold">Foto do Defeito</label>
                <input class="form-control" type="file" id="imagemDefeito" name="imagemDefeito" accept="image/png, image/jpeg, image/jpg">
              </div>
              
              <div class="mb-3">
                <label for="descricaoProblema" class="form-label fw-semibold">Descrição do Problema</label>
                <textarea class="form-control" id="descricaoProblema" name="descricao" rows="4" placeholder="Descreva o defeito encontrado..." required></textarea>
              </div>
              
              <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                  <i class="bi bi-send me-2"></i>Enviar Problema
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Pop-up de sucesso -->
  <div id="popup" class="popup<?php if ($showPopup) echo ' show'; ?>">
    <div class="icon">✓</div>
    <h5 class="fw-bold">Problema enviado com sucesso!</h5>
    <p class="text-muted mb-0">Em breve sua solicitação será analisada e resolvida.</p>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    <?php if ($showPopup): ?>
      window.onload = function() {
        var popup = document.getElementById("popup");
        popup.classList.add("show");
        setTimeout(function() {
          popup.classList.remove("show");
        }, 3000);
      }
    <?php endif; ?>
  </script>
</body>

</html>
<?php
$conn->close();
?>