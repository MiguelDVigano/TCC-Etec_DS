<?php
session_start();
if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo_usuario"] !== "Aluno") {
    header("Location: ../Login.html");
    exit();
}

require_once '../../conexao.php';

$sql = "SELECT id_sala, titulo_sala FROM sala order by titulo_sala";
$result = $conn->query($sql);
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
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #23395d 0%, #4f6d7a 100%);
      min-height: 100vh;
    }

    .card {
      background: #f7f9fa !important;
      border-radius: 12px !important;
      box-shadow: 0 4px 24px #23395d33 !important;
      border: none !important;
    }

    .card-title,
    h3 {
      color: #23395d !important;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      font-weight: 700;
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

    .navbar {
      background: linear-gradient(135deg, #23395d 0%, #4f6d7a 100%) !important;
      box-shadow: 0 2px 12px #23395d22 !important;
      border-radius: 12px !important;
      margin-bottom: 32px;
    }

    .navbar .navbar-brand,
    .navbar .nav-link,
    .navbar .navbar-toggler {
      color: #fff !important;
    }

    .navbar .nav-link.active,
    .navbar .nav-link:focus {
      color: #f7c948 !important;
    }

    .navbar .nav-link.disabled {
      color: #bfc9d1 !important;
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
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg bg-body-tertiary shadow-sm mb-4">
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
          </li>
          <li class="nav-item">
            <a class="nav-link" href="mensagem_aluno.php"><i class="bi bi-chat-dots me-1"></i>Mensagens</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active fw-bold" href="problema.html"><i class="bi bi-tools me-1"></i>Enviar Problema</a>
          </li>
          <li class="nav-item">
            <button class="btn btn-danger" onclick="window.location.href='../../src/logout.php'" style="margin-left:12px;"><i class="bi bi-box-arrow-right me-1"></i>Sair</button>
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
            <form action="../../src/salvar_chamado.php" method="POST" enctype="multipart/form-data">
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
              <button type="submit" class="btn btn-primary w-100 rounded-3">
                <i class="bi bi-send me-1"></i>Enviar
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
$conn->close();
?>