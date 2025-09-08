-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 18-Ago-2025 às 14:36
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `dbtcc`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `chamado`
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
-- Extraindo dados da tabela `chamado`
--

INSERT INTO `chamado` (`id_chamado`, `titulo_chamado`, `descricao`, `url_foto`, `data_chamado`, `status_chamado`, `id_sala`, `created_at`, `updated_at`) VALUES
(1, 'Lâmpada queimada', 'Lâmpada da sala não acende', 'lampada.jpg', '2025-05-01', 'Aberto', 1, '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(2, 'Ar-condicionado quebrado', 'Não resfria', 'arcond.jpg', '2025-05-02', 'Aberto', 2, '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(3, 'Cadeira quebrada', 'Assento solto', 'cadeira.jpg', '2025-05-03', 'Aberto', 3, '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(4, 'Computador não liga', 'Defeito na CPU', 'pc.jpg', '2025-05-04', 'Aberto', 4, '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(5, 'Porta emperrada', 'Difícil de abrir', 'porta.jpg', '2025-05-05', 'Aberto', 5, '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(6, 'Vidro trincado', 'Janela com risco de quebrar', 'vidro.jpg', '2025-05-06', 'Aberto', 6, '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(7, 'Quadro apagado', 'Não fixa o giz', 'quadro.jpg', '2025-05-07', 'Aberto', 7, '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(8, 'Rede lenta', 'Internet instável', 'rede.jpg', '2025-05-08', 'Aberto', 8, '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(9, 'Fiação exposta', 'Risco de choque', 'fiacao.jpg', '2025-05-09', 'Aberto', 9, '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(10, 'Microfone sem som', 'Problema no cabo', 'mic.jpg', '2025-05-10', 'Aberto', 10, '2025-08-18 12:35:08', '2025-08-18 12:35:08');

-- --------------------------------------------------------

--
-- Estrutura da tabela `matricula`
--

CREATE TABLE `matricula` (
  `id_matricula` int(11) NOT NULL,
  `id_aluno` int(11) NOT NULL,
  `id_turma` int(11) NOT NULL,
  `data_matricula` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `matricula`
--

INSERT INTO `matricula` (`id_matricula`, `id_aluno`, `id_turma`, `data_matricula`) VALUES
(1, 11, 1, '2025-08-18'),
(2, 12, 2, '2025-08-18'),
(3, 13, 3, '2025-08-18'),
(4, 14, 4, '2025-08-18'),
(5, 15, 5, '2025-08-18'),
(6, 16, 6, '2025-08-18'),
(7, 17, 7, '2025-08-18'),
(8, 18, 8, '2025-08-18'),
(9, 19, 9, '2025-08-18'),
(10, 20, 10, '2025-08-18');

-- --------------------------------------------------------

--
-- Estrutura da tabela `notificacao`
--

CREATE TABLE `notificacao` (
  `id_notificacao` int(11) NOT NULL,
  `titulo_notificacao` varchar(100) NOT NULL,
  `mensagem` text NOT NULL,
  `data_notificacao` date NOT NULL,
  `id_professor` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `notificacao`
--

INSERT INTO `notificacao` (`id_notificacao`, `titulo_notificacao`, `mensagem`, `data_notificacao`, `id_professor`, `created_at`, `updated_at`) VALUES
(1, 'Aviso 1', 'Mensagem para Turma A', '2025-05-01', 1, '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(2, 'Aviso 2', 'Mensagem para Turma B', '2025-05-02', 2, '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(3, 'Aviso 3', 'Mensagem para Turma C', '2025-05-03', 3, '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(4, 'Aviso 4', 'Mensagem para Turma D', '2025-05-04', 4, '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(5, 'Aviso 5', 'Mensagem para Turma E', '2025-05-05', 5, '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(6, 'Aviso 6', 'Mensagem para Turma F', '2025-05-06', 6, '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(7, 'Aviso 7', 'Mensagem para Turma G', '2025-05-07', 7, '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(8, 'Aviso 8', 'Mensagem para Turma H', '2025-05-08', 8, '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(9, 'Aviso 9', 'Mensagem para Turma I', '2025-05-09', 9, '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(10, 'Aviso 10', 'Mensagem para Turma J', '2025-05-10', 10, '2025-08-18 12:35:08', '2025-08-18 12:35:08');

-- --------------------------------------------------------

--
-- Estrutura da tabela `notificacao_turma`
--

CREATE TABLE `notificacao_turma` (
  `id_notificacao` int(11) NOT NULL,
  `id_turma` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `notificacao_turma`
--

INSERT INTO `notificacao_turma` (`id_notificacao`, `id_turma`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8),
(9, 9),
(10, 10);

-- --------------------------------------------------------

--
-- Estrutura da tabela `reserva`
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
-- Extraindo dados da tabela `reserva`
--

INSERT INTO `reserva` (`id_reserva`, `data_reserva`, `hora_inicio`, `hora_fim`, `id_professor`, `id_sala`, `id_turma`, `observacao`, `created_at`, `updated_at`) VALUES
(1, '2025-05-01', '08:00:00', '10:00:00', 1, 1, 1, 'Aula de Matemática', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(2, '2025-05-02', '10:00:00', '12:00:00', 2, 2, 2, 'Aula de História', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(3, '2025-05-03', '13:00:00', '15:00:00', 3, 3, 3, 'Aula de Química', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(4, '2025-05-04', '15:00:00', '17:00:00', 4, 4, 4, 'Aula de Física', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(5, '2025-05-05', '08:00:00', '10:00:00', 5, 5, 5, 'Aula de Geografia', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(6, '2025-05-06', '10:00:00', '12:00:00', 6, 6, 6, 'Aula de Biologia', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(7, '2025-05-07', '13:00:00', '15:00:00', 7, 7, 7, 'Aula de Português', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(8, '2025-05-08', '15:00:00', '17:00:00', 8, 8, 8, 'Aula de Artes', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(9, '2025-05-09', '08:00:00', '10:00:00', 9, 9, 9, 'Aula de Inglês', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(10, '2025-05-10', '10:00:00', '12:00:00', 10, 10, 10, 'Aula de Educação Física', '2025-08-18 12:35:08', '2025-08-18 12:35:08');

-- --------------------------------------------------------

--
-- Estrutura da tabela `sala`
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
-- Extraindo dados da tabela `sala`
--

INSERT INTO `sala` (`id_sala`, `numero_sala`, `titulo_sala`, `tipo_sala`, `capacidade`, `status_sala`, `created_at`, `updated_at`) VALUES
(1, 101, 'Laboratório de Informática', 'Pratica', 25, 'Ativa', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(2, 102, 'Sala de Matemática', 'Teorica', 40, 'Ativa', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(3, 103, 'Laboratório de Química', 'Pratica', 20, 'Ativa', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(4, 104, 'Auditório Principal', 'Mista', 100, 'Ativa', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(5, 105, 'Sala de História', 'Teorica', 35, 'Ativa', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(6, 106, 'Laboratório de Física', 'Pratica', 20, 'Ativa', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(7, 107, 'Sala de Geografia', 'Teorica', 35, 'Ativa', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(8, 108, 'Biblioteca', 'Mista', 50, 'Ativa', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(9, 109, 'Sala de Artes', 'Teorica', 30, 'Ativa', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(10, 110, 'Laboratório de Robótica', 'Pratica', 15, 'Ativa', '2025-08-18 12:35:08', '2025-08-18 12:35:08');

-- --------------------------------------------------------

--
-- Estrutura da tabela `turma`
--

CREATE TABLE `turma` (
  `id_turma` int(11) NOT NULL,
  `nome_turma` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `turma`
--

INSERT INTO `turma` (`id_turma`, `nome_turma`, `created_at`, `updated_at`) VALUES
(1, 'Turma A', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(2, 'Turma B', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(3, 'Turma C', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(4, 'Turma D', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(5, 'Turma E', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(6, 'Turma F', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(7, 'Turma G', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(8, 'Turma H', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(9, 'Turma I', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(10, 'Turma J', '2025-08-18 12:35:08', '2025-08-18 12:35:08');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
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
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nome`, `email`, `senha_hash`, `tipo_usuario`, `telefone`, `created_at`, `updated_at`) VALUES
(1, 'João Silva', 'joao.silva@escola.com', 'hash_senha', 'Professor', '11999999901', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(2, 'Maria Souza', 'maria.souza@escola.com', 'hash_senha', 'Professor', '11999999902', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(3, 'Carlos Lima', 'carlos.lima@escola.com', 'hash_senha', 'Professor', '11999999903', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(4, 'Ana Costa', 'ana.costa@escola.com', 'hash_senha', 'Professor', '11999999904', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(5, 'Paulo Mendes', 'paulo.mendes@escola.com', 'hash_senha', 'Professor', '11999999905', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(6, 'Fernanda Rocha', 'fernanda.rocha@escola.com', 'hash_senha', 'Professor', '11999999906', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(7, 'Rafael Alves', 'rafael.alves@escola.com', 'hash_senha', 'Professor', '11999999907', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(8, 'Beatriz Ramos', 'beatriz.ramos@escola.com', 'hash_senha', 'Professor', '11999999908', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(9, 'Gustavo Dias', 'gustavo.dias@escola.com', 'hash_senha', 'Professor', '11999999909', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(10, 'Camila Pereira', 'camila.pereira@escola.com', 'hash_senha', 'Professor', '11999999910', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(11, 'Aluno 1', 'aluno1@escola.com', 'hash_senha', 'Aluno', '11988888801', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(12, 'Aluno 2', 'aluno2@escola.com', 'hash_senha', 'Aluno', '11988888802', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(13, 'Aluno 3', 'aluno3@escola.com', 'hash_senha', 'Aluno', '11988888803', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(14, 'Aluno 4', 'aluno4@escola.com', 'hash_senha', 'Aluno', '11988888804', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(15, 'Aluno 5', 'aluno5@escola.com', 'hash_senha', 'Aluno', '11988888805', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(16, 'Aluno 6', 'aluno6@escola.com', 'hash_senha', 'Aluno', '11988888806', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(17, 'Aluno 7', 'aluno7@escola.com', 'hash_senha', 'Aluno', '11988888807', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(18, 'Aluno 8', 'aluno8@escola.com', 'hash_senha', 'Aluno', '11988888808', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(19, 'Aluno 9', 'aluno9@escola.com', 'hash_senha', 'Aluno', '11988888809', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(20, 'Aluno 10', 'aluno10@escola.com', 'hash_senha', 'Aluno', '11988888810', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(21, 'Manutenção 1', 'manu1@escola.com', 'hash_senha', 'Manutencao', '11977777701', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(22, 'Manutenção 2', 'manu2@escola.com', 'hash_senha', 'Manutencao', '11977777702', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(23, 'Manutenção 3', 'manu3@escola.com', 'hash_senha', 'Manutencao', '11977777703', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(24, 'Manutenção 4', 'manu4@escola.com', 'hash_senha', 'Manutencao', '11977777704', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(25, 'Manutenção 5', 'manu5@escola.com', 'hash_senha', 'Manutencao', '11977777705', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(26, 'Manutenção 6', 'manu6@escola.com', 'hash_senha', 'Manutencao', '11977777706', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(27, 'Manutenção 7', 'manu7@escola.com', 'hash_senha', 'Manutencao', '11977777707', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(28, 'Manutenção 8', 'manu8@escola.com', 'hash_senha', 'Manutencao', '11977777708', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(29, 'Manutenção 9', 'manu9@escola.com', 'hash_senha', 'Manutencao', '11977777709', '2025-08-18 12:35:08', '2025-08-18 12:35:08'),
(30, 'Manutenção 10', 'manu10@escola.com', 'hash_senha', 'Manutencao', '11977777710', '2025-08-18 12:35:08', '2025-08-18 12:35:08');

-- NOVAS TABELAS PARA SISTEMA DE MENSAGENS

CREATE TABLE `mensagem` (
  `id_mensagem` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `assunto` VARCHAR(100) NOT NULL,
  `mensagem` TEXT NOT NULL,
  `data_envio` DATETIME NOT NULL,
  `id_remetente` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_remetente`) REFERENCES `usuario`(`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `mensagem_turma` (
  `id_mensagem` INT NOT NULL,
  `id_turma` INT NOT NULL,
  PRIMARY KEY (`id_mensagem`, `id_turma`),
  FOREIGN KEY (`id_mensagem`) REFERENCES `mensagem`(`id_mensagem`) ON DELETE CASCADE,
  FOREIGN KEY (`id_turma`) REFERENCES `turma`(`id_turma`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `chamado`
--
ALTER TABLE `chamado`
  ADD PRIMARY KEY (`id_chamado`),
  ADD KEY `id_sala` (`id_sala`);

--
-- Índices para tabela `matricula`
--
ALTER TABLE `matricula`
  ADD PRIMARY KEY (`id_matricula`),
  ADD KEY `id_aluno` (`id_aluno`),
  ADD KEY `id_turma` (`id_turma`);

--
-- Índices para tabela `notificacao`
--
ALTER TABLE `notificacao`
  ADD PRIMARY KEY (`id_notificacao`),
  ADD KEY `id_professor` (`id_professor`);

--
-- Índices para tabela `notificacao_turma`
--
ALTER TABLE `notificacao_turma`
  ADD PRIMARY KEY (`id_notificacao`,`id_turma`),
  ADD KEY `id_turma` (`id_turma`);

--
-- Índices para tabela `reserva`
--
ALTER TABLE `reserva`
  ADD PRIMARY KEY (`id_reserva`),
  ADD KEY `id_professor` (`id_professor`),
  ADD KEY `id_sala` (`id_sala`),
  ADD KEY `id_turma` (`id_turma`);

--
-- Índices para tabela `sala`
--
ALTER TABLE `sala`
  ADD PRIMARY KEY (`id_sala`),
  ADD KEY `idx_numero_sala` (`numero_sala`);

--
-- Índices para tabela `turma`
--
ALTER TABLE `turma`
  ADD PRIMARY KEY (`id_turma`),
  ADD KEY `idx_nome_turma` (`nome_turma`);

--
-- Índices para tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email_usuario` (`email`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `chamado`
--
ALTER TABLE `chamado`
  MODIFY `id_chamado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `matricula`
--
ALTER TABLE `matricula`
  MODIFY `id_matricula` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `notificacao`
--
ALTER TABLE `notificacao`
  MODIFY `id_notificacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `reserva`
--
ALTER TABLE `reserva`
  MODIFY `id_reserva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `sala`
--
ALTER TABLE `sala`
  MODIFY `id_sala` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `turma`
--
ALTER TABLE `turma`
  MODIFY `id_turma` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `chamado`
--
ALTER TABLE `chamado`
  ADD CONSTRAINT `chamado_ibfk_1` FOREIGN KEY (`id_sala`) REFERENCES `sala` (`id_sala`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `matricula`
--
ALTER TABLE `matricula`
  ADD CONSTRAINT `matricula_ibfk_1` FOREIGN KEY (`id_aluno`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `matricula_ibfk_2` FOREIGN KEY (`id_turma`) REFERENCES `turma` (`id_turma`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `notificacao`
--
ALTER TABLE `notificacao`
  ADD CONSTRAINT `notificacao_ibfk_1` FOREIGN KEY (`id_professor`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `notificacao_turma`
--
ALTER TABLE `notificacao_turma`
  ADD CONSTRAINT `notificacao_turma_ibfk_1` FOREIGN KEY (`id_notificacao`) REFERENCES `notificacao` (`id_notificacao`) ON DELETE CASCADE,
  ADD CONSTRAINT `notificacao_turma_ibfk_2` FOREIGN KEY (`id_turma`) REFERENCES `turma` (`id_turma`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `reserva`
--
ALTER TABLE `reserva`
  ADD CONSTRAINT `reserva_ibfk_1` FOREIGN KEY (`id_professor`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `reserva_ibfk_2` FOREIGN KEY (`id_sala`) REFERENCES `sala` (`id_sala`) ON DELETE CASCADE,
  ADD CONSTRAINT `reserva_ibfk_3` FOREIGN KEY (`id_turma`) REFERENCES `turma` (`id_turma`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
