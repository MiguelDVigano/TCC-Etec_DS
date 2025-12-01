<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    include '../controller/conexao.php';
    $data = $_POST;

    // =========================
    // 1. VALIDAR DADOS
    // =========================
    if (
        empty($data['data_reserva']) ||
        empty($data['hora_inicio']) ||
        empty($data['hora_fim']) ||
        empty($data['id_professor']) ||
        empty($data['id_sala']) ||
        empty($data['id_turma'])
    ) {
        header("Location: ../view/professor/laboratorios.php?erro_reserva=" . urlencode("Dados incompletos."));
        exit;
    }

    // =========================
    // 2. TABELA DE HORÁRIOS
    // =========================
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

    // =========================
    // 3. VALIDAR INTERVALO
    // =========================
    $idx_inicio = array_search($data['hora_inicio'] . '-' . substr($data['hora_fim'], 0, 5), $timeSlots);

    $idx_inicio = -1;
    $idx_fim = -1;

    foreach ($timeSlots as $i => $slot) {
        [$inicio, $fim] = explode('-', $slot);
        if ($inicio == $data['hora_inicio']) $idx_inicio = $i;
        if ($fim == $data['hora_fim']) $idx_fim = $i;
    }

    if ($idx_inicio === -1 || $idx_fim === -1 || $idx_inicio > $idx_fim) {
        header("Location: ../view/professor/laboratorios.php?erro_reserva=" . urlencode("Horário inválido."));
        exit;
    }

    // =========================
    // 4. BLOQUEIO ABSOLUTO DE CONFLITO
    // =========================
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM reserva
        WHERE id_sala = ?
          AND data_reserva = ?
          AND NOT (
              hora_fim <= ? OR hora_inicio >= ?
          )
    ");

    $stmt->bind_param(
        "isss",
        $data['id_sala'],
        $data['data_reserva'],
        $data['hora_inicio'],
        $data['hora_fim']
    );

    $stmt->execute();
    $result = $stmt->get_result();
    $conflict = $result->fetch_assoc()['total'] > 0;

    if ($conflict) {
        header("Location: ../view/professor/laboratorios.php?erro_reserva=" . urlencode("Erro: Este laboratório já está reservado nesse horário."));
        exit;
    }

    // =========================
    // 5. INSERIR RESERVA
    // =========================
    $stmt = $conn->prepare("
        INSERT INTO reserva
        (data_reserva, hora_inicio, hora_fim, id_professor, id_sala, id_turma, observacao)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "sssiiis",
        $data['data_reserva'],
        $data['hora_inicio'],
        $data['hora_fim'],
        $data['id_professor'],
        $data['id_sala'],
        $data['id_turma'],
        $data['observacao']
    );

    if (!$stmt->execute()) {
        echo "Erro ao reservar: " . $conn->error;
        exit;
    }

    // =========================
    // 6. ATUALIZAR STATUS DA SALA POR PERÍODO
    // =========================
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM reserva
        WHERE id_sala = ?
          AND data_reserva = ?
    ");
    $stmt->bind_param("is", $data['id_sala'], $data['data_reserva']);
    $stmt->execute();
    $totalReservas = $stmt->get_result()->fetch_assoc()['total'];

    if ($totalReservas > 0) {
        $stmt = $conn->prepare("UPDATE sala SET status_sala = 'Ocupado' WHERE id_sala = ?");
    } else {
        $stmt = $conn->prepare("UPDATE sala SET status_sala = 'Ativa' WHERE id_sala = ?");
    }

    $stmt->bind_param("i", $data['id_sala']);
    $stmt->execute();

    // =========================
    // 7. RETORNO
    // =========================
    header("Location: ../view/professor/laboratorios.php?reserva=ok");
    exit;
}
?>
