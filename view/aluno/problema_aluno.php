<?php
session_start();
if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo_usuario"] !== "Aluno") {
    header("Location: ../Login.html");
    exit();
}

require_once '../../conexao.php';

$sql = "SELECT id_sala, titulo_sala FROM sala order by titulo_sala";
$result = $conn->query($sql);

// Lógica para exibir o pop-up após envio do formulário
$showPopup = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Aqui você pode processar o upload e salvar no banco, se desejar.
    // Por enquanto, apenas mostra o pop-up.
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

    .card,
    .modal-content {
      background: #f7f9fa !important;
      border-radius: 12px !important;
      box-shadow: 0 4px 24px #23395d33 !important;
      border: none !important;
    }

    .card-title,
    .modal-title,
    h3 {
      color: #23395d !important;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      font-weight: 700;
    }

    .btn-primary,
    .btn-success {
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

    .form-control,
    .form-select,
    textarea {
      background-color: #f7f9fa !important;
      border: 1.5px solid #bfc9d1 !important;
      border-radius: 7px !important;
      font-size: 15px;
      transition: border-color 0.3s, box-shadow 0.3s;
    }

    .form-control:focus,
    .form-select:focus,
    textarea:focus {
      outline: none;
      border-color: #23395d !important;
      box-shadow: 0 0 0 2px #23395d22 !important;
      background-color: #fff !important;
    }

    .btn-primary {
      background: linear-gradient(135deg, #23395d 0%, #4f6d7a 100%) !important;
      color: #fff !important;
      border: none !important;
      border-radius: 7px !important;
      font-size: 16px;
      font-weight: 600;
      box-shadow: 0 2px 8px #23395d22;
      transition: background 0.2s, box-shadow 0.2s;
    }

    .btn-primary:hover {
      background: linear-gradient(135deg, #1a2940 0%, #23395d 100%) !important;
      box-shadow: 0 4px 16px #23395d33;
    }

    .btn-primary:active {
      background: #23395d !important;
    }

    .btn-danger {
      background: #c0392b !important;
      color: #fff !important;
      border: none !important;
      border-radius: 7px !important;
      font-size: 16px;
      font-weight: 600;
      box-shadow: 0 2px 8px #23395d22;
      transition: background 0.2s, color 0.2s;
    }

    .btn-danger:hover {
      background: #a93226 !important;
      color: #fff !important;
    }

    .btn-danger:focus, .btn-danger.focus {
      background: #922b20 !important;
      border-color: #922b20 !important;
      box-shadow: 0 0 0 0.2rem rgba(169, 50, 38, 0.5) !important;
    }

    .popup {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background-color: #fff;
      color: #333;
      padding: 32px 40px;
      border-radius: 12px;
      border: 2px solid #222;
      box-shadow: 0 4px 24px rgba(44, 62, 80, 0.18);
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.5s ease, visibility 0.5s ease;
      z-index: 9999;
      font-family: Arial, sans-serif;
      font-size: 1.2rem;
      text-align: center;
      min-width: 320px;
    }

    .popup.show {
      opacity: 1;
      visibility: visible;
    }

    .popup .icon {
      font-size: 2.5rem;
      color: #4CAF50;
      margin-bottom: 12px;
      display: block;
    }

    .popup .title {
      font-weight: bold;
      font-size: 1.3rem;
      margin-bottom: 8px;
    }

    .popup .subtitle {
      color: #555;
      font-size: 1rem;
    }

    /* Responsive improvements */
    @media (max-width: 991.98px) {
      .navbar-nav {
        text-align: center;
        margin-top: 0.5rem;
      }
      
      .btn-danger {
        margin-top: 0.5rem;
        width: 100%;
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
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
      
      .py-5 {
        padding-top: 2rem !important;
        padding-bottom: 2rem !important;
      }
      
      .col-md-6 {
        max-width: 100%;
        flex: 0 0 100%;
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
      }
      
      h3 {
        font-size: 1.25rem;
      }
      
      .card-body {
        padding: 1.5rem !important;
      }
      
      .btn {
        padding: 0.75rem 1rem;
        font-size: 1rem;
      }
      
      .container {
        padding-left: 12px;
        padding-right: 12px;
      }
      
      .py-5 {
        padding-top: 1.5rem !important;
        padding-bottom: 1.5rem !important;
      }
      
      .form-control, .form-select, textarea {
        font-size: 16px; /* Prevents zoom on iOS */
      }
    }
    
    @media (max-width: 399.98px) {
      .navbar-brand span {
        display: none;
      }
      
      .navbar-brand .bi {
        font-size: 1.25rem !important;
      }
      
      .py-5 {
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
      
      .card-body {
        padding: 1rem !important;
      }
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
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAluno" aria-controls="navbarAluno" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarAluno">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" href="mensagem_aluno.php"><i class="bi bi-chat-dots me-1"></i>Mensagens</a>
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
      <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
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
  <div id="popup" class="popup<?php if ($showPopup) echo ' show'; ?>">
    <span class="icon">&#10003;</span>
    <div class="title">Problema enviado com sucesso!</div>
    <div class="subtitle">Em breve sua solicitação será analisada e resolvida.</div>
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