<?php
session_start();
if (!isset($_SESSION["id_usuario"])) {
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
    // Mensagens já lidas pelo aluno
    $sql_mensagens = "
        SELECT m.id_mensagem, m.assunto, m.mensagem, m.data_envio, u.nome AS remetente
        FROM mensagem m
        INNER JOIN usuario u ON m.id_remetente = u.id_usuario
        INNER JOIN mensagem_leitura ml ON ml.id_mensagem = m.id_mensagem AND ml.id_aluno = $id_aluno
        WHERE (
            m.enviar_para_todas = 1 and mensagem_valida(m.id_mensagem)
            OR EXISTS (
                SELECT 1 FROM mensagem_turma mt 
                WHERE mt.id_mensagem = m.id_mensagem and mensagem_valida(m.id_mensagem)
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
            m.enviar_para_todas = 1 and NOT mensagem_valida(m.id_mensagem)
            OR EXISTS (
                SELECT 1 FROM mensagem_turma mt 
                WHERE mt.id_mensagem = m.id_mensagem and NOT mensagem_valida(m.id_mensagem)
                AND mt.id_turma IN ($turmas_str)
            )
        )
        ORDER BY m.data_envio DESC
    ";
} else {
    // Mensagens não lidas e válidas
    $sql_mensagens = "
        SELECT m.id_mensagem, m.assunto, m.mensagem, m.data_envio, u.nome AS remetente
        FROM mensagem m
        INNER JOIN usuario u ON m.id_remetente = u.id_usuario
        LEFT JOIN mensagem_leitura ml ON ml.id_mensagem = m.id_mensagem AND ml.id_aluno = $id_aluno
        WHERE (
            (m.enviar_para_todas = 1 and mensagem_valida(m.id_mensagem))
            OR EXISTS (
                SELECT 1 FROM mensagem_turma mt 
                WHERE mt.id_mensagem = m.id_mensagem and mensagem_valida(m.id_mensagem)
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
        h3 {
            color: #f7c948 !important;
            font-weight: 700;
        }
        .filtro-mensagens {
            display: flex;
            justify-content: center;
            gap: 32px;
            margin-bottom: 32px;
        }
        .filtro-mensagens label {
            font-weight: 600;
            color: #fdfdfd;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary shadow-sm mb-4">
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
                        <a class="nav-link" href="problema.php"><i class="bi bi-tools me-1"></i>Enviar Problema</a>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-danger" onclick="window.location.href='../Login.html'" style="margin-left:12px;"><i class="bi bi-box-arrow-right me-1"></i>Sair</button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Filtro de mensagens -->
    <div class="container">
        <form method="get" class="filtro-mensagens mb-4">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="filtro" id="filtro_nao_lidas" value="nao_lidas" <?php if($filtro==='nao_lidas') echo 'checked'; ?>>
                <label class="form-check-label" for="filtro_nao_lidas">Não lidas</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="filtro" id="filtro_lidas" value="lidas" <?php if($filtro==='lidas') echo 'checked'; ?>>
                <label class="form-check-label" for="filtro_lidas">Já lidas</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="filtro" id="filtro_antigas" value="antigas" <?php if($filtro==='antigas') echo 'checked'; ?>>
                <label class="form-check-label" for="filtro_antigas">Antigas</label>
            </div>
            <button type="submit" class="btn btn-primary ms-3">Filtrar</button>
        </form>
    </div>

    <!-- Conteúdo Principal -->
    <div class="container py-5">
        <h3 class="text-center mb-5"><i class="bi bi-chat-dots me-2"></i>
            <?php
                if ($filtro === 'lidas') echo "Mensagens já lidas";
                elseif ($filtro === 'antigas') echo "Mensagens antigas";
                else echo "Suas Mensagens Não Lidas";
            ?>
        </h3>
        <div class="row g-4">
            <?php if ($result_mensagens && $result_mensagens->num_rows > 0): ?>
                <?php while ($msg = $result_mensagens->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card p-3 h-100">
                            <h5 class="card-title"><?php echo htmlspecialchars($msg['assunto']); ?></h5>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($msg['mensagem'])); ?></p>
                            <p class="text-muted small">
                                Enviado por: <?php echo htmlspecialchars($msg['remetente']); ?><br>
                                Data: <?php echo date('d/m/Y H:i', strtotime($msg['data_envio'])); ?>
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
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">Nenhuma mensagem encontrada.</div>
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