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
    header("Location: ../../src/logout.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Adicionar Laboratório</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #23395d 0%, #4f6d7a 100%) !important;
            min-height: 100vh;
        }

        .card {
            background: rgba(247, 249, 250, 0.97) !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 24px rgba(35, 57, 93, 0.2) !important;
            border: none !important;
            backdrop-filter: blur(10px);
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
    </style>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card p-4 shadow" style="min-width:350px; max-width:450px; width:100%;">
            <div class="mb-4 text-center">
                <h3 class="fw-bold mb-1" style="color:#23395d;">
                    <i class="bi bi-plus-circle me-2"></i>Adicionar Laboratório
                </h3>
                <p class="text-muted mb-0">Preencha as informações abaixo para cadastrar um novo laboratório.</p>
            </div>
            <form action="../../src/salvar_laboratorio.php" method="POST">
                <div class="mb-3">
                    <label for="nome" class="form-label fw-semibold">Nome do Laboratório</label>
                    <input type="text" class="form-control" id="nome" name="nome" required>
                </div>
                <div class="mb-3">
                    <label for="numero_sala" class="form-label fw-semibold">Número da Sala</label>
                    <input type="number" class="form-control" id="numero_sala" name="numero_sala" min="1" required>
                </div>
                <div class="mb-3">
                    <label for="tipo_sala" class="form-label fw-semibold">Tipo de Sala</label>
                    <select class="form-select" id="tipo_sala" name="tipo_sala" required>
                        <option value="Pratica">Prática</option>
                        <option value="Teorica">Teórica</option>
                        <option value="Mista">Mista</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="capacidade" class="form-label fw-semibold">Limite de Ocupação</label>
                    <input type="number" class="form-control" id="capacidade" name="capacidade" min="1" required>
                </div>
                <div class="mb-3">
                    <input type="hidden" id="status_sala" name="status_sala" value="Ativa">
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Salvar Laboratório
                    </button>
                    <a href="laboratorios.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>