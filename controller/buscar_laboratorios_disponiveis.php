<?php
// Adicione esta linha no início para garantir que só retorne JSON
header('Content-Type: application/json; charset=utf-8');

include 'conexao.php';

// Verifique se há erros de conexão
if (!isset($conn)) {
    echo json_encode(['ok' => false, 'erro' => 'Erro de conexão com o banco de dados']);
    exit;
}

$action = $_POST['action'] ?? null;
$data   = $_POST['data'] ?? null;
$periodo_inicio = $_POST['periodo_inicio'] ?? null;
$periodo_fim    = $_POST['periodo_fim'] ?? null;

/* ================================
   DEFINIÇÃO DOS PERÍODOS
   1-9 conforme grade oficial
================================ */
$PERIODOS = [
    '1' => '07:10-08:00',
    '2' => '08:00-08:50',
    '3' => '08:50-09:40',
    '4' => '10:00-10:50',
    '5' => '10:50-11:40',
    '6' => '11:40-12:30',
    '7' => '13:30-14:20',
    '8' => '14:20-15:10',
    '9' => '15:10-16:00'
];

if ($action !== 'disponiveis') {
    echo json_encode(['ok' => false, 'erro' => 'Ação inválida']);
    exit;
}

if (
    empty($data) ||
    !isset($PERIODOS[$periodo_inicio]) ||
    !isset($PERIODOS[$periodo_fim]) ||
    $periodo_inicio > $periodo_fim
) {
    echo json_encode(['ok' => false, 'erro' => 'Parâmetros inválidos. Data: ' . ($data ?? 'vazio') . ', Início: ' . ($periodo_inicio ?? 'vazio') . ', Fim: ' . ($periodo_fim ?? 'vazio')]);
    exit;
}

/* ================================
   QUERY QUE BLOQUEIA CONFLITOS
   Verifica se existe reserva que sobrepõe os períodos
================================ */
$sql = "
    SELECT s.id_sala, s.titulo_sala
    FROM sala s
    WHERE s.status_sala = 'Ativa'
    AND NOT EXISTS (
        SELECT 1
        FROM reserva r
        WHERE r.id_sala = s.id_sala
          AND r.data_reserva = ?
          AND NOT (
                r.periodo_fim < ?  -- Reserva termina antes do período desejado
             OR r.periodo_inicio > ? -- Reserva começa depois do período desejado
          )
    )
    ORDER BY s.titulo_sala
";

try {
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['ok' => false, 'erro' => 'Erro ao preparar consulta: ' . $conn->error]);
        exit;
    }
    
    $stmt->bind_param("sii", $data, $periodo_inicio, $periodo_fim);
    $stmt->execute();
    $result = $stmt->get_result();

    $labs = [];
    while ($row = $result->fetch_assoc()) {
        $labs[] = $row;
    }

    echo json_encode([
        'ok' => true,
        'data' => $labs,
        'total' => count($labs)
    ]);
    
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'erro' => 'Erro no banco de dados: ' . $e->getMessage()]);
}
exit;
?>