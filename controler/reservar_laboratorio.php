<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../conexao.php';
    $data = $_POST;

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

    // Restaurar status para 'Ativa' se a data mudou
    $stmt = $conn->prepare("
        SELECT data_reserva FROM reserva
        WHERE id_sala = ? ORDER BY data_reserva DESC LIMIT 1
    ");
    $stmt->bind_param("i", $data['id_sala']);
    $stmt->execute();
    $result = $stmt->get_result();
    $lastReserva = $result->fetch_assoc();
    if ($lastReserva && $lastReserva['data_reserva'] !== $data['data_reserva']) {
        $stmt2 = $conn->prepare("UPDATE sala SET status_sala = 'Ativa' WHERE id_sala = ?");
        $stmt2->bind_param("i", $data['id_sala']);
        $stmt2->execute();
    }

    // Encontrar índices dos horários selecionados
    $idx_inicio = -1;
    $idx_fim = -1;
    foreach ($timeSlots as $i => $slot) {
        list($inicio, $fim) = explode('-', $slot);
        if ($inicio === $data['hora_inicio']) $idx_inicio = $i;
        if ($fim === $data['hora_fim']) $idx_fim = $i;
    }

    if ($idx_inicio === -1 || $idx_fim === -1 || $idx_inicio > $idx_fim) {
        header("Location: ../view/laboratorios.php?erro_reserva=" . urlencode("Horário inválido selecionado."));
        exit;
    }

    // Verificar conflitos em todos os horários do intervalo
    $conflict = false;
    for ($i = $idx_inicio; $i <= $idx_fim; $i++) {
        list($slot_inicio, ) = explode('-', $timeSlots[$i]);
        $stmt = $conn->prepare("
            SELECT COUNT(*) AS total 
            FROM reserva 
            WHERE id_sala = ? 
              AND data_reserva = ? 
              AND hora_inicio = ?
        ");
        $stmt->bind_param("iss", $data['id_sala'], $data['data_reserva'], $slot_inicio);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($row['total'] > 0) {
            $conflict = true;
            break;
        }
    }

    if ($conflict) {
        header("Location: ../view/laboratorios.php?erro_reserva=" . urlencode("Erro: Um ou mais horários já estão reservados."));
        exit;
    } else {
        // Inserir a reserva com o início do primeiro e fim do último horário
        list($hora_inicio_real, ) = explode('-', $timeSlots[$idx_inicio]);
        list(, $hora_fim_real) = explode('-', $timeSlots[$idx_fim]);
        $stmt = $conn->prepare("
            INSERT INTO reserva (data_reserva, hora_inicio, hora_fim, id_professor, id_sala, id_turma, observacao) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "sssiiis",
            $data['data_reserva'],
            $hora_inicio_real,
            $hora_fim_real,
            $data['id_professor'],
            $data['id_sala'],
            $data['id_turma'],
            $data['observacao']
        );
        if ($stmt->execute()) {
            // Checar se todos os horários estão reservados para o dia
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
                $stmt = $conn->prepare("UPDATE sala SET status_sala = 'Ocupado' WHERE id_sala = ?");
                $stmt->bind_param("i", $data['id_sala']);
                $stmt->execute();
            } else {
                $stmt = $conn->prepare("UPDATE sala SET status_sala = 'Ativa' WHERE id_sala = ?");
                $stmt->bind_param("i", $data['id_sala']);
                $stmt->execute();
            }

            header("Location: ../view/laboratorios.php?reserva=ok");
            exit;
        } else {
            echo "Erro ao reservar: " . $conn->error;
        }
    }
}
