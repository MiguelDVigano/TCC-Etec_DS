<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'conexao.php';
    $data = $_POST;
    $stmt = $conn->prepare("INSERT INTO reserva (data_reserva, hora_inicio, hora_fim, id_professor, id_sala, id_turma, observacao) VALUES (?, ?, ?, ?, ?, ?, ?)");
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
    if ($stmt->execute()) {
        header("Location: laboratorios.php?reserva=ok");
        exit;
    } else {
        echo "Erro ao reservar: " . $conn->error;
    }
}
?>
