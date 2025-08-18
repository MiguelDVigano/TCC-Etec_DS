<?php
include '../conexao.php';

$id_sala = isset($_GET['id_sala']) ? intval($_GET['id_sala']) : 0;
$data_reserva = isset($_GET['data_reserva']) ? $_GET['data_reserva'] : '';

$timeSlots = [
    "07:10-08:00",
    "08:00-08:50",
    "08:50-09:40",
    "10:00-10:50",
    "10:50-11:40",
    "11:40-12:30",
    "13:30-14:20",
    "14:20-15:10",
    "15:10-16:00",
    "19:00-19:50",
    "19:50-20:40",
    "20:40-21:30",
    "21:30-22:20",
    "22:20-23:10"
];

$reservedStarts = [];
if ($id_sala && $data_reserva) {
    $stmt = $conn->prepare("
        SELECT hora_inicio 
        FROM reserva 
        WHERE id_sala = ? 
        AND data_reserva = ?
    ");
    $stmt->bind_param("is", $id_sala, $data_reserva);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $reservedStarts[] = $row['hora_inicio'];
    }
}

$available = [];
foreach ($timeSlots as $slot) {
    list($inicio, ) = explode('-', $slot);
    if (!in_array($inicio, $reservedStarts)) {
        $available[] = $slot;
    }
}

header('Content-Type: application/json');
echo json_encode($available);
