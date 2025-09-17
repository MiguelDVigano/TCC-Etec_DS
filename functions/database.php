<?php

/**
 * Busca todas as salas ordenadas por título
 * @param mysqli $conn Conexão com o banco
 * @return mysqli_result|false Resultado da query
 */
function buscarSalas($conn) {
    $sql = "SELECT id_sala, titulo_sala FROM sala ORDER BY titulo_sala";
    return $conn->query($sql);
}

/**
 * Busca todas as turmas ordenadas por nome
 * @param mysqli $conn Conexão com o banco
 * @return mysqli_result|false Resultado da query
 */
function buscarTurmas($conn) {
    $sql = "SELECT id_turma, nome_turma FROM turma ORDER BY nome_turma";
    return $conn->query($sql);
}

/**
 * Busca todos os professores
 * @param mysqli $conn Conexão com o banco
 * @return mysqli_result|false Resultado da query
 */
function buscarProfessores($conn) {
    $sql = "SELECT id_usuario AS id_professor, nome FROM usuario WHERE tipo_usuario = 'Professor'";
    return $conn->query($sql);
}

/**
 * Busca todas as salas com detalhes completos
 * @param mysqli $conn Conexão com o banco
 * @return mysqli_result|false Resultado da query
 */
function buscarSalasCompletas($conn) {
    $sql = "SELECT * FROM sala";
    return $conn->query($sql);
}

/**
 * Busca turmas de um aluno específico
 * @param mysqli $conn Conexão com o banco
 * @param int $id_aluno ID do aluno
 * @return array Array com IDs das turmas
 */
function buscarTurmasAluno($conn, $id_aluno) {
    $sql = "SELECT id_turma FROM matricula WHERE id_aluno = $id_aluno";
    $result = $conn->query($sql);
    $turmas = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $turmas[] = $row['id_turma'];
        }
    }
    
    return $turmas;
}

/**
 * Busca chamados em aberto ou em andamento
 * @param mysqli $conn Conexão com o banco
 * @return mysqli_result|false Resultado da query
 */
function buscarChamadosAbertos($conn) {
    $sql = "SELECT c.id_chamado, c.titulo_chamado, c.descricao, c.url_foto, s.titulo_sala, c.status_chamado 
            FROM chamado c
            INNER JOIN sala s ON c.id_sala = s.id_sala
            WHERE c.status_chamado = 'Aberto' OR c.status_chamado = 'Em Andamento'
            ORDER BY c.data_chamado DESC, c.id_chamado DESC";
    return $conn->query($sql);
}

?>