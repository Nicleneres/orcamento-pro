-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 05/09/2025 às 18:53
-- Versão do servidor: 9.1.0
-- Versão do PHP: 8.4.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `projeto_contato`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `cnpj` varchar(20) DEFAULT NULL,
  `endereco` text,
  `telefone` varchar(50) DEFAULT NULL,
  `contato` varchar(255) DEFAULT NULL,
  `data_cadastro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cnpj` (`cnpj`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `empresa`
--

DROP TABLE IF EXISTS `empresa`;
CREATE TABLE IF NOT EXISTS `empresa` (
  `id` int NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `endereco` text,
  `telefone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `logo_url` text,
  `chave_pix` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `empresa`
--

INSERT INTO `empresa` (`id`, `nome`, `endereco`, `telefone`, `email`, `logo_url`, `chave_pix`) VALUES
(1, 'Farmvet C D S Veterinarios', 'Rua Antonio Nery, 150', '(15) 99773-1683', 'farmvetcd@gmail.com', '', 'farmvetcd@gmail.com');

-- --------------------------------------------------------

--
-- Estrutura para tabela `notificacoes`
--

DROP TABLE IF EXISTS `notificacoes`;
CREATE TABLE IF NOT EXISTS `notificacoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `mensagem` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `lida` tinyint(1) NOT NULL DEFAULT '0',
  `data_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `notificacoes`
--

INSERT INTO `notificacoes` (`id`, `usuario_id`, `mensagem`, `link`, `lida`, `data_criacao`) VALUES
(1, 1, 'O vendedor  criou o orçamento #5.', 'ver_orcamento.php?id=5', 1, '2025-09-04 13:08:47'),
(2, 1, 'O vendedor  criou o orçamento #6.', 'ver_orcamento.php?id=6', 1, '2025-09-04 13:08:53'),
(3, 1, 'O vendedor  criou o orçamento #7.', 'ver_orcamento.php?id=7', 1, '2025-09-04 13:11:47'),
(4, 1, 'O vendedor  criou o orçamento #8.', 'ver_orcamento.php?id=8', 1, '2025-09-04 13:13:02'),
(5, 1, 'O vendedor Fernando Zuchi Junior criou o orçamento #9.', 'ver_orcamento.php?id=9', 1, '2025-09-04 13:24:14'),
(6, 1, 'O vendedor Administrador criou o orçamento #10.', 'ver_orcamento.php?id=10', 1, '2025-09-04 19:28:26'),
(7, 1, 'O vendedor Fernando Zuchi Junior criou o orçamento #11.', 'ver_orcamento.php?id=11', 1, '2025-09-04 19:29:55'),
(8, 1, 'O vendedor Fernando Zuchi Junior criou o orçamento #12.', 'ver_orcamento.php?id=12', 1, '2025-09-05 11:15:41'),
(9, 1, 'O vendedor Administrador criou o orçamento #13.', 'ver_orcamento.php?id=13', 1, '2025-09-05 14:06:31'),
(10, 1, 'O vendedor Fernando Zuchi Junior criou o orçamento #14.', 'ver_orcamento.php?id=14', 1, '2025-09-05 14:14:13'),
(11, 1, 'O vendedor Administrador criou o orçamento #15.', 'ver_orcamento.php?id=15', 1, '2025-09-05 16:53:23'),
(12, 1, 'O vendedor Administrador criou o orçamento #16.', 'ver_orcamento.php?id=16', 1, '2025-09-05 16:53:35'),
(13, 1, 'O vendedor Administrador criou o orçamento #17.', 'ver_orcamento.php?id=17', 1, '2025-09-05 16:56:44'),
(14, 1, 'O vendedor Administrador criou o orçamento #18.', 'ver_orcamento.php?id=18', 1, '2025-09-05 17:16:42'),
(15, 1, 'O vendedor Administrador criou o orçamento #19.', 'ver_orcamento.php?id=19', 1, '2025-09-05 17:16:56'),
(16, 1, 'O vendedor Administrador criou o orçamento #20.', 'ver_orcamento.php?id=20', 1, '2025-09-05 17:23:20'),
(17, 1, 'O vendedor Administrador criou o orçamento #21.', 'ver_orcamento.php?id=21', 1, '2025-09-05 17:24:09'),
(18, 1, 'O vendedor Administrador criou o orçamento #22.', 'ver_orcamento.php?id=22', 1, '2025-09-05 17:32:35'),
(19, 1, 'O vendedor Administrador criou o orçamento #23.', 'ver_orcamento.php?id=23', 1, '2025-09-05 17:36:49'),
(20, 1, 'O vendedor Administrador criou o orçamento #24.', 'ver_orcamento.php?id=24', 1, '2025-09-05 17:43:40'),
(21, 1, 'O vendedor Administrador criou o orçamento #25.', 'ver_orcamento.php?id=25', 1, '2025-09-05 17:44:33'),
(22, 1, 'O vendedor Administrador criou o orçamento #26.', 'ver_orcamento.php?id=26', 1, '2025-09-05 17:52:44'),
(23, 1, 'O vendedor Administrador criou o orçamento #27.', 'ver_orcamento.php?id=27', 1, '2025-09-05 17:57:14'),
(24, 1, 'O vendedor Administrador criou o orçamento #28.', 'ver_orcamento.php?id=28', 1, '2025-09-05 18:04:46'),
(25, 1, 'O vendedor Administrador criou o orçamento #29.', 'ver_orcamento.php?id=29', 1, '2025-09-05 18:05:20'),
(26, 1, 'O vendedor Administrador criou o orçamento #30.', 'ver_orcamento.php?id=30', 1, '2025-09-05 18:22:01'),
(27, 1, 'O vendedor Administrador criou o orçamento #31.', 'ver_orcamento.php?id=31', 1, '2025-09-05 18:22:04'),
(28, 1, 'O vendedor Administrador criou o orçamento #32.', 'ver_orcamento.php?id=32', 1, '2025-09-05 18:27:07'),
(29, 1, 'O vendedor Administrador criou o orçamento #33.', 'ver_orcamento.php?id=33', 1, '2025-09-05 18:33:08'),
(30, 1, 'O vendedor Administrador criou o orçamento #34.', 'ver_orcamento.php?id=34', 1, '2025-09-05 18:45:46');

-- --------------------------------------------------------

--
-- Estrutura para tabela `orcamentos`
--

DROP TABLE IF EXISTS `orcamentos`;
CREATE TABLE IF NOT EXISTS `orcamentos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_nome` varchar(255) NOT NULL,
  `cliente_cnpj` varchar(20) DEFAULT NULL,
  `cliente_endereco` text,
  `cliente_telefone` varchar(50) DEFAULT NULL,
  `cliente_contato` varchar(255) DEFAULT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `desconto` decimal(10,2) DEFAULT NULL,
  `forma_pagamento` varchar(50) DEFAULT NULL,
  `data_orcamento` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `orcamento_itens`
--

DROP TABLE IF EXISTS `orcamento_itens`;
CREATE TABLE IF NOT EXISTS `orcamento_itens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `orcamento_id` int NOT NULL,
  `produto_nome` varchar(255) NOT NULL,
  `quantidade` int NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL,
  `preco_total` decimal(10,2) NOT NULL,
  `tipo_preco` enum('final','meio','loja') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `orcamento_id` (`orcamento_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

DROP TABLE IF EXISTS `produtos`;
CREATE TABLE IF NOT EXISTS `produtos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `preco_final` decimal(10,2) NOT NULL,
  `preco_meio` decimal(10,2) DEFAULT NULL,
  `preco_loja` decimal(10,2) DEFAULT NULL,
  `data_cadastro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `preco_final`, `preco_meio`, `preco_loja`, `data_cadastro`) VALUES
(6, 'Parasit 100 | 20KG', 12.50, 10.50, 9.00, '2025-09-03 23:11:15'),
(9, 'Papilomazin | 20KG', 250.00, 210.00, 190.00, '2025-09-05 12:29:01'),
(10, 'Papilomazin | 20KG', 250.00, 210.00, 175.00, '2025-09-05 12:29:52'),
(11, 'Papilomazin | 20KG', 250.00, 210.00, 175.00, '2025-09-05 12:39:34'),
(13, 'Parasit 100 | 20KG', 250.00, 200.00, 100.00, '2025-09-05 12:40:58'),
(14, 'Parasit 100 | 20KG', 250.00, 200.00, 1000.00, '2025-09-05 12:56:27'),
(17, 'Mast 100 | 20KG', 150.00, 130.00, 100.00, '2025-09-05 14:04:19');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `cargo` enum('administrador','vendedor') NOT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `endereco` text,
  `data_cadastro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `primeiro_login` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `cargo`, `foto_perfil`, `endereco`, `data_cadastro`, `primeiro_login`) VALUES
(1, 'Administrador', 'admin@email.com', '$2y$10$UFc.TvDSN1P7/G1cSVKgTOxH97NHJfnTXmQ0Pm2J90J43eTT1.Dja', 'administrador', 'uploads/68b8eb5b312d2-IMG_20211004_072424_371.jpg', 'Rua Roberto Bertola, 96', '2025-09-04 01:13:41', 0),
(2, 'Nilcle', 'nilcleneresmodesto@gmail.com', 'nil1234', 'vendedor', NULL, NULL, '2025-09-04 01:13:41', 1),
(3, 'Fernando Zuchi Junior', 'Fernandozuchi@gmail.com', '$2y$10$Q5otzigu1o8CVw4/GJNK5OwB1ZqHWIiyAaDXdSWBvKY5ESgqRLjkK', 'vendedor', NULL, NULL, '2025-09-04 01:13:41', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
