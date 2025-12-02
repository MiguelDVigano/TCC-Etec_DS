<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    include '../controller/conexao.php';
    $data = $_POST;

    // =========================
    // 1. VALIDAR DADOS
    // =========================
    if (
        empty($data['data_reserva']) ||
        empty($data['periodo_inicio']) ||
        empty($data['periodo_fim']) ||
        empty($data['id_professor']) ||
        empty($data['id_sala']) ||
        empty($data['id_turma'])
    ) {
        header("Location: ../view/professor/laboratorios.php?erro_reserva=" . urlencode("Dados incompletos."));
        exit;
    }

    // =========================
    // 2. VALIDAR PERÍODOS (1-9)
    // =========================
    $periodo_inicio = (int)$data['periodo_inicio'];
    $periodo_fim = (int)$data['periodo_fim'];

    if ($periodo_inicio < 1 || $periodo_inicio > 9 || 
        $periodo_fim < 1 || $periodo_fim > 9 || 
        $periodo_inicio > $periodo_fim) {
        header("Location: ../view/professor/laboratorios.php?erro_reserva=" . urlencode("Períodos inválidos."));
        exit;
    }

    // =========================
    // 3. TABELA DE CONVERSÃO PERÍODO -> HORÁRIO
    // =========================
    $PERIODOS_HORARIOS = [
        '1' => ['07:10:00', '08:00:00'],
        '2' => ['08:00:00', '08:50:00'],
        '3' => ['08:50:00', '09:40:00'],
        '4' => ['10:00:00', '10:50:00'],
        '5' => ['10:50:00', '11:40:00'],
        '6' => ['11:40:00', '12:30:00'],
        '7' => ['13:30:00', '14:20:00'],
        '8' => ['14:20:00', '15:10:00'],
        '9' => ['15:10:00', '16:00:00']
    ];

    $hora_inicio = $PERIODOS_HORARIOS[$periodo_inicio][0];
    $hora_fim = $PERIODOS_HORARIOS[$periodo_fim][1];

    // =========================
    // 4. BLOQUEIO DE CONFLITO POR PERÍODO
    // =========================
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM reserva
        WHERE id_sala = ?
          AND data_reserva = ?
          AND NOT (
              periodo_fim < ? OR periodo_inicio > ?
          )
    ");

    $stmt->bind_param(
        "isii",
        $data['id_sala'],
        $data['data_reserva'],
        $periodo_inicio,
        $periodo_fim
    );

    $stmt->execute();
    $result = $stmt->get_result();
    $conflict = $result->fetch_assoc()['total'] > 0;

    if ($conflict) {
        header("Location: ../view/professor/laboratorios.php?erro_reserva=" . urlencode("Erro: Este laboratório já está reservado nesse período."));
        exit;
    }

    // =========================
    // 5. INSERIR RESERVA COM PERÍODOS
    // =========================
    $stmt = $conn->prepare("
        INSERT INTO reserva
        (data_reserva, periodo_inicio, periodo_fim, hora_inicio, hora_fim, id_professor, id_sala, id_turma, observacao)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "siissiiis",
        $data['data_reserva'],
        $periodo_inicio,
        $periodo_fim,
        $hora_inicio,
        $hora_fim,
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
    // 6. NÃO ATUALIZAR STATUS DA SALA PARA "OCUPADO" 
    //    (deixar sempre "Ativa" e controlar por reservas)
    // =========================
    // Remover esta parte que muda o status para "Ocupado"

    // =========================
    // 7. RETORNO
    // =========================
    header("Location: ../view/professor/laboratorios.php?reserva=ok");
    exit;
}