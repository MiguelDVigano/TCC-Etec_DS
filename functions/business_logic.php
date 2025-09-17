<?php

/**
 * Busca mensagens enviadas por um professor com filtros opcionais
 * @param mysqli $conn Conexão com o banco
 * @param int $id_remetente ID do professor remetente
 * @param string $filtro_assunto Filtro por assunto (opcional)
 * @param string $filtro_data Filtro por data (opcional)
 * @return mysqli_result|false Resultado da query
 */
function buscarMensagensEnviadas($conn, $id_remetente, $filtro_assunto = '', $filtro_data = '') {
    $where = "m.id_remetente = $id_remetente";
    
    if ($filtro_assunto !== '') {
        $where .= " AND m.assunto LIKE '%" . $conn->real_escape_string($filtro_assunto) . "%'";
    }
    
    if ($filtro_data !== '') {
        $where .= " AND DATE(m.data_envio) = '" . $conn->real_escape_string($filtro_data) . "'";
    }
    
    $sql = "
        SELECT m.assunto, m.mensagem, m.data_envio,
               (SELECT GROUP_CONCAT(t.nome_turma SEPARATOR ', ')
                FROM mensagem_turma mt
                JOIN turma t ON mt.id_mensagem = m.id_mensagem
                WHERE mt.id_mensagem = m.id_mensagem) AS turmas_destinatarias
        FROM mensagem m
        WHERE $where
        ORDER BY m.data_envio DESC
    ";
    
    return $conn->query($sql);
}

/**
 * Busca mensagens para um aluno baseado no filtro
 * @param mysqli $conn Conexão com o banco
 * @param int $id_aluno ID do aluno
 * @param array $turmas Array com IDs das turmas do aluno
 * @param string $filtro Tipo de filtro (nao_lidas, lidas, antigas)
 * @return mysqli_result|false Resultado da query
 */
function buscarMensagensAluno($conn, $id_aluno, $turmas, $filtro = 'nao_lidas') {
    $turmas_str = implode(',', $turmas) ?: '0'; // Evitar erro se sem turmas
    
    switch ($filtro) {
        case 'lidas':
            // Mensagens já lidas pelo aluno e ainda válidas (não expiradas)
            $sql = "
                SELECT m.id_mensagem, m.assunto, m.mensagem, m.data_envio, u.nome AS remetente
                FROM mensagem m
                INNER JOIN usuario u ON m.id_remetente = u.id_usuario
                INNER JOIN mensagem_leitura ml ON ml.id_mensagem = m.id_mensagem AND ml.id_aluno = $id_aluno
                WHERE (
                    (m.enviar_para_todas = 1 AND mensagem_valida(m.id_mensagem))
                    OR EXISTS (
                        SELECT 1 FROM mensagem_turma mt 
                        WHERE mt.id_mensagem = m.id_mensagem 
                        AND mensagem_valida(m.id_mensagem)
                        AND mt.id_turma IN ($turmas_str)
                    )
                )
                ORDER BY m.data_envio DESC
            ";
            break;
            
        case 'antigas':
            // Mensagens antigas (expiradas)
            $sql = "
                SELECT m.id_mensagem, m.assunto, m.mensagem, m.data_envio, u.nome AS remetente
                FROM mensagem m
                INNER JOIN usuario u ON m.id_remetente = u.id_usuario
                WHERE (
                    (m.enviar_para_todas = 1 AND NOT mensagem_valida(m.id_mensagem))
                    OR EXISTS (
                        SELECT 1 FROM mensagem_turma mt 
                        WHERE mt.id_mensagem = m.id_mensagem 
                        AND NOT mensagem_valida(m.id_mensagem)
                        AND mt.id_turma IN ($turmas_str)
                    )
                )
                ORDER BY m.data_envio DESC
            ";
            break;
            
        default: // nao_lidas
            // Mensagens não lidas e válidas (não expiradas)
            $sql = "
                SELECT m.id_mensagem, m.assunto, m.mensagem, m.data_envio, u.nome AS remetente
                FROM mensagem m
                INNER JOIN usuario u ON m.id_remetente = u.id_usuario
                LEFT JOIN mensagem_leitura ml ON ml.id_mensagem = m.id_mensagem AND ml.id_aluno = $id_aluno
                WHERE (
                    (m.enviar_para_todas = 1 AND mensagem_valida(m.id_mensagem))
                    OR EXISTS (
                        SELECT 1 FROM mensagem_turma mt 
                        WHERE mt.id_mensagem = m.id_mensagem 
                        AND mensagem_valida(m.id_mensagem)
                        AND mt.id_turma IN ($turmas_str)
                    )
                )
                AND ml.id_leitura IS NULL
                ORDER BY m.data_envio DESC
            ";
            break;
    }
    
    return $conn->query($sql);
}

/**
 * Obtém o título da seção de mensagens baseado no filtro
 * @param string $filtro Tipo de filtro
 * @return string Título da seção
 */
function obterTituloMensagens($filtro) {
    switch ($filtro) {
        case 'lidas':
            return "Mensagens já lidas";
        case 'antigas':
            return "Mensagens antigas";
        default:
            return "Suas Mensagens Não Lidas";
    }
}

?>