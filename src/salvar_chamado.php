<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../view/Login.html");
    exit();
}

require_once '../conexao.php';

// Apenas aceitar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../view/professor/problema.php");
    exit();
}

// Validações básicas
$id_sala = isset($_POST['id_sala']) ? intval($_POST['id_sala']) : 0;
$descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';

if ($id_sala <= 0 || $descricao === '') {
    header("Location: ../view/professor/problema.php?error=missing");
    exit();
}

// Verifica se a sala existe e pega o título para compor um título do chamado
$stmt = $conn->prepare("SELECT titulo_sala FROM sala WHERE id_sala = ?");
$stmt->bind_param("i", $id_sala);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    header("Location: ../view/professor/problema.php?error=invalid_sala");
    exit();
}
$sala = $res->fetch_assoc();
$titulo_chamado = "Defeito - " . substr($sala['titulo_sala'], 0, 80);
$stmt->close();

// Processa upload de imagem (opcional)
$uploadPath = '../uploads/chamados/';
if (!is_dir($uploadPath)) {
    mkdir($uploadPath, 0755, true);
}

$url_foto = null;
if (isset($_FILES['imagemDefeito']) && $_FILES['imagemDefeito']['error'] !== UPLOAD_ERR_NO_FILE) {
    $file = $_FILES['imagemDefeito'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        header("Location: ../view/professor/problema.php?error=upload");
        exit();
    }

    // Validação MIME tipo com finfo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowed = ['image/jpeg' => '.jpg', 'image/png' => '.png', 'image/gif' => '.gif', 'image/webp' => '.webp'];
    if (!array_key_exists($mime, $allowed)) {
        header("Location: ../view/professor/problema.php?error=type");
        exit();
    }

    $ext = $allowed[$mime];
    $filename = uniqid('ch_') . $ext;
    $target = $uploadPath . $filename;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        header("Location: ../view/professor/problema.php?error=move");
        exit();
    }

    // Salvar caminho relativo que possa ser usado na view
    $url_foto = '../../uploads/chamados/' . $filename;
}

// Inserir chamado usando prepared statement
$today = date('Y-m-d');
$stmt = $conn->prepare("INSERT INTO chamado (titulo_chamado, descricao, url_foto, data_chamado, id_sala) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssssi", $titulo_chamado, $descricao, $url_foto, $today, $id_sala);
$ok = $stmt->execute();
$stmt->close();

if ($ok) {
    header("Location: ../view/professor/problema.php?success=1");
    exit();
} else {
    header("Location: ../view/professor/problema.php?error=db");
    exit();
}
?>