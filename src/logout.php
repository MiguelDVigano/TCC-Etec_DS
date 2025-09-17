<?php
session_start();

// Limpa todas as variáveis de sessão
$_SESSION = array();

// Se existir um cookie de sessão, remove-o
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroi a sessão
session_destroy();

header("Location: ../index.html");
exit();
