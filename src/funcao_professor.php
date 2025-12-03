<?php

function buscarTurmas($conn) {
    $sql_turmas = "SELECT id_turma, nome_turma FROM turma ORDER BY nome_turma";
    return $conn->query($sql_turmas);
}

function obterFiltrosMensagem() {
    $filtro_assunto = isset($_GET['filtro_assunto']) ? trim($_GET['filtro_assunto']) : '';
    $filtro_data = isset($_GET['filtro_data']) ? trim($_GET['filtro_data']) : '';
    return [$filtro_assunto, $filtro_data];
}

function buscarMensagensEnviadas($conn, $id_remetente, $filtro_assunto, $filtro_data) {
    $where = "m.id_remetente = $id_remetente";
    if ($filtro_assunto !== '') {
        $where .= " AND m.assunto LIKE '%" . $conn->real_escape_string($filtro_assunto) . "%'";
    }
    if ($filtro_data !== '') {
        $where .= " AND DATE(m.data_envio) = '" . $conn->real_escape_string($filtro_data) . "'";
    }

    // CONSULTA CORRIGIDA: usar LEFT JOIN e GROUP_CONCAT com a relação correta
    $sql_mensagens_enviadas = "
        SELECT m.id_mensagem, m.assunto, m.mensagem, m.data_envio,
               m.enviar_para_todas,
               CASE 
                   WHEN m.enviar_para_todas = 1 THEN 'Todas as turmas'
                   ELSE COALESCE(
                       (SELECT GROUP_CONCAT(t.nome_turma SEPARATOR ', ')
                        FROM mensagem_turma mt
                        JOIN turma t ON mt.id_turma = t.id_turma
                        WHERE mt.id_mensagem = m.id_mensagem), 
                       '—'
                   )
               END AS turmas_destinatarias
        FROM mensagem m
        WHERE $where
        GROUP BY m.id_mensagem
        ORDER BY m.data_envio DESC
    ";
    
    return $conn->query($sql_mensagens_enviadas);
}

function carregarDadosProfessor($conn, $id_remetente) {
    $result_turmas = buscarTurmas($conn);
    list($filtro_assunto, $filtro_data) = obterFiltrosMensagem();
    $result_mensagens_enviadas = buscarMensagensEnviadas($conn, $id_remetente, $filtro_assunto, $filtro_data);
    return [$result_turmas, $filtro_assunto, $filtro_data, $result_mensagens_enviadas];
}