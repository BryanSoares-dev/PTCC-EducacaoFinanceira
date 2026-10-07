-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 07/10/2026 às 22:12
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
-- Estrutura para tabela `arena_active_items`
--

CREATE TABLE `arena_active_items` (
  `arena_user_id` int(11) NOT NULL,
  `item_id` varchar(50) NOT NULL,
  `activated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `arena_activities`
--

CREATE TABLE `arena_activities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `arena_user_id` int(11) NOT NULL,
  `label` varchar(180) NOT NULL,
  `xp` int(11) NOT NULL DEFAULT 0,
  `coins` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `arena_comments`
--

CREATE TABLE `arena_comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `arena_user_id` int(11) NOT NULL,
  `lesson_id` int(11) DEFAULT NULL,
  `author` varchar(120) NOT NULL,
  `body` varchar(500) NOT NULL,
  `likes` int(11) NOT NULL DEFAULT 0,
  `dislikes` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `arena_comment_votes`
--

CREATE TABLE `arena_comment_votes` (
  `comment_id` bigint(20) UNSIGNED NOT NULL,
  `arena_user_id` int(11) NOT NULL,
  `vote` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `arena_exercises`
--

CREATE TABLE `arena_exercises` (
  `id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`options`)),
  `answer` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `arena_exercises`
--

INSERT INTO `arena_exercises` (`id`, `lesson_id`, `question`, `options`, `answer`) VALUES
(71, 1, 'Qual é a primeira etapa antes de escolher um investimento?', '[\"Definir objetivo, prazo e tolerância a risco\",\"Seguir a indicação mais comentada\",\"Escolher sempre o ativo que mais subiu\",\"Investir sem montar orçamento\"]', 0),
(72, 1, 'Qual atitude ajuda a montar uma reserva de emergência?', '[\"Usar ativos de alta liquidez e baixo risco\",\"Concentrar tudo em criptomoedas\",\"Escolher apenas ativos sem resgate\",\"Investir somente em ações\"]', 0),
(73, 1, 'O que é diversificação de carteira?', '[\"Distribuir recursos entre ativos e classes diferentes\",\"Comprar o mesmo ativo em várias corretoras\",\"Colocar tudo no investimento mais rentável\",\"Manter todo o dinheiro parado\"]', 0),
(74, 1, 'Se a inflação sobe e o dinheiro fica parado, o que pode ocorrer?', '[\"O poder de compra pode diminuir\",\"O saldo nominal aumenta sozinho\",\"O dinheiro passa a render automaticamente\",\"O risco desaparece\"]', 0),
(75, 1, 'O que representa a liquidez de um investimento?', '[\"A facilidade e a rapidez para transformar o ativo em dinheiro\",\"A garantia de lucro diário\",\"O tamanho da empresa emissora\",\"A quantidade de dividendos\"]', 0),
(76, 1, 'Qual é uma diferença importante entre rentabilidade nominal e real?', '[\"A real considera o efeito da inflação\",\"A nominal sempre é menor\",\"A real ignora custos e inflação\",\"Não existe diferença\"]', 0),
(77, 1, 'O que é volatilidade?', '[\"A intensidade das oscilações de preço de um ativo\",\"A certeza de receber juros\",\"O prazo de vencimento de uma conta\",\"A taxa de câmbio fixa\"]', 0),
(78, 1, 'Sobre criptomoedas, qual afirmação é mais responsável?', '[\"Podem ter alta volatilidade e exigem gestão de risco\",\"São sempre protegidas pelo FGC\",\"Não sofrem oscilações\",\"Garantem retorno positivo\"]', 0),
(79, 1, 'O que é uma stablecoin?', '[\"Um criptoativo projetado para acompanhar o valor de uma referência\",\"Uma ação de empresa estatal\",\"Um título público brasileiro\",\"Uma moeda sem qualquer risco\"]', 0),
(80, 1, 'Por que não se deve compartilhar a chave privada de uma carteira cripto?', '[\"Quem a possui pode controlar os ativos\",\"Ela serve apenas para receber promoções\",\"Ela reduz a inflação\",\"Ela garante lucro\"]', 0),
(81, 2, 'Qual é a primeira etapa antes de escolher um investimento?', '[\"Definir objetivo, prazo e tolerância a risco\",\"Seguir a indicação mais comentada\",\"Escolher sempre o ativo que mais subiu\",\"Investir sem montar orçamento\"]', 0),
(82, 2, 'Qual atitude ajuda a montar uma reserva de emergência?', '[\"Usar ativos de alta liquidez e baixo risco\",\"Concentrar tudo em criptomoedas\",\"Escolher apenas ativos sem resgate\",\"Investir somente em ações\"]', 0),
(83, 2, 'O que é diversificação de carteira?', '[\"Distribuir recursos entre ativos e classes diferentes\",\"Comprar o mesmo ativo em várias corretoras\",\"Colocar tudo no investimento mais rentável\",\"Manter todo o dinheiro parado\"]', 0),
(84, 2, 'Se a inflação sobe e o dinheiro fica parado, o que pode ocorrer?', '[\"O poder de compra pode diminuir\",\"O saldo nominal aumenta sozinho\",\"O dinheiro passa a render automaticamente\",\"O risco desaparece\"]', 0),
(85, 2, 'O que representa a liquidez de um investimento?', '[\"A facilidade e a rapidez para transformar o ativo em dinheiro\",\"A garantia de lucro diário\",\"O tamanho da empresa emissora\",\"A quantidade de dividendos\"]', 0),
(86, 2, 'Qual é uma diferença importante entre rentabilidade nominal e real?', '[\"A real considera o efeito da inflação\",\"A nominal sempre é menor\",\"A real ignora custos e inflação\",\"Não existe diferença\"]', 0),
(87, 2, 'O que é volatilidade?', '[\"A intensidade das oscilações de preço de um ativo\",\"A certeza de receber juros\",\"O prazo de vencimento de uma conta\",\"A taxa de câmbio fixa\"]', 0),
(88, 2, 'Sobre criptomoedas, qual afirmação é mais responsável?', '[\"Podem ter alta volatilidade e exigem gestão de risco\",\"São sempre protegidas pelo FGC\",\"Não sofrem oscilações\",\"Garantem retorno positivo\"]', 0),
(89, 2, 'O que é uma stablecoin?', '[\"Um criptoativo projetado para acompanhar o valor de uma referência\",\"Uma ação de empresa estatal\",\"Um título público brasileiro\",\"Uma moeda sem qualquer risco\"]', 0),
(90, 2, 'Por que não se deve compartilhar a chave privada de uma carteira cripto?', '[\"Quem a possui pode controlar os ativos\",\"Ela serve apenas para receber promoções\",\"Ela reduz a inflação\",\"Ela garante lucro\"]', 0),
(91, 3, 'Qual é a primeira etapa antes de escolher um investimento?', '[\"Definir objetivo, prazo e tolerância a risco\",\"Seguir a indicação mais comentada\",\"Escolher sempre o ativo que mais subiu\",\"Investir sem montar orçamento\"]', 0),
(92, 3, 'Qual atitude ajuda a montar uma reserva de emergência?', '[\"Usar ativos de alta liquidez e baixo risco\",\"Concentrar tudo em criptomoedas\",\"Escolher apenas ativos sem resgate\",\"Investir somente em ações\"]', 0),
(93, 3, 'O que é diversificação de carteira?', '[\"Distribuir recursos entre ativos e classes diferentes\",\"Comprar o mesmo ativo em várias corretoras\",\"Colocar tudo no investimento mais rentável\",\"Manter todo o dinheiro parado\"]', 0),
(94, 3, 'Se a inflação sobe e o dinheiro fica parado, o que pode ocorrer?', '[\"O poder de compra pode diminuir\",\"O saldo nominal aumenta sozinho\",\"O dinheiro passa a render automaticamente\",\"O risco desaparece\"]', 0),
(95, 3, 'O que representa a liquidez de um investimento?', '[\"A facilidade e a rapidez para transformar o ativo em dinheiro\",\"A garantia de lucro diário\",\"O tamanho da empresa emissora\",\"A quantidade de dividendos\"]', 0),
(96, 3, 'Qual é uma diferença importante entre rentabilidade nominal e real?', '[\"A real considera o efeito da inflação\",\"A nominal sempre é menor\",\"A real ignora custos e inflação\",\"Não existe diferença\"]', 0),
(97, 3, 'O que é volatilidade?', '[\"A intensidade das oscilações de preço de um ativo\",\"A certeza de receber juros\",\"O prazo de vencimento de uma conta\",\"A taxa de câmbio fixa\"]', 0),
(98, 3, 'Sobre criptomoedas, qual afirmação é mais responsável?', '[\"Podem ter alta volatilidade e exigem gestão de risco\",\"São sempre protegidas pelo FGC\",\"Não sofrem oscilações\",\"Garantem retorno positivo\"]', 0),
(99, 3, 'O que é uma stablecoin?', '[\"Um criptoativo projetado para acompanhar o valor de uma referência\",\"Uma ação de empresa estatal\",\"Um título público brasileiro\",\"Uma moeda sem qualquer risco\"]', 0),
(100, 3, 'Por que não se deve compartilhar a chave privada de uma carteira cripto?', '[\"Quem a possui pode controlar os ativos\",\"Ela serve apenas para receber promoções\",\"Ela reduz a inflação\",\"Ela garante lucro\"]', 0),
(101, 4, 'Qual é a primeira etapa antes de escolher um investimento?', '[\"Definir objetivo, prazo e tolerância a risco\",\"Seguir a indicação mais comentada\",\"Escolher sempre o ativo que mais subiu\",\"Investir sem montar orçamento\"]', 0),
(102, 4, 'Qual atitude ajuda a montar uma reserva de emergência?', '[\"Usar ativos de alta liquidez e baixo risco\",\"Concentrar tudo em criptomoedas\",\"Escolher apenas ativos sem resgate\",\"Investir somente em ações\"]', 0),
(103, 4, 'O que é diversificação de carteira?', '[\"Distribuir recursos entre ativos e classes diferentes\",\"Comprar o mesmo ativo em várias corretoras\",\"Colocar tudo no investimento mais rentável\",\"Manter todo o dinheiro parado\"]', 0),
(104, 4, 'Se a inflação sobe e o dinheiro fica parado, o que pode ocorrer?', '[\"O poder de compra pode diminuir\",\"O saldo nominal aumenta sozinho\",\"O dinheiro passa a render automaticamente\",\"O risco desaparece\"]', 0),
(105, 4, 'O que representa a liquidez de um investimento?', '[\"A facilidade e a rapidez para transformar o ativo em dinheiro\",\"A garantia de lucro diário\",\"O tamanho da empresa emissora\",\"A quantidade de dividendos\"]', 0),
(106, 4, 'Qual é uma diferença importante entre rentabilidade nominal e real?', '[\"A real considera o efeito da inflação\",\"A nominal sempre é menor\",\"A real ignora custos e inflação\",\"Não existe diferença\"]', 0),
(107, 4, 'O que é volatilidade?', '[\"A intensidade das oscilações de preço de um ativo\",\"A certeza de receber juros\",\"O prazo de vencimento de uma conta\",\"A taxa de câmbio fixa\"]', 0),
(108, 4, 'Sobre criptomoedas, qual afirmação é mais responsável?', '[\"Podem ter alta volatilidade e exigem gestão de risco\",\"São sempre protegidas pelo FGC\",\"Não sofrem oscilações\",\"Garantem retorno positivo\"]', 0),
(109, 4, 'O que é uma stablecoin?', '[\"Um criptoativo projetado para acompanhar o valor de uma referência\",\"Uma ação de empresa estatal\",\"Um título público brasileiro\",\"Uma moeda sem qualquer risco\"]', 0),
(110, 4, 'Por que não se deve compartilhar a chave privada de uma carteira cripto?', '[\"Quem a possui pode controlar os ativos\",\"Ela serve apenas para receber promoções\",\"Ela reduz a inflação\",\"Ela garante lucro\"]', 0),
(111, 5, 'Qual é a primeira etapa antes de escolher um investimento?', '[\"Definir objetivo, prazo e tolerância a risco\",\"Seguir a indicação mais comentada\",\"Escolher sempre o ativo que mais subiu\",\"Investir sem montar orçamento\"]', 0),
(112, 5, 'Qual atitude ajuda a montar uma reserva de emergência?', '[\"Usar ativos de alta liquidez e baixo risco\",\"Concentrar tudo em criptomoedas\",\"Escolher apenas ativos sem resgate\",\"Investir somente em ações\"]', 0),
(113, 5, 'O que é diversificação de carteira?', '[\"Distribuir recursos entre ativos e classes diferentes\",\"Comprar o mesmo ativo em várias corretoras\",\"Colocar tudo no investimento mais rentável\",\"Manter todo o dinheiro parado\"]', 0),
(114, 5, 'Se a inflação sobe e o dinheiro fica parado, o que pode ocorrer?', '[\"O poder de compra pode diminuir\",\"O saldo nominal aumenta sozinho\",\"O dinheiro passa a render automaticamente\",\"O risco desaparece\"]', 0),
(115, 5, 'O que representa a liquidez de um investimento?', '[\"A facilidade e a rapidez para transformar o ativo em dinheiro\",\"A garantia de lucro diário\",\"O tamanho da empresa emissora\",\"A quantidade de dividendos\"]', 0),
(116, 5, 'Qual é uma diferença importante entre rentabilidade nominal e real?', '[\"A real considera o efeito da inflação\",\"A nominal sempre é menor\",\"A real ignora custos e inflação\",\"Não existe diferença\"]', 0),
(117, 5, 'O que é volatilidade?', '[\"A intensidade das oscilações de preço de um ativo\",\"A certeza de receber juros\",\"O prazo de vencimento de uma conta\",\"A taxa de câmbio fixa\"]', 0),
(118, 5, 'Sobre criptomoedas, qual afirmação é mais responsável?', '[\"Podem ter alta volatilidade e exigem gestão de risco\",\"São sempre protegidas pelo FGC\",\"Não sofrem oscilações\",\"Garantem retorno positivo\"]', 0),
(119, 5, 'O que é uma stablecoin?', '[\"Um criptoativo projetado para acompanhar o valor de uma referência\",\"Uma ação de empresa estatal\",\"Um título público brasileiro\",\"Uma moeda sem qualquer risco\"]', 0),
(120, 5, 'Por que não se deve compartilhar a chave privada de uma carteira cripto?', '[\"Quem a possui pode controlar os ativos\",\"Ela serve apenas para receber promoções\",\"Ela reduz a inflação\",\"Ela garante lucro\"]', 0),
(121, 6, 'Qual é a primeira etapa antes de escolher um investimento?', '[\"Definir objetivo, prazo e tolerância a risco\",\"Seguir a indicação mais comentada\",\"Escolher sempre o ativo que mais subiu\",\"Investir sem montar orçamento\"]', 0),
(122, 6, 'Qual atitude ajuda a montar uma reserva de emergência?', '[\"Usar ativos de alta liquidez e baixo risco\",\"Concentrar tudo em criptomoedas\",\"Escolher apenas ativos sem resgate\",\"Investir somente em ações\"]', 0),
(123, 6, 'O que é diversificação de carteira?', '[\"Distribuir recursos entre ativos e classes diferentes\",\"Comprar o mesmo ativo em várias corretoras\",\"Colocar tudo no investimento mais rentável\",\"Manter todo o dinheiro parado\"]', 0),
(124, 6, 'Se a inflação sobe e o dinheiro fica parado, o que pode ocorrer?', '[\"O poder de compra pode diminuir\",\"O saldo nominal aumenta sozinho\",\"O dinheiro passa a render automaticamente\",\"O risco desaparece\"]', 0),
(125, 6, 'O que representa a liquidez de um investimento?', '[\"A facilidade e a rapidez para transformar o ativo em dinheiro\",\"A garantia de lucro diário\",\"O tamanho da empresa emissora\",\"A quantidade de dividendos\"]', 0),
(126, 6, 'Qual é uma diferença importante entre rentabilidade nominal e real?', '[\"A real considera o efeito da inflação\",\"A nominal sempre é menor\",\"A real ignora custos e inflação\",\"Não existe diferença\"]', 0),
(127, 6, 'O que é volatilidade?', '[\"A intensidade das oscilações de preço de um ativo\",\"A certeza de receber juros\",\"O prazo de vencimento de uma conta\",\"A taxa de câmbio fixa\"]', 0),
(128, 6, 'Sobre criptomoedas, qual afirmação é mais responsável?', '[\"Podem ter alta volatilidade e exigem gestão de risco\",\"São sempre protegidas pelo FGC\",\"Não sofrem oscilações\",\"Garantem retorno positivo\"]', 0),
(129, 6, 'O que é uma stablecoin?', '[\"Um criptoativo projetado para acompanhar o valor de uma referência\",\"Uma ação de empresa estatal\",\"Um título público brasileiro\",\"Uma moeda sem qualquer risco\"]', 0),
(130, 6, 'Por que não se deve compartilhar a chave privada de uma carteira cripto?', '[\"Quem a possui pode controlar os ativos\",\"Ela serve apenas para receber promoções\",\"Ela reduz a inflação\",\"Ela garante lucro\"]', 0),
(131, 7, 'Qual é a primeira etapa antes de escolher um investimento?', '[\"Definir objetivo, prazo e tolerância a risco\",\"Seguir a indicação mais comentada\",\"Escolher sempre o ativo que mais subiu\",\"Investir sem montar orçamento\"]', 0),
(132, 7, 'Qual atitude ajuda a montar uma reserva de emergência?', '[\"Usar ativos de alta liquidez e baixo risco\",\"Concentrar tudo em criptomoedas\",\"Escolher apenas ativos sem resgate\",\"Investir somente em ações\"]', 0),
(133, 7, 'O que é diversificação de carteira?', '[\"Distribuir recursos entre ativos e classes diferentes\",\"Comprar o mesmo ativo em várias corretoras\",\"Colocar tudo no investimento mais rentável\",\"Manter todo o dinheiro parado\"]', 0),
(134, 7, 'Se a inflação sobe e o dinheiro fica parado, o que pode ocorrer?', '[\"O poder de compra pode diminuir\",\"O saldo nominal aumenta sozinho\",\"O dinheiro passa a render automaticamente\",\"O risco desaparece\"]', 0),
(135, 7, 'O que representa a liquidez de um investimento?', '[\"A facilidade e a rapidez para transformar o ativo em dinheiro\",\"A garantia de lucro diário\",\"O tamanho da empresa emissora\",\"A quantidade de dividendos\"]', 0),
(136, 7, 'Qual é uma diferença importante entre rentabilidade nominal e real?', '[\"A real considera o efeito da inflação\",\"A nominal sempre é menor\",\"A real ignora custos e inflação\",\"Não existe diferença\"]', 0),
(137, 7, 'O que é volatilidade?', '[\"A intensidade das oscilações de preço de um ativo\",\"A certeza de receber juros\",\"O prazo de vencimento de uma conta\",\"A taxa de câmbio fixa\"]', 0),
(138, 7, 'Sobre criptomoedas, qual afirmação é mais responsável?', '[\"Podem ter alta volatilidade e exigem gestão de risco\",\"São sempre protegidas pelo FGC\",\"Não sofrem oscilações\",\"Garantem retorno positivo\"]', 0),
(139, 7, 'O que é uma stablecoin?', '[\"Um criptoativo projetado para acompanhar o valor de uma referência\",\"Uma ação de empresa estatal\",\"Um título público brasileiro\",\"Uma moeda sem qualquer risco\"]', 0),
(140, 7, 'Por que não se deve compartilhar a chave privada de uma carteira cripto?', '[\"Quem a possui pode controlar os ativos\",\"Ela serve apenas para receber promoções\",\"Ela reduz a inflação\",\"Ela garante lucro\"]', 0),
(141, 8, 'Qual é a primeira etapa antes de escolher um investimento?', '[\"Definir objetivo, prazo e tolerância a risco\",\"Seguir a indicação mais comentada\",\"Escolher sempre o ativo que mais subiu\",\"Investir sem montar orçamento\"]', 0),
(142, 8, 'Qual atitude ajuda a montar uma reserva de emergência?', '[\"Usar ativos de alta liquidez e baixo risco\",\"Concentrar tudo em criptomoedas\",\"Escolher apenas ativos sem resgate\",\"Investir somente em ações\"]', 0),
(143, 8, 'O que é diversificação de carteira?', '[\"Distribuir recursos entre ativos e classes diferentes\",\"Comprar o mesmo ativo em várias corretoras\",\"Colocar tudo no investimento mais rentável\",\"Manter todo o dinheiro parado\"]', 0),
(144, 8, 'Se a inflação sobe e o dinheiro fica parado, o que pode ocorrer?', '[\"O poder de compra pode diminuir\",\"O saldo nominal aumenta sozinho\",\"O dinheiro passa a render automaticamente\",\"O risco desaparece\"]', 0),
(145, 8, 'O que representa a liquidez de um investimento?', '[\"A facilidade e a rapidez para transformar o ativo em dinheiro\",\"A garantia de lucro diário\",\"O tamanho da empresa emissora\",\"A quantidade de dividendos\"]', 0),
(146, 8, 'Qual é uma diferença importante entre rentabilidade nominal e real?', '[\"A real considera o efeito da inflação\",\"A nominal sempre é menor\",\"A real ignora custos e inflação\",\"Não existe diferença\"]', 0),
(147, 8, 'O que é volatilidade?', '[\"A intensidade das oscilações de preço de um ativo\",\"A certeza de receber juros\",\"O prazo de vencimento de uma conta\",\"A taxa de câmbio fixa\"]', 0),
(148, 8, 'Sobre criptomoedas, qual afirmação é mais responsável?', '[\"Podem ter alta volatilidade e exigem gestão de risco\",\"São sempre protegidas pelo FGC\",\"Não sofrem oscilações\",\"Garantem retorno positivo\"]', 0),
(149, 8, 'O que é uma stablecoin?', '[\"Um criptoativo projetado para acompanhar o valor de uma referência\",\"Uma ação de empresa estatal\",\"Um título público brasileiro\",\"Uma moeda sem qualquer risco\"]', 0),
(150, 8, 'Por que não se deve compartilhar a chave privada de uma carteira cripto?', '[\"Quem a possui pode controlar os ativos\",\"Ela serve apenas para receber promoções\",\"Ela reduz a inflação\",\"Ela garante lucro\"]', 0),
(151, 9, 'Qual é a primeira etapa antes de escolher um investimento?', '[\"Definir objetivo, prazo e tolerância a risco\",\"Seguir a indicação mais comentada\",\"Escolher sempre o ativo que mais subiu\",\"Investir sem montar orçamento\"]', 0),
(152, 9, 'Qual atitude ajuda a montar uma reserva de emergência?', '[\"Usar ativos de alta liquidez e baixo risco\",\"Concentrar tudo em criptomoedas\",\"Escolher apenas ativos sem resgate\",\"Investir somente em ações\"]', 0),
(153, 9, 'O que é diversificação de carteira?', '[\"Distribuir recursos entre ativos e classes diferentes\",\"Comprar o mesmo ativo em várias corretoras\",\"Colocar tudo no investimento mais rentável\",\"Manter todo o dinheiro parado\"]', 0),
(154, 9, 'Se a inflação sobe e o dinheiro fica parado, o que pode ocorrer?', '[\"O poder de compra pode diminuir\",\"O saldo nominal aumenta sozinho\",\"O dinheiro passa a render automaticamente\",\"O risco desaparece\"]', 0),
(155, 9, 'O que representa a liquidez de um investimento?', '[\"A facilidade e a rapidez para transformar o ativo em dinheiro\",\"A garantia de lucro diário\",\"O tamanho da empresa emissora\",\"A quantidade de dividendos\"]', 0),
(156, 9, 'Qual é uma diferença importante entre rentabilidade nominal e real?', '[\"A real considera o efeito da inflação\",\"A nominal sempre é menor\",\"A real ignora custos e inflação\",\"Não existe diferença\"]', 0),
(157, 9, 'O que é volatilidade?', '[\"A intensidade das oscilações de preço de um ativo\",\"A certeza de receber juros\",\"O prazo de vencimento de uma conta\",\"A taxa de câmbio fixa\"]', 0),
(158, 9, 'Sobre criptomoedas, qual afirmação é mais responsável?', '[\"Podem ter alta volatilidade e exigem gestão de risco\",\"São sempre protegidas pelo FGC\",\"Não sofrem oscilações\",\"Garantem retorno positivo\"]', 0),
(159, 9, 'O que é uma stablecoin?', '[\"Um criptoativo projetado para acompanhar o valor de uma referência\",\"Uma ação de empresa estatal\",\"Um título público brasileiro\",\"Uma moeda sem qualquer risco\"]', 0),
(160, 9, 'Por que não se deve compartilhar a chave privada de uma carteira cripto?', '[\"Quem a possui pode controlar os ativos\",\"Ela serve apenas para receber promoções\",\"Ela reduz a inflação\",\"Ela garante lucro\"]', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `arena_lessons`
--

CREATE TABLE `arena_lessons` (
  `id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `title` varchar(180) NOT NULL,
  `duration` varchar(20) NOT NULL,
  `xp` int(11) NOT NULL DEFAULT 10,
  `position` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `arena_lessons`
--

INSERT INTO `arena_lessons` (`id`, `module_id`, `title`, `duration`, `xp`, `position`) VALUES
(1, 1, 'Introdução ao Mundo dos Investimentos', '12:30', 10, 1),
(2, 1, 'Tipos de Ativos Financeiros', '18:45', 10, 2),
(3, 1, 'Risco e Retorno', '22:10', 10, 3),
(4, 2, 'Montando sua Carteira Inicial', '15:20', 10, 1),
(5, 2, 'Gráficos e Tendências', '14:50', 10, 2),
(6, 2, 'Indicadores Técnicos', '20:10', 10, 3),
(7, 3, 'Suporte e Resistência', '17:30', 10, 1),
(8, 3, 'Padrões de Candlestick', '25:00', 10, 2),
(9, 3, 'O que é Valuation?', '16:40', 10, 3);

-- --------------------------------------------------------

--
-- Estrutura para tabela `arena_modules`
--

CREATE TABLE `arena_modules` (
  `id` int(11) NOT NULL,
  `title` varchar(180) NOT NULL,
  `subtitle` varchar(255) NOT NULL,
  `position` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `arena_modules`
--

INSERT INTO `arena_modules` (`id`, `title`, `subtitle`, `position`) VALUES
(1, 'Fundamentos de Investimentos', 'Construa uma base sólida para tomar decisões financeiras.', 1),
(2, 'Análise Técnica', 'Leia riscos, tendências e movimentos do mercado com responsabilidade.', 2),
(3, 'Valuation e Carteira', 'Aprenda a comparar ativos e montar uma carteira consciente.', 3);

-- --------------------------------------------------------

--
-- Estrutura para tabela `arena_progress`
--

CREATE TABLE `arena_progress` (
  `arena_user_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `kind` enum('lesson','exercise') NOT NULL,
  `completed_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `arena_users`
--

CREATE TABLE `arena_users` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL DEFAULT 'Jogador',
  `xp` int(11) NOT NULL DEFAULT 0,
  `coins` int(11) NOT NULL DEFAULT 0,
  `streak` int(11) NOT NULL DEFAULT 0,
  `best_streak` int(11) NOT NULL DEFAULT 0,
  `last_visit` date DEFAULT NULL,
  `last_checkin` date DEFAULT NULL,
  `freeze_count` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `arena_users`
--

INSERT INTO `arena_users` (`id`, `user_id`, `name`, `xp`, `coins`, `streak`, `best_streak`, `last_visit`, `last_checkin`, `freeze_count`, `created_at`) VALUES
(1, 12, 'bryan soares', 0, 0, 0, 0, NULL, NULL, 0, '2026-10-07 16:00:11');

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes_asaas`
--

CREATE TABLE `clientes_asaas` (
  `usuario_id` int(11) NOT NULL,
  `asaas_customer_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `clientes_asaas`
--

INSERT INTO `clientes_asaas` (`usuario_id`, `asaas_customer_id`) VALUES
(1, 'cus_000009378516');

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

-- --------------------------------------------------------

--
-- Estrutura para tabela `open_finance_transacoes`
--

CREATE TABLE `open_finance_transacoes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `transacao_id` varchar(255) NOT NULL,
  `account_id` varchar(255) DEFAULT NULL,
  `conta_nome` varchar(255) DEFAULT NULL,
  `tipo` enum('entrada','saida') NOT NULL,
  `status` varchar(30) DEFAULT 'POSTED',
  `valor` decimal(12,2) NOT NULL DEFAULT 0.00,
  `descricao` varchar(255) DEFAULT NULL,
  `categoria` varchar(100) NOT NULL DEFAULT 'Outros',
  `categoria_sugerida` varchar(100) NOT NULL DEFAULT 'Outros',
  `categoria_origem` enum('automatica','manual') NOT NULL DEFAULT 'automatica',
  `moeda` varchar(12) DEFAULT 'BRL',
  `data_transacao` datetime NOT NULL,
  `payload` longtext DEFAULT NULL,
  `sincronizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `plano` varchar(20) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pendente',
  `asaas_payment_id` varchar(50) DEFAULT NULL,
  `invoice_url` varchar(255) DEFAULT NULL,
  `pago_em` datetime DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`id`, `usuario_id`, `plano`, `valor`, `status`, `asaas_payment_id`, `invoice_url`, `pago_em`, `criado_em`) VALUES
(1, 1, 'mensal', 50.00, 'pendente', 'pay_i1n9tk6mrqt1y2zi', 'https://sandbox.asaas.com/i/i1n9tk6mrqt1y2zi', NULL, '2026-10-07 19:52:46'),
(2, 1, 'mensal', 50.00, 'pendente', 'pay_3twgccl0j4oqnqbw', 'https://sandbox.asaas.com/i/3twgccl0j4oqnqbw', NULL, '2026-10-07 19:59:12');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `estoque` int(11) DEFAULT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `status` enum('ativo','inativo') NOT NULL DEFAULT 'ativo',
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tentativas_cadastro`
--

CREATE TABLE `tentativas_cadastro` (
  `ip` varchar(45) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 0,
  `janela_inicio` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tentativas_cadastro`
--

INSERT INTO `tentativas_cadastro` (`ip`, `quantidade`, `janela_inicio`) VALUES
('::1', 1, '2026-10-07 15:53:20');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tentativas_login`
--

CREATE TABLE `tentativas_login` (
  `email` varchar(190) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `tentativas` int(11) NOT NULL DEFAULT 0,
  `ultima_tentativa` datetime DEFAULT NULL,
  `bloqueado_ate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `itemid` varchar(255) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(190) NOT NULL,
  `oauth_uid` varchar(255) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `telefone` varchar(25) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `provedor` varchar(20) DEFAULT 'local',
  `tipo` enum('usuario','admin') NOT NULL DEFAULT 'usuario',
  `tema` varchar(22) DEFAULT 'sistema',
  `idioma` varchar(22) DEFAULT 'pt-br',
  `moeda` varchar(22) DEFAULT 'BRL',
  `banner` varchar(255) DEFAULT NULL,
  `patente` varchar(30) DEFAULT NULL,
  `xp` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `itemid`, `nome`, `email`, `oauth_uid`, `foto`, `senha`, `telefone`, `data_criacao`, `provedor`, `tipo`, `tema`, `idioma`, `moeda`, `banner`, `patente`, `xp`) VALUES
(12, '', 'bryan soares', 'soares@gmail.com', NULL, NULL, '$2y$10$52.JlR4cNi1YO5fCsH6a4ufGc4H/TGDyY65wA94hOqedJjRvMlT9W', '', '2026-10-07 18:53:20', 'local', 'usuario', 'sistema', 'pt-br', 'BRL', NULL, NULL, 0);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `arena_active_items`
--
ALTER TABLE `arena_active_items`
  ADD PRIMARY KEY (`arena_user_id`,`item_id`),
  ADD KEY `idx_arena_items_expiration` (`expires_at`);

--
-- Índices de tabela `arena_activities`
--
ALTER TABLE `arena_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_arena_activities_user_date` (`arena_user_id`,`created_at`);

--
-- Índices de tabela `arena_comments`
--
ALTER TABLE `arena_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_arena_comments_lesson` (`lesson_id`),
  ADD KEY `fk_arena_comments_user` (`arena_user_id`);

--
-- Índices de tabela `arena_comment_votes`
--
ALTER TABLE `arena_comment_votes`
  ADD PRIMARY KEY (`comment_id`,`arena_user_id`),
  ADD KEY `fk_arena_votes_user` (`arena_user_id`);

--
-- Índices de tabela `arena_exercises`
--
ALTER TABLE `arena_exercises`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_arena_exercise_question` (`lesson_id`,`question`(191)),
  ADD KEY `idx_arena_exercises_lesson` (`lesson_id`);

--
-- Índices de tabela `arena_lessons`
--
ALTER TABLE `arena_lessons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_arena_lessons_position` (`module_id`,`position`),
  ADD KEY `idx_arena_lessons_module` (`module_id`);

--
-- Índices de tabela `arena_modules`
--
ALTER TABLE `arena_modules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_arena_modules_position` (`position`);

--
-- Índices de tabela `arena_progress`
--
ALTER TABLE `arena_progress`
  ADD PRIMARY KEY (`arena_user_id`,`lesson_id`,`kind`),
  ADD KEY `idx_arena_progress_lesson` (`lesson_id`);

--
-- Índices de tabela `arena_users`
--
ALTER TABLE `arena_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_arena_user_ptcc` (`user_id`),
  ADD KEY `idx_arena_users_user` (`user_id`);

--
-- Índices de tabela `clientes_asaas`
--
ALTER TABLE `clientes_asaas`
  ADD PRIMARY KEY (`usuario_id`);

--
-- Índices de tabela `movimentacoes`
--
ALTER TABLE `movimentacoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `open_finance_transacoes`
--
ALTER TABLE `open_finance_transacoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_open_finance_usuario_transacao` (`usuario_id`,`transacao_id`),
  ADD KEY `idx_open_finance_usuario_data` (`usuario_id`,`data_transacao`),
  ADD KEY `idx_open_finance_usuario_categoria` (`usuario_id`,`categoria`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asaas_payment_id` (`asaas_payment_id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tentativas_cadastro`
--
ALTER TABLE `tentativas_cadastro`
  ADD PRIMARY KEY (`ip`);

--
-- Índices de tabela `tentativas_login`
--
ALTER TABLE `tentativas_login`
  ADD PRIMARY KEY (`email`,`ip`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `uq_usuarios_oauth_uid` (`oauth_uid`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `arena_activities`
--
ALTER TABLE `arena_activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `arena_comments`
--
ALTER TABLE `arena_comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `arena_exercises`
--
ALTER TABLE `arena_exercises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

--
-- AUTO_INCREMENT de tabela `arena_lessons`
--
ALTER TABLE `arena_lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `arena_modules`
--
ALTER TABLE `arena_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `arena_users`
--
ALTER TABLE `arena_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `movimentacoes`
--
ALTER TABLE `movimentacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `open_finance_transacoes`
--
ALTER TABLE `open_finance_transacoes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `arena_active_items`
--
ALTER TABLE `arena_active_items`
  ADD CONSTRAINT `fk_arena_items_user` FOREIGN KEY (`arena_user_id`) REFERENCES `arena_users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `arena_activities`
--
ALTER TABLE `arena_activities`
  ADD CONSTRAINT `fk_arena_activities_user` FOREIGN KEY (`arena_user_id`) REFERENCES `arena_users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `arena_comments`
--
ALTER TABLE `arena_comments`
  ADD CONSTRAINT `fk_arena_comments_lesson` FOREIGN KEY (`lesson_id`) REFERENCES `arena_lessons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_arena_comments_user` FOREIGN KEY (`arena_user_id`) REFERENCES `arena_users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `arena_comment_votes`
--
ALTER TABLE `arena_comment_votes`
  ADD CONSTRAINT `fk_arena_votes_comment` FOREIGN KEY (`comment_id`) REFERENCES `arena_comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_arena_votes_user` FOREIGN KEY (`arena_user_id`) REFERENCES `arena_users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `arena_exercises`
--
ALTER TABLE `arena_exercises`
  ADD CONSTRAINT `fk_arena_exercises_lesson` FOREIGN KEY (`lesson_id`) REFERENCES `arena_lessons` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `arena_lessons`
--
ALTER TABLE `arena_lessons`
  ADD CONSTRAINT `fk_arena_lessons_module` FOREIGN KEY (`module_id`) REFERENCES `arena_modules` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `arena_progress`
--
ALTER TABLE `arena_progress`
  ADD CONSTRAINT `fk_arena_progress_lesson` FOREIGN KEY (`lesson_id`) REFERENCES `arena_lessons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_arena_progress_user` FOREIGN KEY (`arena_user_id`) REFERENCES `arena_users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `arena_users`
--
ALTER TABLE `arena_users`
  ADD CONSTRAINT `fk_arena_users_ptcc` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `open_finance_transacoes`
--
ALTER TABLE `open_finance_transacoes`
  ADD CONSTRAINT `fk_open_finance_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
