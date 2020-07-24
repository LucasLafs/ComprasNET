-- MySQL dump 10.13  Distrib 5.7.30, for Linux (x86_64)
--
-- Host: localhost    Database: comprasnet_db
-- ------------------------------------------------------
-- Server version	5.7.30-0ubuntu0.16.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `config_api_app`
--

DROP TABLE IF EXISTS `config_api_app`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config_api_app` (
  `firebase` varchar(1000) DEFAULT NULL,
  `token_jwt` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `conn_smtp`
--

DROP TABLE IF EXISTS `conn_smtp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conn_smtp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `remetente` varchar(45) NOT NULL,
  `server_smtp` varchar(45) DEFAULT NULL,
  `port_smtp` int(11) DEFAULT NULL,
  `usuario` varchar(45) DEFAULT NULL,
  `senha` varchar(45) NOT NULL,
  `cop_email` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_enviados`
--

DROP TABLE IF EXISTS `email_enviados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_enviados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `fabricante_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `email_enviado` enum('Y','N') NOT NULL DEFAULT 'N',
  `resposta` varchar(85) DEFAULT NULL,
  `data_envio` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_produto_idx` (`item_id`),
  KEY `fk_fabricante_idx` (`fabricante_id`),
  CONSTRAINT `fk_fabricante` FOREIGN KEY (`fabricante_id`) REFERENCES `fabricantes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_produto` FOREIGN KEY (`item_id`) REFERENCES `licitacao_itens` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fabricantes`
--

DROP TABLE IF EXISTS `fabricantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fabricantes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(85) DEFAULT NULL,
  `email` varchar(65) NOT NULL DEFAULT '',
  `descricao` varchar(85) DEFAULT NULL,
  `cod_fabricante` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=677 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `licitacao_itens`
--

DROP TABLE IF EXISTS `licitacao_itens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `licitacao_itens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lic_uasg` int(11) NOT NULL,
  `lic_id` bigint(20) NOT NULL,
  `num_aviso` int(11) DEFAULT NULL,
  `num_item_licitacao` int(11) DEFAULT NULL,
  `cod_item_servico` int(11) DEFAULT NULL,
  `cod_item_material` int(11) DEFAULT NULL,
  `descricao_item` varchar(9999) DEFAULT NULL,
  `sustentavel` int(11) DEFAULT NULL,
  `quantidade` varchar(45) DEFAULT NULL,
  `unidade` varchar(45) DEFAULT NULL,
  `cnpj_fornecedor` varchar(45) DEFAULT NULL,
  `cpf_vencedor` varchar(45) DEFAULT NULL,
  `beneficio` varchar(90) DEFAULT NULL,
  `valor_estimado` varchar(45) DEFAULT NULL,
  `decreto_7174` int(11) DEFAULT NULL,
  `criterio_julgamento` varchar(45) DEFAULT NULL,
  `updated` tinyint(1) NOT NULL DEFAULT '1',
  `valid` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `licitacao_itens_lic_id_num_item_licitacao_idx` (`lic_id`,`num_item_licitacao`),
  KEY `fk_licitacao_itens_identificador_idx` (`lic_id`),
  FULLTEXT KEY `licitacao_itens_descricao_item_idx` (`descricao_item`),
  CONSTRAINT `fk_licitacao_itens_identificador` FOREIGN KEY (`lic_id`) REFERENCES `licitacoes_cab` (`identificador`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=421645 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `licitacao_orgao`
--

DROP TABLE IF EXISTS `licitacao_orgao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `licitacao_orgao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uasg` int(11) DEFAULT NULL,
  `lic_orgao` varchar(90) DEFAULT NULL,
  `lic_estado` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `licitacao_orgao_uasg_idx` (`uasg`) USING BTREE,
  KEY `licitacao_orgao_lic_estado_idx` (`lic_estado`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8556 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `licitacoes_cab`
--

DROP TABLE IF EXISTS `licitacoes_cab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `licitacoes_cab` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uasg` int(11) NOT NULL,
  `identificador` bigint(20) NOT NULL,
  `cod_modalidade` int(11) DEFAULT NULL,
  `numero_aviso` int(11) DEFAULT NULL,
  `tipo_pregao` varchar(45) DEFAULT NULL,
  `numero_processo` varchar(20) DEFAULT NULL,
  `numero_itens` int(11) NOT NULL DEFAULT '0',
  `situacao_aviso` varchar(45) DEFAULT NULL,
  `objeto` varchar(9999) DEFAULT NULL,
  `informacoes_gerais` varchar(9999) DEFAULT NULL,
  `tipo_recurso` varchar(45) DEFAULT NULL,
  `nome_responsavel` varchar(300) DEFAULT NULL,
  `funcao_responsavel` varchar(500) DEFAULT NULL,
  `data_entrega_edital` datetime DEFAULT NULL,
  `endereco_entrega_edital` varchar(500) DEFAULT NULL,
  `data_abertura_proposta` datetime DEFAULT NULL,
  `data_entrega_proposta` datetime DEFAULT NULL,
  `data_publicacao` datetime DEFAULT NULL,
  `importador_ultima_atualizacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `identificador_UNIQUE` (`identificador`),
  KEY `licitacoes_cab_data_abertura_proposta_idx` (`data_abertura_proposta`) USING BTREE,
  KEY `licitacoes_cab_data_entrega_proposta_idx` (`data_entrega_proposta`) USING BTREE,
  KEY `licitacoes_cab_uasg_idx` (`uasg`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=129029 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `materiais`
--

DROP TABLE IF EXISTS `materiais`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `materiais` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod_material` int(11) DEFAULT NULL,
  `descricao` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_material_item` FOREIGN KEY (`id`) REFERENCES `licitacao_itens` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `modalidades`
--

DROP TABLE IF EXISTS `modalidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modalidades` (
  `id` int(11) NOT NULL,
  `cod_modalidade` int(11) DEFAULT NULL,
  `descricao` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_material_licitacao` FOREIGN KEY (`id`) REFERENCES `licitacoes_cab` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `produtos_futura`
--

DROP TABLE IF EXISTS `produtos_futura`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produtos_futura` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fabricante_id` int(11) DEFAULT NULL,
  `item_id` int(11) NOT NULL,
  `nome_portal` varchar(999) DEFAULT NULL,
  `lic_id` bigint(20) NOT NULL DEFAULT '0',
  `num_item_licitacao` int(11) NOT NULL,
  `cod_jd_produto` int(11) DEFAULT NULL,
  `desc_licitacao_portal` varchar(9999) DEFAULT NULL,
  `quantidade_item_licitacao` int(11) DEFAULT NULL,
  `desc_licitacao_jd` text,
  `cod_produto_jd` int(11) DEFAULT NULL,
  `quantidade_embalagem_produto_jd` int(11) DEFAULT NULL,
  `desc_produto_jd` varchar(9999) DEFAULT NULL,
  `cod_fabricante_jd` int(11) DEFAULT NULL,
  `nome_fabricante` varchar(120) DEFAULT NULL,
  `estoque_disp_jd` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_produtos_futura_licitacao_itens_idx` (`item_id`),
  KEY `fk_produtos_futura_fabricantes_idx` (`fabricante_id`),
  CONSTRAINT `fk_produtos_futura_fabricantes` FOREIGN KEY (`fabricante_id`) REFERENCES `fabricantes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_produtos_futura_licitacao_itens` FOREIGN KEY (`item_id`) REFERENCES `licitacao_itens` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=92932 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `smtp_body`
--

DROP TABLE IF EXISTS `smtp_body`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smtp_body` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `smtp_assunto` varchar(90) DEFAULT NULL,
  `smtp_corpo` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `timeout`
--

DROP TABLE IF EXISTS `timeout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `timeout` (
  `minutos` int(11) NOT NULL,
  PRIMARY KEY (`minutos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(90) NOT NULL,
  `email` varchar(90) NOT NULL,
  `senha` varchar(45) NOT NULL,
  `gestor` enum('N','Y') NOT NULL DEFAULT 'N',
  `bloqueado` enum('N','Y') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-06-20 17:07:30
