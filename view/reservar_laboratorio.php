<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../conexao.php';
    $data = $_POST;

    // Predefined time slots excluding intervals
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

    // Check if the selected time slot is already reserved
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total 
        FROM reserva 
        WHERE id_sala = ? 
          AND data_reserva = ? 
          AND hora_inicio = ?
    ");
    list($hora_inicio, $hora_fim) = explode('-', $data['time_slot']);
    $stmt->bind_param("iss", $data['id_sala'], $data['data_reserva'], $hora_inicio);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['total'] > 0) {
        echo "Erro: Este horário já está reservado.";
    } else {
        // Insert the reservation
        $stmt = $conn->prepare("
            INSERT INTO reserva (data_reserva, hora_inicio, hora_fim, id_professor, id_sala, id_turma, observacao) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "sssiiis",
            $data['data_reserva'],
            $hora_inicio,
            $hora_fim,
            $data['id_professor'],
            $data['id_sala'],
            $data['id_turma'],
            $data['observacao']
        );
        if ($stmt->execute()) {
            // Check if all time slots are reserved
            $stmt = $conn->prepare("
                SELECT COUNT(*) AS reserved 
                FROM reserva 
                WHERE id_sala = ? 
                  AND data_reserva = ?
            ");
            $stmt->bind_param("is", $data['id_sala'], $data['data_reserva']);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row['reserved'] >= count($timeSlots)) {
                // Deactivate the laboratory
                $stmt = $conn->prepare("UPDATE sala SET status_sala = 'Inativa' WHERE id_sala = ?");
                $stmt->bind_param("i", $data['id_sala']);
                $stmt->execute();
            }

            header("Location: laboratorios.php?reserva=ok");
            exit;
        } else {
            echo "Erro ao reservar: " . $conn->error;
        }
    }
}
?>