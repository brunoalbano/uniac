SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE DATABASE IF NOT EXISTS `uniac` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `uniac`;

CREATE TABLE IF NOT EXISTS `anexo` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `caminho` varchar(255) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `data_upload` datetime NOT NULL,
  `tamanho` decimal(10,2) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=177 ;

CREATE TABLE IF NOT EXISTS `atividade` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `horas_requisitadas` int(11) NOT NULL,
  `horas_aceitas` int(11) unsigned zerofill NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descricao` text NOT NULL,
  `justificativa` text,
  `criado_em` datetime NOT NULL,
  `atualizado_em` datetime NOT NULL,
  `avaliado_em` datetime DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `tipo_atividade_codigo` int(11) NOT NULL,
  `matricula_codigo` int(11) NOT NULL,
  `usuario_resp_criacao_codigo` int(11) NOT NULL,
  `usuario_resp_avaliacao_codigo` int(11) DEFAULT NULL,
  `motivo_recusa_codigo` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`),
  KEY `fk_Atividade_Tipo_atividade_idx` (`tipo_atividade_codigo`),
  KEY `fk_Atividade_Matricula_idx` (`matricula_codigo`),
  KEY `fk_Atividade_Usuario_criacao_idx` (`usuario_resp_criacao_codigo`),
  KEY `fk_Atividade_Usuario_avaliacao_idx` (`usuario_resp_avaliacao_codigo`),
  KEY `fk_Atividade_MotivoRecusa_idx` (`motivo_recusa_codigo`),
  KEY `idx_titulo` (`titulo`),
  KEY `idx_atualizado_em` (`atualizado_em`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

CREATE TABLE IF NOT EXISTS `atividade_anexo` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `atividade_codigo` int(11) NOT NULL,
  `anexo_codigo` int(11) NOT NULL,
  PRIMARY KEY (`codigo`),
  UNIQUE KEY `Atividade_anexo_UNIQUE` (`atividade_codigo`,`anexo_codigo`),
  KEY `fk_Atividade_anexo_Atividade_idx` (`atividade_codigo`),
  KEY `fk_Atividade_anexo_Anexo_idx` (`anexo_codigo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

CREATE TABLE IF NOT EXISTS `campus` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  PRIMARY KEY (`codigo`),
  UNIQUE KEY `Campus_nome_UNIQUE` (`nome`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

CREATE TABLE IF NOT EXISTS `comentario` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `comentario` text NOT NULL,
  `usuario_codigo` int(11) NOT NULL,
  `atividade_codigo` int(11) NOT NULL,
  `criado_em` datetime NOT NULL,
  `atualizado_em` datetime NOT NULL,
  `interno` varchar(100) NOT NULL,
  PRIMARY KEY (`codigo`),
  KEY `fk_Comentario_Usuario_idx` (`usuario_codigo`),
  KEY `fk_Comentario_Atividade_idx` (`atividade_codigo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

CREATE TABLE IF NOT EXISTS `curso` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `campus_codigo` int(11) NOT NULL,
  PRIMARY KEY (`codigo`),
  UNIQUE KEY `Curso_nome_UNIQUE` (`nome`),
  KEY `fk_Curso_Campus_idx` (`campus_codigo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=168 ;

CREATE TABLE IF NOT EXISTS `curso_anexo` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `curso_codigo` int(11) NOT NULL,
  `anexo_codigo` int(11) NOT NULL,
  PRIMARY KEY (`codigo`),
  UNIQUE KEY `Curso_anexo_UNIQUE` (`curso_codigo`,`anexo_codigo`),
  KEY `fk_Curso_anexo_Curso_idx` (`curso_codigo`),
  KEY `fk_Curso_anexo_Anexo_idx` (`anexo_codigo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=41 ;

CREATE TABLE IF NOT EXISTS `log` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_codigo` int(11) DEFAULT NULL,
  `url` varchar(200) DEFAULT NULL,
  `navegador` varchar(200) DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `tabela` varchar(64) DEFAULT NULL,
  `tabela_codigo_campo` varchar(100) DEFAULT NULL,
  `tabela_codigo_valor` varchar(100) DEFAULT NULL,
  `acao` varchar(100) NOT NULL,
  `criado_em` datetime NOT NULL,
  `atualizado_em` datetime NOT NULL,
  PRIMARY KEY (`codigo`),
  KEY `fk_log_Usuario1_idx` (`usuario_codigo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24754 ;

CREATE TABLE IF NOT EXISTS `matricula` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `saldo_anterior` int(11) unsigned zerofill NOT NULL,
  `turma_codigo` int(11) NOT NULL,
  `usuario_codigo` int(11) NOT NULL,
  `matriz_curricular_codigo` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `horas_aceitas` int(11) unsigned zerofill NOT NULL,
  PRIMARY KEY (`codigo`),
  KEY `fk_Matricula_Turma_idx` (`turma_codigo`),
  KEY `fk_Matricula_Usuario_idx` (`usuario_codigo`),
  KEY `fk_Matricula_Matriz_curricular_idx` (`matriz_curricular_codigo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=69 ;

CREATE TABLE IF NOT EXISTS `matriz_curricular` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `horas` int(11) NOT NULL,
  `curso_codigo` int(11) NOT NULL,
  PRIMARY KEY (`codigo`),
  KEY `fk_Matriz_curricular_Curso_idx` (`curso_codigo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;

CREATE TABLE IF NOT EXISTS `motivo_recusa` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  PRIMARY KEY (`codigo`),
  UNIQUE KEY `motivo_recusa_unique` (`nome`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

CREATE TABLE IF NOT EXISTS `recuperar_senha` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `token` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=21 ;

CREATE TABLE IF NOT EXISTS `tipo_atividade` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(200) NOT NULL,
  `horas` int(11) DEFAULT NULL,
  `obrigatorio` tinyint(1) NOT NULL,
  `visivel_para_aluno` tinyint(1) NOT NULL,
  `ativo` tinyint(1) NOT NULL,
  `tipo_atividade_codigo` int(11) DEFAULT NULL,
  `curso_codigo` int(11) NOT NULL,
  `nivel` varchar(100) NOT NULL,
  PRIMARY KEY (`codigo`),
  KEY `fk_Tipo_atividade_Tipo_atividade_idx` (`tipo_atividade_codigo`),
  KEY `fk_Tipo_atividade_Curso_idx` (`curso_codigo`),
  KEY `idx_descricao` (`descricao`),
  KEY `idx_nivel` (`nivel`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22296 ;

CREATE TABLE IF NOT EXISTS `turma` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `ativa` tinyint(1) NOT NULL,
  `curso_codigo` int(11) NOT NULL,
  PRIMARY KEY (`codigo`),
  UNIQUE KEY `nome_UNIQUE` (`nome`),
  KEY `fk_Turma_Curso_idx` (`curso_codigo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

CREATE TABLE IF NOT EXISTS `usuario` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `primeiro_nome` varchar(50) NOT NULL,
  `sobrenome` varchar(100) NOT NULL,
  `login` varchar(20) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(60) NOT NULL,
  `ultimo_acesso` datetime DEFAULT NULL,
  `acesso_liberado` tinyint(1) NOT NULL,
  `perfil` int(11) NOT NULL,
  `notificar` tinyint(1) NOT NULL,
  PRIMARY KEY (`codigo`),
  UNIQUE KEY `login_UNIQUE` (`login`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  KEY `idx_nome` (`primeiro_nome`),
  KEY `idx_sobrenome` (`sobrenome`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=151 ;

CREATE TABLE IF NOT EXISTS `usuario_curso` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `coordenador` tinyint(1) NOT NULL,
  `curso_codigo` int(11) NOT NULL,
  `usuario_codigo` int(11) NOT NULL,
  PRIMARY KEY (`codigo`),
  UNIQUE KEY `Usuario_curso_UNIQUE` (`curso_codigo`,`usuario_codigo`),
  KEY `fk_Usuario_curso_Curso_idx` (`curso_codigo`),
  KEY `fk_Usuario_curso_Usuario_idx` (`usuario_codigo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36 ;


ALTER TABLE `atividade`
  ADD CONSTRAINT `fk_Atividade_Matricula` FOREIGN KEY (`matricula_codigo`) REFERENCES `matricula` (`codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Atividade_MotivoRecusa` FOREIGN KEY (`motivo_recusa_codigo`) REFERENCES `motivo_recusa` (`codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Atividade_Tipo_atividade` FOREIGN KEY (`tipo_atividade_codigo`) REFERENCES `tipo_atividade` (`codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Atividade_Usuario_avaliacao` FOREIGN KEY (`usuario_resp_avaliacao_codigo`) REFERENCES `usuario` (`codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Atividade_Usuario_criacao` FOREIGN KEY (`usuario_resp_criacao_codigo`) REFERENCES `usuario` (`codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `atividade_anexo`
  ADD CONSTRAINT `fk_Atividade_anexo_Anexo` FOREIGN KEY (`anexo_codigo`) REFERENCES `anexo` (`codigo`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Atividade_anexo_Atividade` FOREIGN KEY (`atividade_codigo`) REFERENCES `atividade` (`codigo`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `comentario`
  ADD CONSTRAINT `fk_Comentario_Atividade` FOREIGN KEY (`atividade_codigo`) REFERENCES `atividade` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_Comentario_Usuario` FOREIGN KEY (`usuario_codigo`) REFERENCES `usuario` (`codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `curso`
  ADD CONSTRAINT `fk_Curso_Campus` FOREIGN KEY (`campus_codigo`) REFERENCES `campus` (`codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `curso_anexo`
  ADD CONSTRAINT `fk_Curso_anexo_Anexo` FOREIGN KEY (`anexo_codigo`) REFERENCES `anexo` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_Curso_anexo_Curso` FOREIGN KEY (`curso_codigo`) REFERENCES `curso` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `log`
  ADD CONSTRAINT `fk_log_Usuario1` FOREIGN KEY (`usuario_codigo`) REFERENCES `usuario` (`codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `matricula`
  ADD CONSTRAINT `fk_Matricula_Matriz_curricular` FOREIGN KEY (`matriz_curricular_codigo`) REFERENCES `matriz_curricular` (`codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Matricula_Turma` FOREIGN KEY (`turma_codigo`) REFERENCES `turma` (`codigo`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Matricula_Usuario` FOREIGN KEY (`usuario_codigo`) REFERENCES `usuario` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `matriz_curricular`
  ADD CONSTRAINT `fk_Matriz_curricular_Curso` FOREIGN KEY (`curso_codigo`) REFERENCES `curso` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tipo_atividade`
  ADD CONSTRAINT `fk_Tipo_atividade_Curso` FOREIGN KEY (`curso_codigo`) REFERENCES `curso` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_Tipo_atividade_Tipo_atividade` FOREIGN KEY (`tipo_atividade_codigo`) REFERENCES `tipo_atividade` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `turma`
  ADD CONSTRAINT `fk_Turma_Curso` FOREIGN KEY (`curso_codigo`) REFERENCES `curso` (`codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `usuario_curso`
  ADD CONSTRAINT `fk_Usuario_curso_Curso` FOREIGN KEY (`curso_codigo`) REFERENCES `curso` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_Usuario_curso_Usuario` FOREIGN KEY (`usuario_codigo`) REFERENCES `usuario` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
