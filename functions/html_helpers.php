<?php

/**
 * Gera options HTML para select de salas
 * @param mysqli_result $result Resultado da query de salas
 * @return string HTML das options
 */
function gerarOptionsSalas($result) {
    $html = '<option value="" selected disabled>Escolha um laboratório</option>';
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $html .= '<option value="' . $row['id_sala'] . '">' . htmlspecialchars($row['titulo_sala']) . '</option>';
        }
    }
    
    return $html;
}

/**
 * Gera options HTML para select de turmas
 * @param mysqli_result $result Resultado da query de turmas
 * @return string HTML das options
 */
function gerarOptionsTurmas($result) {
    $html = '';
    
    if ($result && $result->num_rows > 0) {
        while ($turma = $result->fetch_assoc()) {
            $html .= '<option value="' . $turma['id_turma'] . '">' . htmlspecialchars($turma['nome_turma']) . '</option>';
        }
    }
    
    return $html;
}

/**
 * Gera options HTML para select de professores
 * @param mysqli_result $result Resultado da query de professores
 * @return string HTML das options
 */
function gerarOptionsProfessores($result) {
    $html = '<option value="">Selecione</option>';
    
    if ($result && $result->num_rows > 0) {
        while ($prof = $result->fetch_assoc()) {
            $html .= '<option value="' . $prof['id_professor'] . '">' . htmlspecialchars($prof['nome']) . '</option>';
        }
    }
    
    return $html;
}

/**
 * Gera badge HTML baseado no status da sala
 * @param string $status Status da sala
 * @return string HTML do badge
 */
function gerarBadgeStatusSala($status) {
    switch ($status) {
        case 'Ativa':
            return '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Ativo</span>';
        case 'Ocupado':
            return '<span class="badge bg-danger"><i class="bi bi-exclamation-circle me-1"></i>Ocupado</span>';
        default:
            return '<span class="badge bg-secondary"><i class="bi bi-x-circle me-1"></i>Inativo</span>';
    }
}

?>