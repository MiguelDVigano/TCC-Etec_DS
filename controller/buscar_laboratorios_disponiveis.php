<?php
include '../controller/conexao.php';

$action = $_POST['action'] ?? null;
$data   = $_POST['data'] ?? null;
$periodo_inicio = $_POST['periodo_inicio'] ?? null;
$periodo_fim    = $_POST['periodo_fim'] ?? null;

/* ================================
   GRADE OFICIAL DE HORÁRIOS
   (TEM QUE SER IGUAL AO SISTEMA)
================================ */
$PERIODOS_HORARIOS = [
    '1'  => ['07:10:00','08:00:00'],
    '2'  => ['08:00:00','08:50:00'],
    '3'  => ['08:50:00','09:40:00'],
    '4'  => ['10:00:00','10:50:00'],
    '5'  => ['10:50:00','11:40:00'],
    '6'  => ['11:40:00','12:30:00'],
    '7'  => ['13:30:00','14:20:00'],
    '8'  => ['14:20:00','15:10:00'],
    '9'  => ['15:10:00','16:00:00'],
    '10' => ['19:00:00','19:50:00'],
    '11' => ['19:50:00','20:40:00'],
    '12' => ['20:40:00','21:30:00'],
    '13' => ['21:30:00','22:20:00'],
    '14' => ['22:20:00','23:10:00']
];

if ($action !== 'disponiveis') {
    echo json_encode(['ok' => false, 'erro' => 'Ação inválida']);
    exit;
}

if (
    empty($data) ||
    !isset($PERIODOS_HORARIOS[$periodo_inicio]) ||
    !isset($PERIODOS_HORARIOS[$periodo_fim])
) {
    echo json_encode(['ok' => false, 'erro' => 'Parâmetros inválidos']);
    exit;
}

$horaInicio = $PERIODOS_HORARIOS[$periodo_inicio][0];
$horaFim    = $PERIODOS_HORARIOS[$periodo_fim][1];

/* ================================
   QUERY QUE BLOQUEIA CONFLITOS
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
                r.hora_fim <= ?
             OR r.hora_inicio >= ?
          )
    )
    ORDER BY s.titulo_sala
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $data, $horaInicio, $horaFim);
$stmt->execute();
$result = $stmt->get_result();

$labs = [];
while ($row = $result->fetch_assoc()) {
    $labs[] = $row;
}

echo json_encode([
    'ok' => true,
    'data' => $labs
]);
exit;
