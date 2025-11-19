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
if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo_usuario"] !== "Aluno") {
    header("Location: ../Login.html");
    exit();
}
include '../../conexao.php';

// Buscar turmas do aluno
$id_aluno = $_SESSION["id_usuario"];
$sql_turmas = "SELECT id_turma FROM matricula WHERE id_aluno = $id_aluno";
$result_turmas = $conn->query($sql_turmas);
$turmas = [];
if ($result_turmas && $result_turmas->num_rows > 0) {
    while ($row = $result_turmas->fetch_assoc()) {
        $turmas[] = $row['id_turma'];
    }
}

// Filtro de mensagens
$filtro = $_GET['filtro'] ?? 'nao_lidas'; // padrão: não lidas

$turmas_str = implode(',', $turmas) ?: '0'; // Evitar erro se sem turmas

// Montar SQL conforme filtro
if ($filtro === 'lidas') {
    // Mensagens já lidas pelo aluno e ainda válidas (não expiradas)
    $sql_mensagens = "
        SELECT m.id_mensagem, m.assunto, m.mensagem, m.data_envio, u.nome AS remetente
        FROM mensagem m
        INNER JOIN usuario u ON m.id_remetente = u.id_usuario
        INNER JOIN mensagem_leitura ml ON ml.id_mensagem = m.id_mensagem AND ml.id_aluno = $id_aluno
        WHERE (
            (m.enviar_para_todas = 1 AND mensagem_valida(m.id_mensagem))
            OR EXISTS (
                SELECT 1 FROM mensagem_turma mt 
                WHERE mt.id_mensagem = m.id_mensagem 
                AND mensagem_valida(m.id_mensagem)
                AND mt.id_turma IN ($turmas_str)
            )
        )
        ORDER BY m.data_envio DESC
    ";
} elseif ($filtro === 'antigas') {
    // Mensagens antigas (expiradas)
    $sql_mensagens = "
        SELECT m.id_mensagem, m.assunto, m.mensagem, m.data_envio, u.nome AS remetente
        FROM mensagem m
        INNER JOIN usuario u ON m.id_remetente = u.id_usuario
        WHERE (
            (m.enviar_para_todas = 1 AND NOT mensagem_valida(m.id_mensagem))
            OR EXISTS (
                SELECT 1 FROM mensagem_turma mt 
                WHERE mt.id_mensagem = m.id_mensagem 
                AND NOT mensagem_valida(m.id_mensagem)
                AND mt.id_turma IN ($turmas_str)
            )
        )
        ORDER BY m.data_envio DESC
    ";
} else {
    // Mensagens não lidas e válidas (não expiradas)
    $sql_mensagens = "
        SELECT m.id_mensagem, m.assunto, m.mensagem, m.data_envio, u.nome AS remetente
        FROM mensagem m
        INNER JOIN usuario u ON m.id_remetente = u.id_usuario
        LEFT JOIN mensagem_leitura ml ON ml.id_mensagem = m.id_mensagem AND ml.id_aluno = $id_aluno
        WHERE (
            (m.enviar_para_todas = 1 AND mensagem_valida(m.id_mensagem))
            OR EXISTS (
                SELECT 1 FROM mensagem_turma mt 
                WHERE mt.id_mensagem = m.id_mensagem 
                AND mensagem_valida(m.id_mensagem)
                AND mt.id_turma IN ($turmas_str)
            )
        )
        AND ml.id_leitura IS NULL
        ORDER BY m.data_envio DESC
    ";
}
$result_mensagens = $conn->query($sql_mensagens);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Mensagens - Aluno</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
        h3.bg-title {
            background: linear-gradient(135deg, #23395d 0%, #4f6d7a 100%);
            color: #fff !important;
            border-radius: 10px;
            padding: 16px 0;
            margin-bottom: 32px;
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
        .alert-info {
            background: rgba(234, 241, 251, 0.9);
            color: #23395d;
            border: 1px solid rgba(35, 57, 93, 0.2);
            backdrop-filter: blur(5px);
        }
        .form-check-label {
            color: #fff !important;
            font-weight: 500;
        }
        .container {
            position: relative;
            z-index: 1;
        }
        .filtro-btn.active {
            box-shadow: 0 0 0 3px #23395d44;
            border: 2px solid #f7c948 !important;
            font-weight: 700;
        }
        .filtro-btn {
            transition: box-shadow 0.2s, border 0.2s;
        }
        .filtro-btn:not(.active):hover {
            box-shadow: 0 0 0 2px #23395d22;
            border: 2px solid #bfc9d1 !important;
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
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAluno" aria-controls="navbarAluno" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarAluno">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active fw-bold" href="#"><i class="bi bi-chat-dots me-1"></i>Mensagens</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="problema_aluno.php"><i class="bi bi-tools me-1"></i>Enviar Problema</a>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-danger ms-lg-2 mt-2 mt-lg-0" onclick="window.location.href='../../src/logout.php'"><i class="bi bi-box-arrow-right me-1"></i>Sair</button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Filtro de mensagens -->
    <div class="container mb-4">
        <form method="get" class="d-flex justify-content-center align-items-center gap-3 flex-wrap">
            <input type="hidden" name="filtro" id="filtro_hidden" value="<?php echo $filtro; ?>">
            <button type="button" class="btn btn-lg btn-primary px-4 py-2 d-flex align-items-center gap-2 filtro-btn <?php if($filtro==='nao_lidas') echo 'active'; ?>" onclick="setFiltro('nao_lidas')">
                <i class="bi bi-envelope-exclamation"></i> Não lidas
            </button>
            <button type="button" class="btn btn-lg btn-success px-4 py-2 d-flex align-items-center gap-2 filtro-btn <?php if($filtro==='lidas') echo 'active'; ?>" onclick="setFiltro('lidas')">
                <i class="bi bi-envelope-check"></i> Já lidas
            </button>
            <button type="button" class="btn btn-lg btn-secondary px-4 py-2 d-flex align-items-center gap-2 filtro-btn <?php if($filtro==='antigas') echo 'active'; ?>" onclick="setFiltro('antigas')">
                <i class="bi bi-archive"></i> Antigas
            </button>
        </form>
    </div>
    <style>
        .filtro-btn.active {
            box-shadow: 0 0 0 3px #23395d44;
            border: 2px solid #f7c948 !important;
            font-weight: 700;
        }
        .filtro-btn {
            transition: box-shadow 0.2s, border 0.2s;
        }
        .filtro-btn:not(.active):hover {
            box-shadow: 0 0 0 2px #23395d22;
            border: 2px solid #bfc9d1 !important;
        }
    </style>
    <script>
        function setFiltro(valor) {
            document.getElementById('filtro_hidden').value = valor;
            document.querySelector('form').submit();
        }
    </script>
    <!-- Conteúdo Principal -->
    <div class="container py-4">
        <h3 class="text-center mb-4 bg-title">
            <i class="bi bi-chat-dots me-2"></i>
            <?php
                if ($filtro === 'lidas') echo "Mensagens já lidas";
                elseif ($filtro === 'antigas') echo "Mensagens antigas";
                else echo "Suas Mensagens Não Lidas";
            ?>
        </h3>
        <div class="row g-4">
            <?php if ($result_mensagens && $result_mensagens->num_rows > 0): ?>
                <?php while ($msg = $result_mensagens->fetch_assoc()): ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title mb-2 text-primary-emphasis"><?php echo htmlspecialchars($msg['assunto']); ?></h5>
                                <p class="card-text flex-grow-1"><?php echo nl2br(htmlspecialchars($msg['mensagem'])); ?></p>
                                <p class="text-muted small mb-2">
                                    <span><i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars($msg['remetente']); ?></span><br>
                                    <span><i class="bi bi-calendar-event me-1"></i><?php echo date('d/m/Y H:i', strtotime($msg['data_envio'])); ?></span>
                                </p>
                                <?php if($filtro === 'nao_lidas'): ?>
                                <form action="../../src/marcar_mensagem_lida.php" method="POST">
                                    <input type="hidden" name="id_mensagem" value="<?php echo $msg['id_mensagem']; ?>">
                                    <button type="submit" class="btn btn-success w-100 mt-2">
                                        <i class="bi bi-check2-circle"></i> Marcar como lida
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center rounded-3">Nenhuma mensagem encontrada.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$conn->close();
?>