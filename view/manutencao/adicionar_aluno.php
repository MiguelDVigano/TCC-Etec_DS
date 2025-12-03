<?php
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();
if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo_usuario"] !== "Manutencao") {
    header("Location: ../../src/logout.php");
    exit();
}
include '../../controller/conexao.php';
// Busca turmas para o select
$turmas = $conn->query("SELECT id_turma, nome_turma FROM turma");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Aluno</title>
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
                    <i class="bi bi-person-plus me-2"></i>Adicionar Aluno
                </h3>
                <p class="text-muted mb-0">Preencha as informações abaixo para cadastrar e matricular um novo aluno.</p>
            </div>
            <form action="../../src/matricula.php" method="POST">
                <div class="mb-3">
                    <label for="nome" class="form-label fw-semibold">Nome do Aluno</label>
                    <input type="text" class="form-control" id="nome" name="nome" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">E-mail</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="telefone" class="form-label fw-semibold">Telefone</label>
                    <input type="text" class="form-control" id="telefone" name="telefone" maxlength="11" required>
                </div>
                <div class="mb-3">
                    <label for="senha" class="form-label fw-semibold">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" required>
                </div>
                <div class="mb-3">
                    <label for="id_turma" class="form-label fw-semibold">Turma</label>
                    <select class="form-select" id="id_turma" name="id_turma" required>
                        <option value="">Selecione</option>
                        <?php
                        if ($turmas) {
                            while ($turma = $turmas->fetch_assoc()) {
                                echo '<option value="' . $turma['id_turma'] . '">' . htmlspecialchars($turma['nome_turma']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Salvar Aluno
                    </button>
                    <a href="manutencao.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
