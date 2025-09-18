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
  <title>Reportar Defeito - Laboratório</title>
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
    .navbar-custom .navbar-brand, .navbar-custom .nav-link, .navbar-custom .navbar-toggler {
      color: #fff !important;
    }
    .navbar-custom .nav-link.active, .navbar-custom .nav-link:focus {
      color: #f7c948 !important;
    }
    .navbar-custom .nav-link.disabled {
      color: #bfc9d1 !important;
    }
    .card, .modal-content {
      background: #f7f9fa !important;
      border-radius: 12px !important;
      box-shadow: 0 4px 24px #23395d33 !important;
      border: none !important;
    }
    .card-title, .modal-title, h3 {
      color: #23395d !important;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      font-weight: 700;
    }
    .btn-primary, .btn-success {
      border-radius: 7px !important;
      font-weight: 600;
    }
    .btn-danger {
      border-radius: 7px !important;
    }
    .form-label {
      color: #23395d !important;
      font-weight: 500;
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-custom shadow-sm mb-4">
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
            <a class="nav-link active fw-bold" href=""><i class="bi bi-tools me-1"></i>Enviar Problema</a>
          </li>
          <li class="nav-item">
            <button class="btn btn-danger ms-2" onclick="window.location.href='../../src/logout.php'"><i class="bi bi-box-arrow-right me-1"></i>Sair</button>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-lg border-0 rounded-4">
          <div class="card-body p-4">
            <h3 class="text-center mb-4"><i class="bi bi-tools me-2"></i>Reportar Defeito</h3>
            <form action="" method="POST" enctype="multipart/form-data">
              <div class="mb-3">
                <label for="laboratorio" class="form-label">Selecione o Laboratório</label>
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
                <label for="imagemDefeito" class="form-label">Foto do Defeito</label>
                <input class="form-control" type="file" id="imagemDefeito" name="imagemDefeito" accept="image/png, image/jpeg, image/jpg">
              </div>
              <div class="mb-3">
                <label for="descricaoProblema" class="form-label">Descrição do Problema</label>
                <textarea class="form-control" id="descricaoProblema" name="descricao" rows="4" placeholder="Descreva o defeito encontrado..." required></textarea>
              </div>
              <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-send me-1"></i>Enviar
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Pop-up de sucesso -->
  <div id="popup" class="modal fade<?php if ($showPopup) echo ' show d-block'; ?>" tabindex="-1" style="background:rgba(0,0,0,0.3);" <?php if ($showPopup) echo 'aria-modal="true" role="dialog"'; ?>>
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center">
        <div class="modal-body">
          <span class="display-3 text-success">&#10003;</span>
          <div class="fw-bold fs-5 mb-2">Problema enviado com sucesso!</div>
          <div class="text-secondary mb-2">Em breve sua solicitação será analisada e resolvida.</div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    <?php if ($showPopup): ?>
      window.onload = function() {
        setTimeout(function() {
          var popup = document.getElementById("popup");
          if (popup) popup.classList.remove("show", "d-block");
        }, 3000);
      }
    <?php endif; ?>
  </script>
</body>

</html>
<?php
$conn->close();
?>