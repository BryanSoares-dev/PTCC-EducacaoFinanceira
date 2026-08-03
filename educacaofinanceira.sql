-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 06/07/2026 às 23:35
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

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
-- Estrutura para tabela `movimentacoes`
--

CREATE TABLE `movimentacoes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo` enum('entrada','saida') NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `movimentacoes`
--

INSERT INTO `movimentacoes` (`id`, `usuario_id`, `tipo`, `valor`, `descricao`, `categoria`, `data_criacao`) VALUES
(1, 4, 'entrada', 100.00, 'gostei kk', 'salario', '2026-06-03 16:22:58'),
(2, 4, 'saida', 10.00, 'gostei kk', 'moradia', '2026-06-03 16:31:37');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `telefone` varchar(25) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `foto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `telefone`, `data_criacao`, `foto`) VALUES
(1, 'ana portela', 'anaportela@gmail.com', '$2y$10$AQak0pbULnxpniQnTOmFcuZtVDIfBPDxH4SqGxscr9ot.2ExymF9i', '', '2026-04-29 16:09:29', ''),
(2, 'isadora', 'isadorajans@gmail.com', '$2y$10$SMNthbFKZrKhI0BD5pOUz.bzQOHON1NO5i4BErJas7EMvded0jzKS', '11875938264', '2026-04-29 16:30:02', ''),
(3, 'JoÃ£o Rabelo', 'rabelo@gmail.com', '$2y$10$P8D0Jm6eXaT0uQuTTnuQru4dZtnm2IcTO.IW1tf8kDHjX7dh8gvsu', '', '2026-04-29 16:46:02', ''),
(4, 'João', 'joaohenrique2512jp@gmail.com', '$2y$10$bPhjpbOACqkjOjEbuZ55xuX7lmo5JE7e7Ce44MTP1XjbrqaO75Fnq', '', '2026-06-03 16:21:51', ''),
(5, 'Bryan Soares', 'soares123@gmail.com', '$2y$10$f0ZyLv6zU.mjArU4Gh1B3ufHbZJadv7YdG046EY8M7OANmsrP9pk6', '', '2026-07-06 19:52:16', '');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `movimentacoes`
--
ALTER TABLE `movimentacoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `movimentacoes`
--
ALTER TABLE `movimentacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
