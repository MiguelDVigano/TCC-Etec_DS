<?php
session_start();
if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo_usuario"] !== "Professor") {
    header("Location: ../view/Login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_reserva'])) {
    header("Location: ../view/professor/laboratorios.php");
    exit();
}

include '../controller/conexao.php';

$id_reserva = intval($_POST['id_reserva']);
$id_professor = $_SESSION['id_usuario'];

// Verifica se a reserva pertence ao professor logado
$stmt = $conn->prepare("SELECT id_reserva FROM reserva WHERE id_reserva = ? AND id_professor = ?");
$stmt->bind_param("ii", $id_reserva, $id_professor);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    // Reserva não encontrada ou não pertence ao professor
    $_SESSION['msg_cancelar'] = "Reserva não encontrada ou não autorizada.";
    header("Location: ../view/professor/laboratorios.php");
    exit();
}

// Cancela a reserva
$stmtDel = $conn->prepare("DELETE FROM reserva WHERE id_reserva = ?");
$stmtDel->bind_param("i", $id_reserva);
if ($stmtDel->execute()) {
    $_SESSION['msg_cancelar'] = "Reserva cancelada com sucesso.";
} else {
    $_SESSION['msg_cancelar'] = "Erro ao cancelar reserva.";
}

header("Location: ../view/professor/laboratorios.php");
exit();
