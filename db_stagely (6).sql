-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 14-Jun-2026 às 22:58
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
-- Banco de dados: `db_stagely`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `alunos`
--

CREATE TABLE `alunos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `bilhete_identidade` char(14) DEFAULT NULL,
  `numero_processo` int(11) NOT NULL,
  `genero` enum('masculino','feminino') DEFAULT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `id_encarregado` int(11) DEFAULT NULL,
  `id_status_estagio` int(11) DEFAULT 6,
  `id_ano_letivo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `alunos`
--

INSERT INTO `alunos` (`id`, `nome`, `data_nascimento`, `bilhete_identidade`, `numero_processo`, `genero`, `id_curso`, `id_encarregado`, `id_status_estagio`, `id_ano_letivo`) VALUES
(7, 'Aomine Daiki', '2007-03-31', '008433599LA000', 785, 'masculino', 1, 1, 3, 2),
(8, 'Sigmund Freud', '2007-02-21', '007433599LA000', 982, 'masculino', 1, 1, 2, 1),
(9, 'Laura Figueira', '2008-01-23', '005433599LA000', 103, 'feminino', 2, 1, 6, 1),
(10, 'John Dalton', '2007-08-17', '001433599LA000', 93, 'masculino', 2, 1, 3, 1),
(11, 'Sílvio Sebastião', '2007-01-05', '006433599LA000', 109, 'masculino', 3, 1, 2, 1),
(12, 'Marisa Campos', '2006-08-18', '002433599LA000', 942, 'feminino', 3, 1, 2, 1),
(13, 'Miriam Gomes', '2007-04-05', '000433599LA000', 938, 'masculino', 4, 1, 3, 2),
(15, 'Maxwell Viveira', '2007-02-21', '008033599LA000', 987, 'masculino', 5, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `ano_letivo`
--

CREATE TABLE `ano_letivo` (
  `id` int(11) NOT NULL,
  `ano` char(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `ano_letivo`
--

INSERT INTO `ano_letivo` (`id`, `ano`) VALUES
(1, '2025/2026'),
(2, '2027/2028');

-- --------------------------------------------------------

--
-- Estrutura da tabela `areas_empresa`
--

CREATE TABLE `areas_empresa` (
  `id_area_estagio` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `areas_empresa`
--

INSERT INTO `areas_empresa` (`id_area_estagio`, `id_empresa`) VALUES
(95, 93),
(97, 97),
(98, 96),
(100, 97),
(101, 98),
(102, 97);

-- --------------------------------------------------------

--
-- Estrutura da tabela `areas_estagio`
--

CREATE TABLE `areas_estagio` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `id_curso` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `areas_estagio`
--

INSERT INTO `areas_estagio` (`id`, `nome`, `id_curso`) VALUES
(95, 'Programação', 1),
(96, 'Mobile development', 1),
(97, 'Controle Ambiental', 4),
(98, 'Automação', 3),
(99, 'Projeção', 2),
(100, 'Projecção', 2),
(101, 'Circuitos elétricos', 5),
(102, 'Game Dev', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `avaliacoes`
--

CREATE TABLE `avaliacoes` (
  `id` int(11) NOT NULL,
  `bin` longblob DEFAULT NULL,
  `caminho_arquivo` varchar(255) DEFAULT NULL,
  `id_aluno` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `avaliacoes`
--

INSERT INTO `avaliacoes` (`id`, `bin`, `caminho_arquivo`, `id_aluno`) VALUES
(47, 0x433a5c78616d70705c746d705c706870343842422e746d70, '/stagely/backend/private/files/evaluations/Desenhador Projectista (DP)/John Dalton.pdf', 10),
(48, 0x433a5c78616d70705c746d705c706870364545322e746d70, '/stagely/backend/private/files/evaluations/Bioquímica (BQ)/Miriam Gomes.pdf', 13),
(49, 0x433a5c78616d70705c746d705c706870393846372e746d70, '/stagely/backend/private/files/evaluations/Técnico de Informática (TI)/Aomine Daiki.pdf', 7);

-- --------------------------------------------------------

--
-- Estrutura da tabela `contactos_encarregados`
--

CREATE TABLE `contactos_encarregados` (
  `id` int(11) NOT NULL,
  `id_encarregado` int(11) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `telefone` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `contactos_encarregados`
--

INSERT INTO `contactos_encarregados` (`id`, `id_encarregado`, `email`, `telefone`) VALUES
(1, 1, 'tyrese@gmail.com', 935555500);

-- --------------------------------------------------------

--
-- Estrutura da tabela `cursos`
--

CREATE TABLE `cursos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `cursos`
--

INSERT INTO `cursos` (`id`, `nome`) VALUES
(1, 'Técnico de Informática (TI)'),
(2, 'Desenhador Projectista (DP)'),
(3, 'Máquinas e Motores (MM)'),
(4, 'Bioquímica (BQ)'),
(5, 'Energia e Instalações Elétricas (IE)');

-- --------------------------------------------------------

--
-- Estrutura da tabela `datas_estagio`
--

CREATE TABLE `datas_estagio` (
  `id` int(11) NOT NULL,
  `inicio` date DEFAULT NULL,
  `fim` date DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `id_area_estagio` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `datas_estagio`
--

INSERT INTO `datas_estagio` (`id`, `inicio`, `fim`, `id_empresa`, `id_curso`, `id_area_estagio`) VALUES
(104, '2026-05-20', '2026-05-24', 93, 1, 95),
(106, '2026-04-22', '2026-05-22', 97, 4, 97),
(107, '2026-05-10', '2026-12-21', 96, 3, 98),
(108, '2026-05-12', '2026-05-23', 97, 2, 100),
(109, '2026-06-10', '2027-06-10', 98, 5, 101),
(110, '2026-05-25', '2026-07-14', 97, 1, 102);

-- --------------------------------------------------------

--
-- Estrutura da tabela `dias_estagio`
--

CREATE TABLE `dias_estagio` (
  `id` int(11) NOT NULL,
  `dia` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `dias_estagio`
--

INSERT INTO `dias_estagio` (`id`, `dia`) VALUES
(1, 'Segunda-feira'),
(2, 'Terça-feira'),
(3, 'Quarta-feira'),
(4, 'Quinta-feira'),
(5, 'Sexta-feira'),
(6, 'Sábado');

-- --------------------------------------------------------

--
-- Estrutura da tabela `dias_horario`
--

CREATE TABLE `dias_horario` (
  `id_horario` int(11) NOT NULL,
  `id_dia_estagio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `dias_horario`
--

INSERT INTO `dias_horario` (`id_horario`, `id_dia_estagio`) VALUES
(125, 1),
(125, 3),
(127, 1),
(127, 4),
(127, 6),
(128, 1),
(128, 3),
(128, 5),
(129, 1),
(129, 3),
(130, 3),
(130, 5),
(131, 2),
(131, 4);

-- --------------------------------------------------------

--
-- Estrutura da tabela `empresas`
--

CREATE TABLE `empresas` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `id_localizacao` int(11) DEFAULT NULL,
  `id_status_empresa` int(11) DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `empresas`
--

INSERT INTO `empresas` (`id`, `nome`, `email`, `id_localizacao`, `id_status_empresa`) VALUES
(93, 'TIS', 'tis@gmail.com', 94, 5),
(94, 'Tech Waves', 'techwaves@hotmail.com', 95, 5),
(95, 'Brafrikon Engenharia', 'brafrikon@gmail.com', 96, 5),
(96, 'APROT Engenharia', 'aprot@gmail.com', 97, 4),
(97, 'Milton', 'milton@ana.com', 98, 4),
(98, 'Luminoser Angola', 'luminoser@gmail.com', 99, 5);

-- --------------------------------------------------------

--
-- Estrutura da tabela `encarregados`
--

CREATE TABLE `encarregados` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `encarregados`
--

INSERT INTO `encarregados` (`id`, `nome`) VALUES
(1, 'Tyrese');

-- --------------------------------------------------------

--
-- Estrutura da tabela `escola`
--

CREATE TABLE `escola` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `escola`
--

INSERT INTO `escola` (`id`, `nome`, `email`) VALUES
(1, 'IPDDF', 'ipddf@gmail.com');

-- --------------------------------------------------------

--
-- Estrutura da tabela `estagiarios`
--

CREATE TABLE `estagiarios` (
  `id` int(11) NOT NULL,
  `id_aluno` int(11) DEFAULT NULL,
  `id_area_estagio` int(11) DEFAULT NULL,
  `id_empresa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `estagiarios`
--

INSERT INTO `estagiarios` (`id`, `id_aluno`, `id_area_estagio`, `id_empresa`) VALUES
(165, 7, 95, 93),
(172, 8, 102, 97),
(170, 10, 100, 97),
(168, 11, 98, 96),
(169, 12, 98, 96),
(167, 13, 97, 97),
(171, 15, 101, 98);

-- --------------------------------------------------------

--
-- Estrutura da tabela `horarios_estagio`
--

CREATE TABLE `horarios_estagio` (
  `id` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `entrada` time DEFAULT NULL,
  `saida` time DEFAULT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `id_area_estagio` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `horarios_estagio`
--

INSERT INTO `horarios_estagio` (`id`, `id_empresa`, `entrada`, `saida`, `id_curso`, `id_area_estagio`) VALUES
(125, 93, '08:00:00', '14:00:00', 1, 95),
(127, 97, '10:00:00', '15:00:00', 4, 97),
(128, 96, '12:00:00', '15:00:00', 3, 98),
(129, 97, '12:00:00', '15:00:00', 2, 100),
(130, 98, '10:00:00', '15:00:00', 5, 101),
(131, 97, '12:00:00', '15:00:00', 1, 102);

-- --------------------------------------------------------

--
-- Estrutura da tabela `localizacao`
--

CREATE TABLE `localizacao` (
  `id` int(11) NOT NULL,
  `rua` varchar(100) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `localizacao`
--

INSERT INTO `localizacao` (`id`, `rua`, `bairro`) VALUES
(94, 'Via A1, Edifício Cabinda', 'Talatona'),
(95, 'Rua da Vizinhança Nº8', 'Maianga'),
(96, 'Fernão Mendes Pinto', 'Alvalade'),
(97, 'S/N', 'Zona Industrial'),
(98, 'Estaleiro', 'Cacuaco'),
(99, 'Via Expresso', 'Zona Verde 3'),
(100, 'vygbhn', 'oniybu'),
(101, '21 de Janeiro', 'Talatona');

-- --------------------------------------------------------

--
-- Estrutura da tabela `ramos_atuacao`
--

CREATE TABLE `ramos_atuacao` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) DEFAULT NULL,
  `id_curso` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `ramos_atuacao`
--

INSERT INTO `ramos_atuacao` (`id`, `nome`, `id_curso`) VALUES
(1, 'Informática', 1),
(2, 'Arquitetura', 2),
(3, 'Bioquímica', 4),
(4, 'Mecânica', 3),
(5, 'Eletricidade', 5);

-- --------------------------------------------------------

--
-- Estrutura da tabela `ramos_empresa`
--

CREATE TABLE `ramos_empresa` (
  `id_empresa` int(11) NOT NULL,
  `id_ramo_atuacao` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `ramos_empresa`
--

INSERT INTO `ramos_empresa` (`id_empresa`, `id_ramo_atuacao`) VALUES
(93, 1),
(94, 1),
(95, 2),
(96, 4),
(97, 1),
(97, 2),
(97, 3),
(98, 5);

-- --------------------------------------------------------

--
-- Estrutura da tabela `status_estagio`
--

CREATE TABLE `status_estagio` (
  `id` int(11) NOT NULL,
  `status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `status_estagio`
--

INSERT INTO `status_estagio` (`id`, `status`) VALUES
(1, 'Pendente'),
(2, 'Em execução'),
(3, 'Concluído'),
(4, 'Activo'),
(5, 'Inactivo'),
(6, 'Indisponível');

-- --------------------------------------------------------

--
-- Estrutura da tabela `status_estagio_empresa`
--

CREATE TABLE `status_estagio_empresa` (
  `id_empresa` int(11) NOT NULL,
  `id_status_estagio` int(11) NOT NULL,
  `id_area_estagio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `status_estagio_empresa`
--

INSERT INTO `status_estagio_empresa` (`id_empresa`, `id_status_estagio`, `id_area_estagio`) VALUES
(93, 3, 95),
(96, 2, 98),
(97, 2, 102),
(97, 3, 97),
(97, 3, 100),
(98, 1, 101);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tipos_usuario`
--

CREATE TABLE `tipos_usuario` (
  `id` int(11) NOT NULL,
  `tipo` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `tipos_usuario`
--

INSERT INTO `tipos_usuario` (`id`, `tipo`) VALUES
(1, 'Escola'),
(2, 'Empresa');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `id_tipo_usuario` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_escola` int(11) DEFAULT NULL,
  `caminho_imagem_perfil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `senha`, `id_tipo_usuario`, `id_empresa`, `id_escola`, `caminho_imagem_perfil`) VALUES
(27, '$2y$10$eNO6Fjdb2DwNR8vJsOVVHurldmiGYoHkf7qfdUtVfIDPyyXZgifeq', 1, NULL, 1, '/stagely/backend/private/files/images/IPDDF/1772400722.png'),
(76, '$2y$10$gx6QBTF29FcrYKy3xInv2OuMkV9u0XBrsT3v3RS1ee1YqyoXtNV3W', 2, 93, NULL, '/stagely/backend/private/files/images/TIS/1779567990.png'),
(77, '$2y$10$myW8KOblSzsPnGJJ7DI2S.T/utpS3xIGRF3UhTfXnzZEI8lExfBk2', 2, 94, NULL, '/stagely/backend/private/files/images/Tech_Waves/1779568372.png'),
(78, '$2y$10$aTzF0lTihWqSK/d05ukNPOqZkLSCLZ726aetReYW2ygpkDZ3cNwP6', 2, 95, NULL, '/stagely/backend/private/files/images/Brafrikon_Engenharia/1779568523.png'),
(79, '$2y$10$Td9qiGFO30HmvC5AVBRAheinEzLz9YoEbj/Lhs3bMucO8we9DDDLi', 2, 96, NULL, '/stagely/backend/private/files/images/APROT_Engenharia/1779568612.png'),
(80, '$2y$10$8SRN6o8VE3kz/7h4DR5m7ubuBJBz7MRA8.K.zRHCt4DpAwcV2LUt2', 2, 97, NULL, '/stagely/backend/private/files/images/Ozuna_Engineering/1780659934.jpg'),
(81, '$2y$10$mhLbyiTWQfOP16XtuIZd6.hiaOe7y5RSdWJiY.0fx1stjt8usB17e', 2, 98, NULL, '/stagely/backend/private/files/images/Luminoser_Angola/1779568745.jpeg');

-- --------------------------------------------------------

--
-- Estrutura da tabela `vagas_estagio`
--

CREATE TABLE `vagas_estagio` (
  `id` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `quantidade` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `vagas_estagio`
--

INSERT INTO `vagas_estagio` (`id`, `id_empresa`, `quantidade`) VALUES
(74, 93, 3),
(75, 94, 2),
(76, 95, 2),
(77, 96, 2),
(78, 97, 5),
(79, 98, 4);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `alunos`
--
ALTER TABLE `alunos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_processo` (`numero_processo`),
  ADD UNIQUE KEY `bilhete_identidade` (`bilhete_identidade`),
  ADD KEY `id_curso` (`id_curso`),
  ADD KEY `id_encarregado` (`id_encarregado`),
  ADD KEY `id_status_estagio` (`id_status_estagio`),
  ADD KEY `id_ano_letivo` (`id_ano_letivo`);

--
-- Índices para tabela `ano_letivo`
--
ALTER TABLE `ano_letivo`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `areas_empresa`
--
ALTER TABLE `areas_empresa`
  ADD PRIMARY KEY (`id_area_estagio`,`id_empresa`),
  ADD KEY `areas_empresa_ibfk_2` (`id_empresa`);

--
-- Índices para tabela `areas_estagio`
--
ALTER TABLE `areas_estagio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_curso` (`id_curso`),
  ADD KEY `nome` (`nome`);

--
-- Índices para tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_aluno` (`id_aluno`);

--
-- Índices para tabela `contactos_encarregados`
--
ALTER TABLE `contactos_encarregados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `telefone` (`telefone`),
  ADD KEY `id_encarregado` (`id_encarregado`);

--
-- Índices para tabela `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `datas_estagio`
--
ALTER TABLE `datas_estagio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `datas_estagio_ibfk_1` (`id_empresa`),
  ADD KEY `id_curso` (`id_curso`),
  ADD KEY `id_area_estagio` (`id_area_estagio`);

--
-- Índices para tabela `dias_estagio`
--
ALTER TABLE `dias_estagio`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `dias_horario`
--
ALTER TABLE `dias_horario`
  ADD PRIMARY KEY (`id_horario`,`id_dia_estagio`),
  ADD KEY `id_dia_estagio` (`id_dia_estagio`);

--
-- Índices para tabela `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `nome` (`nome`),
  ADD KEY `id_localizacao` (`id_localizacao`),
  ADD KEY `id_status_empresa` (`id_status_empresa`);

--
-- Índices para tabela `encarregados`
--
ALTER TABLE `encarregados`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `escola`
--
ALTER TABLE `escola`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices para tabela `estagiarios`
--
ALTER TABLE `estagiarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_aluno` (`id_aluno`,`id_area_estagio`,`id_empresa`),
  ADD UNIQUE KEY `id_aluno_3` (`id_aluno`,`id_area_estagio`,`id_empresa`),
  ADD UNIQUE KEY `id_aluno_5` (`id_aluno`,`id_area_estagio`,`id_empresa`),
  ADD KEY `id_area_estagio` (`id_area_estagio`),
  ADD KEY `estagiarios_ibfk_3` (`id_empresa`),
  ADD KEY `id_aluno_2` (`id_aluno`,`id_area_estagio`,`id_empresa`),
  ADD KEY `id_aluno_4` (`id_aluno`);

--
-- Índices para tabela `horarios_estagio`
--
ALTER TABLE `horarios_estagio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_empresa` (`id_empresa`),
  ADD KEY `id_curso` (`id_curso`),
  ADD KEY `id_area_estagio` (`id_area_estagio`);

--
-- Índices para tabela `localizacao`
--
ALTER TABLE `localizacao`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `ramos_atuacao`
--
ALTER TABLE `ramos_atuacao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_curso` (`id_curso`);

--
-- Índices para tabela `ramos_empresa`
--
ALTER TABLE `ramos_empresa`
  ADD PRIMARY KEY (`id_empresa`,`id_ramo_atuacao`),
  ADD KEY `id_ramo_atuacao` (`id_ramo_atuacao`);

--
-- Índices para tabela `status_estagio`
--
ALTER TABLE `status_estagio`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `status_estagio_empresa`
--
ALTER TABLE `status_estagio_empresa`
  ADD PRIMARY KEY (`id_empresa`,`id_status_estagio`,`id_area_estagio`),
  ADD KEY `id_area_estagio` (`id_area_estagio`),
  ADD KEY `id_status_estagio` (`id_status_estagio`);

--
-- Índices para tabela `tipos_usuario`
--
ALTER TABLE `tipos_usuario`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_tipo_usuario` (`id_tipo_usuario`),
  ADD KEY `id_escola` (`id_escola`),
  ADD KEY `usuarios_ibfk_2` (`id_empresa`);

--
-- Índices para tabela `vagas_estagio`
--
ALTER TABLE `vagas_estagio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_empresa` (`id_empresa`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `alunos`
--
ALTER TABLE `alunos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `ano_letivo`
--
ALTER TABLE `ano_letivo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `areas_estagio`
--
ALTER TABLE `areas_estagio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de tabela `contactos_encarregados`
--
ALTER TABLE `contactos_encarregados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `datas_estagio`
--
ALTER TABLE `datas_estagio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT de tabela `dias_estagio`
--
ALTER TABLE `dias_estagio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT de tabela `encarregados`
--
ALTER TABLE `encarregados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `escola`
--
ALTER TABLE `escola`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `estagiarios`
--
ALTER TABLE `estagiarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=173;

--
-- AUTO_INCREMENT de tabela `horarios_estagio`
--
ALTER TABLE `horarios_estagio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT de tabela `localizacao`
--
ALTER TABLE `localizacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT de tabela `ramos_atuacao`
--
ALTER TABLE `ramos_atuacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `status_estagio`
--
ALTER TABLE `status_estagio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `tipos_usuario`
--
ALTER TABLE `tipos_usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT de tabela `vagas_estagio`
--
ALTER TABLE `vagas_estagio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `alunos`
--
ALTER TABLE `alunos`
  ADD CONSTRAINT `alunos_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id`),
  ADD CONSTRAINT `alunos_ibfk_2` FOREIGN KEY (`id_encarregado`) REFERENCES `encarregados` (`id`),
  ADD CONSTRAINT `alunos_ibfk_3` FOREIGN KEY (`id_status_estagio`) REFERENCES `status_estagio` (`id`),
  ADD CONSTRAINT `alunos_ibfk_4` FOREIGN KEY (`id_ano_letivo`) REFERENCES `ano_letivo` (`id`);

--
-- Limitadores para a tabela `areas_empresa`
--
ALTER TABLE `areas_empresa`
  ADD CONSTRAINT `areas_empresa_ibfk_1` FOREIGN KEY (`id_area_estagio`) REFERENCES `areas_estagio` (`id`),
  ADD CONSTRAINT `areas_empresa_ibfk_2` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `areas_estagio`
--
ALTER TABLE `areas_estagio`
  ADD CONSTRAINT `areas_estagio_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id`);

--
-- Limitadores para a tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD CONSTRAINT `avaliacoes_ibfk_1` FOREIGN KEY (`id_aluno`) REFERENCES `alunos` (`id`);

--
-- Limitadores para a tabela `contactos_encarregados`
--
ALTER TABLE `contactos_encarregados`
  ADD CONSTRAINT `contactos_encarregados_ibfk_1` FOREIGN KEY (`id_encarregado`) REFERENCES `encarregados` (`id`);

--
-- Limitadores para a tabela `datas_estagio`
--
ALTER TABLE `datas_estagio`
  ADD CONSTRAINT `datas_estagio_ibfk_1` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `datas_estagio_ibfk_2` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id`),
  ADD CONSTRAINT `datas_estagio_ibfk_3` FOREIGN KEY (`id_area_estagio`) REFERENCES `areas_estagio` (`id`);

--
-- Limitadores para a tabela `dias_horario`
--
ALTER TABLE `dias_horario`
  ADD CONSTRAINT `dias_horario_ibfk_1` FOREIGN KEY (`id_horario`) REFERENCES `horarios_estagio` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dias_horario_ibfk_2` FOREIGN KEY (`id_dia_estagio`) REFERENCES `dias_estagio` (`id`);

--
-- Limitadores para a tabela `empresas`
--
ALTER TABLE `empresas`
  ADD CONSTRAINT `empresas_ibfk_2` FOREIGN KEY (`id_localizacao`) REFERENCES `localizacao` (`id`),
  ADD CONSTRAINT `empresas_ibfk_4` FOREIGN KEY (`id_status_empresa`) REFERENCES `status_estagio` (`id`);

--
-- Limitadores para a tabela `estagiarios`
--
ALTER TABLE `estagiarios`
  ADD CONSTRAINT `estagiarios_ibfk_1` FOREIGN KEY (`id_aluno`) REFERENCES `alunos` (`id`),
  ADD CONSTRAINT `estagiarios_ibfk_2` FOREIGN KEY (`id_area_estagio`) REFERENCES `areas_estagio` (`id`),
  ADD CONSTRAINT `estagiarios_ibfk_3` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `horarios_estagio`
--
ALTER TABLE `horarios_estagio`
  ADD CONSTRAINT `horarios_estagio_ibfk_3` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `horarios_estagio_ibfk_4` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id`),
  ADD CONSTRAINT `horarios_estagio_ibfk_5` FOREIGN KEY (`id_area_estagio`) REFERENCES `areas_estagio` (`id`);

--
-- Limitadores para a tabela `ramos_atuacao`
--
ALTER TABLE `ramos_atuacao`
  ADD CONSTRAINT `ramos_atuacao_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id`);

--
-- Limitadores para a tabela `ramos_empresa`
--
ALTER TABLE `ramos_empresa`
  ADD CONSTRAINT `ramos_empresa_ibfk_1` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ramos_empresa_ibfk_2` FOREIGN KEY (`id_ramo_atuacao`) REFERENCES `ramos_atuacao` (`id`);

--
-- Limitadores para a tabela `status_estagio_empresa`
--
ALTER TABLE `status_estagio_empresa`
  ADD CONSTRAINT `status_estagio_empresa_ibfk_1` FOREIGN KEY (`id_area_estagio`) REFERENCES `areas_estagio` (`id`),
  ADD CONSTRAINT `status_estagio_empresa_ibfk_2` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id`),
  ADD CONSTRAINT `status_estagio_empresa_ibfk_3` FOREIGN KEY (`id_status_estagio`) REFERENCES `status_estagio` (`id`);

--
-- Limitadores para a tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_tipo_usuario`) REFERENCES `tipos_usuario` (`id`),
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuarios_ibfk_3` FOREIGN KEY (`id_escola`) REFERENCES `escola` (`id`);

--
-- Limitadores para a tabela `vagas_estagio`
--
ALTER TABLE `vagas_estagio`
  ADD CONSTRAINT `vagas_estagio_ibfk_1` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
