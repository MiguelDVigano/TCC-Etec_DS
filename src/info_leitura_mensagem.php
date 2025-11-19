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
if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo_usuario"] !== "Professor") {
    http_response_code(403);
    exit("Acesso negado.");
}

if (!isset($_GET['id_mensagem']) || !is_numeric($_GET['id_mensagem'])) {
    http_response_code(400);
    exit("Parâmetro inválido.");
}

$id_mensagem = intval($_GET['id_mensagem']);

include '../conexao.php';

// Descobrir se a mensagem é para todas as turmas
$sql = "SELECT enviar_para_todas FROM mensagem WHERE id_mensagem = $id_mensagem";
$res = $conn->query($sql);
if (!$res || $res->num_rows == 0) {
    echo '<div class="alert alert-danger">Mensagem não encontrada.</div>';
    exit;
}
$row = $res->fetch_assoc();
$enviar_para_todas = $row['enviar_para_todas'];

// Buscar alunos destinatários
if ($enviar_para_todas) {
    // Todas as turmas: buscar todos alunos
    $sqlAlunos = "SELECT u.id_usuario, u.nome
                  FROM usuario u
                  WHERE u.tipo_usuario = 'Aluno'";
} else {
    // Apenas turmas específicas
    $sqlAlunos = "SELECT DISTINCT u.id_usuario, u.nome
                  FROM mensagem_turma mt
                  JOIN matricula m ON mt.id_turma = m.id_turma
                  JOIN usuario u ON m.id_aluno = u.id_usuario
                  WHERE mt.id_mensagem = $id_mensagem";
}
$resultAlunos = $conn->query($sqlAlunos);

$alunos = [];
if ($resultAlunos) {
    while ($a = $resultAlunos->fetch_assoc()) {
        $alunos[$a['id_usuario']] = $a['nome'];
    }
}

// Buscar leituras
$sqlLeitura = "SELECT id_aluno FROM mensagem_leitura WHERE id_mensagem = $id_mensagem";
$resultLeitura = $conn->query($sqlLeitura);

$alunos_leram = [];
if ($resultLeitura) {
    while ($l = $resultLeitura->fetch_assoc()) {
        $alunos_leram[] = $l['id_aluno'];
    }
}

$leram = [];
$nao_leram = [];
foreach ($alunos as $id => $nome) {
    if (in_array($id, $alunos_leram)) {
        $leram[] = $nome;
    } else {
        $nao_leram[] = $nome;
    }
}
?>
<?php if (count($alunos) === 0): ?>
    <div class="alert alert-info text-center">Nenhum aluno destinatário para esta mensagem.</div>
<?php else: ?>
<div class="row">
    <div class="col-md-6">
        <h6><i class="bi bi-eye-fill text-success"></i> Alunos que <b>leram</b> (<?php echo count($leram); ?>):</h6>
        <?php if (count($leram)): ?>
            <ul class="list-group mb-3">
                <?php foreach ($leram as $nome): ?>
                    <li class="list-group-item list-group-item-success"><?php echo htmlspecialchars($nome); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="alert alert-warning">Nenhum aluno leu ainda.</div>
        <?php endif; ?>
    </div>
    <div class="col-md-6">
        <h6><i class="bi bi-eye-slash-fill text-danger"></i> Alunos que <b>não leram</b> (<?php echo count($nao_leram); ?>):</h6>
        <?php if (count($nao_leram)): ?>
            <ul class="list-group mb-3">
                <?php foreach ($nao_leram as $nome): ?>
                    <li class="list-group-item list-group-item-light"><?php echo htmlspecialchars($nome); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="alert alert-success">Todos os alunos já leram.</div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
<?php
$conn->close();
?>
