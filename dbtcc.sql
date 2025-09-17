-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 17, 2025 at 08:41 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbtcc`
--

DELIMITER $$
--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `mensagem_valida` (`idMsg` INT) RETURNS TINYINT(1) DETERMINISTIC BEGIN
    DECLARE expira DATETIME;

    SELECT data_expiracao 
    INTO expira
    FROM mensagem
    WHERE id_mensagem = idMsg;

    -- Retorna TRUE se não expirou ou se não tiver data de expiração
    RETURN (expira IS NULL OR expira > NOW());
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `chamado`
--

CREATE TABLE `chamado` (
  `id_chamado` int(11) NOT NULL,
  `titulo_chamado` varchar(100) NOT NULL,
  `descricao` text NOT NULL,
  `url_foto` varchar(200) DEFAULT NULL,
  `data_chamado` date NOT NULL,
  `status_chamado` enum('Aberto','Em Andamento','Concluido') DEFAULT 'Aberto',
  `id_sala` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chamado`
--

INSERT INTO `chamado` (`id_chamado`, `titulo_chamado`, `descricao`, `url_foto`, `data_chamado`, `status_chamado`, `id_sala`, `created_at`, `updated_at`) VALUES
(1, 'Defeito - Laboratório Informática 1', 'o problema é que esse whallpaper está incorreto na maquina 2', '../../uploads/chamados/ch_68c31804cfab0.png', '2025-09-11', 'Concluido', 1, '2025-09-11 18:42:12', '2025-09-17 17:51:48');

-- --------------------------------------------------------

--
-- Table structure for table `matricula`
--

CREATE TABLE `matricula` (
  `id_matricula` int(11) NOT NULL,
  `id_aluno` int(11) NOT NULL,
  `id_turma` int(11) NOT NULL,
  `data_matricula` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mensagem`
--

CREATE TABLE `mensagem` (
  `id_mensagem` int(11) NOT NULL,
  `assunto` varchar(100) NOT NULL,
  `mensagem` text NOT NULL,
  `data_envio` datetime NOT NULL,
  `id_remetente` int(11) NOT NULL,
  `enviar_para_todas` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `data_expiracao` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mensagem`
--

INSERT INTO `mensagem` (`id_mensagem`, `assunto`, `mensagem`, `data_envio`, `id_remetente`, `enviar_para_todas`, `created_at`, `updated_at`, `data_expiracao`) VALUES
(1, 'Mensagem de Teste', 'djahhgsdiwagdopwa', '2025-09-15 17:59:00', 2, 0, '2025-09-11 17:59:22', '2025-09-11 17:59:22', NULL),
(2, 'Mensagem para todos', 'teste de mensagem para todas as turmas', '2025-09-18 14:59:00', 2, 1, '2025-09-11 17:59:59', '2025-09-11 17:59:59', NULL),
(3, 'Aviso de Reunião Antiga', 'Reunião que já aconteceu em agosto.', '2025-08-10 14:00:00', 1, 1, '2025-09-17 18:09:32', '2025-09-17 18:09:32', NULL),
(4, 'Manutenção Finalizada', 'Manutenção concluída em setembro.', '2025-09-01 09:00:00', 1, 1, '2025-09-17 18:09:32', '2025-09-17 18:09:32', NULL),
(5, 'Evento Encerrado', 'O evento da semana passada já foi realizado.', '2025-09-12 18:00:00', 1, 1, '2025-09-17 18:09:32', '2025-09-17 18:09:32', NULL),
(6, 'Aviso de Prova', 'A prova será no dia 25 de setembro.', '2025-09-25 10:00:00', 1, 1, '2025-09-17 18:09:32', '2025-09-17 18:09:32', NULL),
(7, 'Vacinação Obrigatória', 'Campanha de vacinação até outubro.', '2025-10-05 08:00:00', 1, 1, '2025-09-17 18:09:32', '2025-09-17 18:09:32', NULL),
(8, 'Encontro de Pais', 'Reunião de pais marcada para novembro.', '2025-11-15 19:00:00', 1, 1, '2025-09-17 18:09:32', '2025-09-17 18:09:32', NULL),
(9, 'Férias Escolares', 'Aviso de férias escolares em dezembro.', '2025-12-20 00:00:00', 1, 1, '2025-09-17 18:09:32', '2025-09-17 18:09:32', NULL),
(10, 'Não terá aula amnhã', 'A escola está sem água', '2025-09-17 15:36:12', 2, 1, '2025-09-17 18:36:12', '2025-09-17 18:36:12', NULL),
(11, 'Falta de água', 'amanhã não terá aula devido a falta de água', '2025-09-17 15:40:38', 1, 1, '2025-09-17 18:40:38', '2025-09-17 18:40:38', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `mensagem_leitura`
--

CREATE TABLE `mensagem_leitura` (
  `id_leitura` int(11) NOT NULL,
  `id_mensagem` int(11) NOT NULL,
  `id_aluno` int(11) NOT NULL,
  `data_leitura` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mensagem_leitura`
--

INSERT INTO `mensagem_leitura` (`id_leitura`, `id_mensagem`, `id_aluno`, `data_leitura`) VALUES
(1, 9, 1, '2025-09-17 15:28:18'),
(10, 7, 1, '2025-09-17 15:31:50');

-- --------------------------------------------------------

--
-- Table structure for table `mensagem_turma`
--

CREATE TABLE `mensagem_turma` (
  `id_mensagem` int(11) NOT NULL,
  `id_turma` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mensagem_turma`
--

INSERT INTO `mensagem_turma` (`id_mensagem`, `id_turma`) VALUES
(1, 5);

-- --------------------------------------------------------

--
-- Table structure for table `reserva`
--

CREATE TABLE `reserva` (
  `id_reserva` int(11) NOT NULL,
  `data_reserva` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fim` time NOT NULL,
  `id_professor` int(11) NOT NULL,
  `id_sala` int(11) NOT NULL,
  `id_turma` int(11) NOT NULL,
  `observacao` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reserva`
--

INSERT INTO `reserva` (`id_reserva`, `data_reserva`, `hora_inicio`, `hora_fim`, `id_professor`, `id_sala`, `id_turma`, `observacao`, `created_at`, `updated_at`) VALUES
(1, '2025-09-24', '08:50:00', '11:40:00', 6, 5, 5, 'Palestra de educação financeira', '2025-09-17 18:34:48', '2025-09-17 18:34:48'),
(2, '2025-09-24', '08:00:00', '08:50:00', 2, 5, 2, 'aswefaf', '2025-09-17 18:35:22', '2025-09-17 18:35:22');

-- --------------------------------------------------------

--
-- Table structure for table `sala`
--

CREATE TABLE `sala` (
  `id_sala` int(11) NOT NULL,
  `numero_sala` int(11) NOT NULL,
  `titulo_sala` varchar(100) NOT NULL,
  `tipo_sala` enum('Teorica','Pratica','Mista') NOT NULL,
  `capacidade` int(11) NOT NULL,
  `status_sala` enum('Ativa','Ocupado','Inativa') DEFAULT 'Ativa',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sala`
--

INSERT INTO `sala` (`id_sala`, `numero_sala`, `titulo_sala`, `tipo_sala`, `capacidade`, `status_sala`, `created_at`, `updated_at`) VALUES
(1, 101, 'Laboratório Informática 1', 'Pratica', 30, 'Ativa', '2025-09-11 18:22:10', '2025-09-11 18:22:10'),
(2, 102, 'Laboratório Informática 2', 'Pratica', 25, 'Ativa', '2025-09-11 18:22:10', '2025-09-11 18:22:10'),
(3, 201, 'Sala Matemática', 'Teorica', 40, 'Ativa', '2025-09-11 18:22:10', '2025-09-11 18:22:10'),
(4, 202, 'Sala História', 'Teorica', 35, 'Ativa', '2025-09-11 18:22:10', '2025-09-11 18:22:10'),
(5, 301, 'Auditório', 'Mista', 80, 'Ativa', '2025-09-11 18:22:10', '2025-09-11 18:22:10'),
(6, 302, 'Sala Física', 'Teorica', 45, 'Ativa', '2025-09-11 18:22:10', '2025-09-11 18:22:10'),
(7, 401, 'Laboratório Química', 'Pratica', 28, 'Ocupado', '2025-09-11 18:22:10', '2025-09-11 18:22:10'),
(8, 402, 'Sala Biologia', 'Teorica', 40, 'Ativa', '2025-09-11 18:22:10', '2025-09-11 18:22:10'),
(9, 501, 'Biblioteca', 'Mista', 50, 'Ativa', '2025-09-11 18:22:10', '2025-09-11 18:22:10'),
(10, 502, 'Sala de Artes', 'Mista', 25, 'Ativa', '2025-09-11 18:22:10', '2025-09-11 18:22:10');

-- --------------------------------------------------------

--
-- Table structure for table `turma`
--

CREATE TABLE `turma` (
  `id_turma` int(11) NOT NULL,
  `nome_turma` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `turma`
--

INSERT INTO `turma` (`id_turma`, `nome_turma`, `created_at`, `updated_at`) VALUES
(1, '1º Ano A', '2025-09-11 17:56:13', '2025-09-11 17:56:13'),
(2, '1º Ano B', '2025-09-11 17:56:13', '2025-09-11 17:56:13'),
(3, '2º Ano A', '2025-09-11 17:56:13', '2025-09-11 17:56:13'),
(4, '2º Ano B', '2025-09-11 17:56:13', '2025-09-11 17:56:13'),
(5, '3º Ano A', '2025-09-11 17:56:13', '2025-09-11 17:56:13'),
(6, '3º Ano B', '2025-09-11 17:56:13', '2025-09-11 17:56:13'),
(7, '4º Ano A', '2025-09-11 17:56:13', '2025-09-11 17:56:13'),
(8, '4º Ano B', '2025-09-11 17:56:13', '2025-09-11 17:56:13'),
(9, '5º Ano A', '2025-09-11 17:56:13', '2025-09-11 17:56:13'),
(10, '5º Ano B', '2025-09-11 17:56:13', '2025-09-11 17:56:13');

-- --------------------------------------------------------

--
-- Table structure for table `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `tipo_usuario` enum('Aluno','Professor','Manutencao') NOT NULL,
  `telefone` char(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nome`, `email`, `senha_hash`, `tipo_usuario`, `telefone`, `created_at`, `updated_at`) VALUES
(1, 'João Silva', 'joao.silva@email.com', 'hash123', 'Aluno', '11999999999', '2025-09-11 17:52:01', '2025-09-11 17:52:01'),
(2, 'Maria Souza', 'maria.souza@email.com', 'hash456', 'Professor', '11888888888', '2025-09-11 17:52:01', '2025-09-11 17:52:01'),
(3, 'Carlos Lima', 'carlos.lima@email.com', 'hash789', 'Manutencao', '11777777777', '2025-09-11 17:52:01', '2025-09-11 17:52:01'),
(4, 'Ana Pereira', 'ana.pereira@email.com', 'hash111', 'Aluno', '11666666666', '2025-09-11 17:52:01', '2025-09-11 17:52:01'),
(5, 'Rafael Gomes', 'rafael.gomes@email.com', 'hash222', 'Aluno', '11555555555', '2025-09-11 17:52:01', '2025-09-11 17:52:01'),
(6, 'Juliana Rocha', 'juliana.rocha@email.com', 'hash333', 'Professor', '11444444444', '2025-09-11 17:52:01', '2025-09-11 17:52:01'),
(7, 'Fernanda Costa', 'fernanda.costa@email.com', 'hash444', 'Aluno', '11333333333', '2025-09-11 17:52:01', '2025-09-11 17:52:01'),
(8, 'Pedro Almeida', 'pedro.almeida@email.com', 'hash555', 'Aluno', '11222222222', '2025-09-11 17:52:01', '2025-09-11 17:52:01'),
(9, 'Lucas Santos', 'lucas.santos@email.com', 'hash666', 'Aluno', '11111111111', '2025-09-11 17:52:01', '2025-09-11 17:52:01'),
(10, 'Bruno Oliveira', 'bruno.oliveira@email.com', 'hash777', 'Manutencao', '11000000000', '2025-09-11 17:52:01', '2025-09-11 17:52:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chamado`
--
ALTER TABLE `chamado`
  ADD PRIMARY KEY (`id_chamado`),
  ADD KEY `id_sala` (`id_sala`);

--
-- Indexes for table `matricula`
--
ALTER TABLE `matricula`
  ADD PRIMARY KEY (`id_matricula`),
  ADD KEY `id_aluno` (`id_aluno`),
  ADD KEY `id_turma` (`id_turma`);

--
-- Indexes for table `mensagem`
--
ALTER TABLE `mensagem`
  ADD PRIMARY KEY (`id_mensagem`),
  ADD KEY `id_remetente` (`id_remetente`);

--
-- Indexes for table `mensagem_leitura`
--
ALTER TABLE `mensagem_leitura`
  ADD PRIMARY KEY (`id_leitura`),
  ADD UNIQUE KEY `id_mensagem` (`id_mensagem`,`id_aluno`),
  ADD KEY `fk_mensagem_leitura_aluno` (`id_aluno`);

--
-- Indexes for table `mensagem_turma`
--
ALTER TABLE `mensagem_turma`
  ADD PRIMARY KEY (`id_mensagem`,`id_turma`),
  ADD KEY `id_turma` (`id_turma`);

--
-- Indexes for table `reserva`
--
ALTER TABLE `reserva`
  ADD PRIMARY KEY (`id_reserva`),
  ADD KEY `id_professor` (`id_professor`),
  ADD KEY `id_sala` (`id_sala`),
  ADD KEY `id_turma` (`id_turma`);

--
-- Indexes for table `sala`
--
ALTER TABLE `sala`
  ADD PRIMARY KEY (`id_sala`),
  ADD KEY `idx_numero_sala` (`numero_sala`);

--
-- Indexes for table `turma`
--
ALTER TABLE `turma`
  ADD PRIMARY KEY (`id_turma`),
  ADD KEY `idx_nome_turma` (`nome_turma`);

--
-- Indexes for table `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email_usuario` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chamado`
--
ALTER TABLE `chamado`
  MODIFY `id_chamado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `matricula`
--
ALTER TABLE `matricula`
  MODIFY `id_matricula` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mensagem`
--
ALTER TABLE `mensagem`
  MODIFY `id_mensagem` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `mensagem_leitura`
--
ALTER TABLE `mensagem_leitura`
  MODIFY `id_leitura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `reserva`
--
ALTER TABLE `reserva`
  MODIFY `id_reserva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sala`
--
ALTER TABLE `sala`
  MODIFY `id_sala` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `turma`
--
ALTER TABLE `turma`
  MODIFY `id_turma` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chamado`
--
ALTER TABLE `chamado`
  ADD CONSTRAINT `chamado_ibfk_1` FOREIGN KEY (`id_sala`) REFERENCES `sala` (`id_sala`) ON DELETE CASCADE;

--
-- Constraints for table `matricula`
--
ALTER TABLE `matricula`
  ADD CONSTRAINT `matricula_ibfk_1` FOREIGN KEY (`id_aluno`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `matricula_ibfk_2` FOREIGN KEY (`id_turma`) REFERENCES `turma` (`id_turma`) ON DELETE CASCADE;

--
-- Constraints for table `mensagem`
--
ALTER TABLE `mensagem`
  ADD CONSTRAINT `mensagem_ibfk_1` FOREIGN KEY (`id_remetente`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Constraints for table `mensagem_leitura`
--
ALTER TABLE `mensagem_leitura`
  ADD CONSTRAINT `fk_mensagem_leitura_aluno` FOREIGN KEY (`id_aluno`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mensagem_leitura_mensagem` FOREIGN KEY (`id_mensagem`) REFERENCES `mensagem` (`id_mensagem`) ON DELETE CASCADE;

--
-- Constraints for table `mensagem_turma`
--
ALTER TABLE `mensagem_turma`
  ADD CONSTRAINT `mensagem_turma_ibfk_1` FOREIGN KEY (`id_mensagem`) REFERENCES `mensagem` (`id_mensagem`) ON DELETE CASCADE,
  ADD CONSTRAINT `mensagem_turma_ibfk_2` FOREIGN KEY (`id_turma`) REFERENCES `turma` (`id_turma`) ON DELETE CASCADE;

--
-- Constraints for table `reserva`
--
ALTER TABLE `reserva`
  ADD CONSTRAINT `reserva_ibfk_1` FOREIGN KEY (`id_professor`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `reserva_ibfk_2` FOREIGN KEY (`id_sala`) REFERENCES `sala` (`id_sala`) ON DELETE CASCADE,
  ADD CONSTRAINT `reserva_ibfk_3` FOREIGN KEY (`id_turma`) REFERENCES `turma` (`id_turma`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
