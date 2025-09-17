<?php
require_once '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_chamado'])) {
    $id_chamado = intval($_POST['id_chamado']);
    $stmt = $conn->prepare("UPDATE chamado SET status_chamado = 'Em Andamento' WHERE id_chamado = ?");
    $stmt->bind_param("i", $id_chamado);
    $stmt->execute();
    $stmt->close();
}
header("Location: ../view/manutencao/manutencao.php");
exit();
?>