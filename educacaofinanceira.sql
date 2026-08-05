-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 05-Ago-2026 às 18:56
-- Versão do servidor: 10.4.22-MariaDB
-- versão do PHP: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `educacaofinanceira`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `movimentacoes`
--

CREATE TABLE `movimentacoes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo` enum('entrada','saida') NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `movimentacoes`
--

INSERT INTO `movimentacoes` (`id`, `usuario_id`, `tipo`, `valor`, `descricao`, `categoria`, `data_criacao`) VALUES
(1, 4, 'entrada', '100.00', 'gostei kk', 'salario', '2026-06-03 16:22:58'),
(2, 4, 'saida', '10.00', 'gostei kk', 'moradia', '2026-06-03 16:31:37'),
(3, 5, 'entrada', '20.00', 'retorno da bet', 'Outros', '2026-07-04 02:41:37'),
(4, 5, 'saida', '2.00', '22', 'Alimentação', '2026-07-04 02:42:30'),
(5, 8, 'entrada', '200.00', 'aa', 'Investimentos', '2026-07-12 01:38:14');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `itemid` varchar(255) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `oauth_uid` varchar(255) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `telefone` varchar(25) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `provedor` varchar(20) DEFAULT 'local'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `itemid`, `nome`, `email`, `oauth_uid`, `foto`, `senha`, `telefone`, `data_criacao`, `provedor`) VALUES
(1, '', 'ana portela', 'anaportela@gmail.com', NULL, NULL, '$2y$10$AQak0pbULnxpniQnTOmFcuZtVDIfBPDxH4SqGxscr9ot.2ExymF9i', '', '2026-04-29 16:09:29', 'local'),
(2, '', 'isadora', 'isadorajans@gmail.com', NULL, NULL, '$2y$10$SMNthbFKZrKhI0BD5pOUz.bzQOHON1NO5i4BErJas7EMvded0jzKS', '11875938264', '2026-04-29 16:30:02', 'local'),
(3, '', 'JoÃ£o Rabelo', 'rabelo@gmail.com', NULL, NULL, '$2y$10$P8D0Jm6eXaT0uQuTTnuQru4dZtnm2IcTO.IW1tf8kDHjX7dh8gvsu', '', '2026-04-29 16:46:02', 'local'),
(4, '', 'João', 'joaohenrique2512jp@gmail.com', NULL, NULL, '$2y$10$bPhjpbOACqkjOjEbuZ55xuX7lmo5JE7e7Ce44MTP1XjbrqaO75Fnq', '', '2026-06-03 16:21:51', 'local'),
(5, '', 'João Henrique dos Santos Rabelo', 'jh.rabelo7@gmail.com', '106090355274792785689', NULL, '$2y$10$.67NR3MpIv7ySzlVHBOoQerGJUKTqqtX3FtxjQDBQRa1gy7N6BItu', '', '2026-07-04 02:39:31', 'local'),
(8, '', 'Rabelx', 'jhrabelo20094541@gmail.com', '117781224819786275127', 'https://lh3.googleusercontent.com/a/ACg8ocIVAO_VpEqX5hAdKJXkwVtnJ2w3_Z0HI95nAsvQVYA67r4txtM=s96-c', '$2y$10$nk64J4bz5qHWCidSetU55ughXyigJbyoNjynxpdIRKER2N0cgAOIK', NULL, '2026-07-05 12:29:36', 'ambos'),
(9, '', 'bryan', 'soares123@gmail.com', NULL, NULL, '$2y$10$aRpKKlDBBb8mTe0V4TgDEO/1DqiztrGEDOaQWOTqYHMRc/M/kWIjS', '', '2026-08-05 16:36:03', 'local');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `movimentacoes`
--
ALTER TABLE `movimentacoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `movimentacoes`
--
ALTER TABLE `movimentacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
