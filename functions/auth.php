<?php

/**
 * Verifica se o usuário está autenticado e é do tipo especificado
 * @param string $tipo_usuario Tipo de usuário esperado (Professor, Aluno, Manutencao)
 * @param string $redirect_url URL para redirecionamento se não autenticado (padrão: ../Login.html)
 * @return array Dados da sessão se autenticado
 */
function verificarAutenticacao($tipo_usuario, $redirect_url = "../Login.html") {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo_usuario"] !== $tipo_usuario) {
        header("Location: $redirect_url");
        exit();
    }
    
    return [
        'id_usuario' => $_SESSION["id_usuario"],
        'tipo_usuario' => $_SESSION["tipo_usuario"]
    ];
}

/**
 * Inicia sessão se ainda não iniciada
 */
function iniciarSessao() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

?>