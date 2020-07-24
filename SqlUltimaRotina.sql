-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema comprasnet_db
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema comprasnet_db
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `comprasnet_db` DEFAULT CHARACTER SET latin1 ;
USE `comprasnet_db` ;

-- -----------------------------------------------------
-- Table `comprasnet_db`.`config_api_app`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comprasnet_db`.`config_api_app` (
  `firebase` VARCHAR(1000) NULL DEFAULT NULL,
  `token_jwt` VARCHAR(1000) NULL DEFAULT NULL)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `comprasnet_db`.`conn_smtp`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comprasnet_db`.`conn_smtp` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `remetente` VARCHAR(45) NOT NULL,
  `server_smtp` VARCHAR(45) NULL DEFAULT NULL,
  `port_smtp` INT(11) NULL DEFAULT NULL,
  `usuario` VARCHAR(45) NULL DEFAULT NULL,
  `senha` VARCHAR(45) NOT NULL,
  `cop_email` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `comprasnet_db`.`fabricantes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comprasnet_db`.`fabricantes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(85) NULL DEFAULT NULL,
  `email` VARCHAR(65) NULL DEFAULT NULL,
  `descricao` VARCHAR(85) NULL DEFAULT NULL,
  `cod_fabricante` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `comprasnet_db`.`licitacoes_cab`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comprasnet_db`.`licitacoes_cab` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uasg` INT(11) NOT NULL,
  `identificador` BIGINT(20) NOT NULL,
  `cod_modalidade` INT(11) NULL DEFAULT NULL,
  `numero_aviso` INT(11) NULL DEFAULT NULL,
  `tipo_pregao` VARCHAR(45) NULL DEFAULT NULL,
  `numero_processo` VARCHAR(20) NULL DEFAULT NULL,
  `numero_itens` INT(11) NULL DEFAULT NULL,
  `situacao_aviso` VARCHAR(45) NULL DEFAULT NULL,
  `objeto` VARCHAR(999) NULL DEFAULT NULL,
  `informacoes_gerais` VARCHAR(999) NULL DEFAULT NULL,
  `tipo_recurso` VARCHAR(45) NULL DEFAULT NULL,
  `nome_responsavel` VARCHAR(180) NULL DEFAULT NULL,
  `funcao_responsavel` VARCHAR(180) NULL DEFAULT NULL,
  `data_entrega_edital` DATETIME NULL DEFAULT NULL,
  `endereco_entrega_edital` VARCHAR(180) NULL DEFAULT NULL,
  `data_abertura_proposta` DATETIME NULL DEFAULT NULL,
  `data_entrega_proposta` DATETIME NULL DEFAULT NULL,
  `data_publicacao` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `identificador_UNIQUE` (`identificador` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `comprasnet_db`.`licitacao_itens`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comprasnet_db`.`licitacao_itens` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `lic_uasg` INT(11) NOT NULL,
  `lic_id` BIGINT(20) NOT NULL,
  `num_aviso` INT(11) NULL DEFAULT NULL,
  `num_item_licitacao` INT(11) NULL DEFAULT NULL,
  `cod_item_servico` INT(11) NULL DEFAULT NULL,
  `cod_item_material` INT(11) NULL DEFAULT NULL,
  `descricao_item` VARCHAR(999) NULL DEFAULT NULL,
  `sustentavel` INT(11) NULL DEFAULT NULL,
  `quantidade` VARCHAR(45) NULL DEFAULT NULL,
  `unidade` VARCHAR(45) NULL DEFAULT NULL,
  `cnpj_fornecedor` VARCHAR(45) NULL DEFAULT NULL,
  `cpf_vencedor` VARCHAR(45) NULL DEFAULT NULL,
  `beneficio` VARCHAR(90) NULL DEFAULT NULL,
  `valor_estimado` VARCHAR(45) NULL DEFAULT NULL,
  `decreto_7174` INT(11) NULL DEFAULT NULL,
  `criterio_julgamento` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_licitacao_itens_identificador_idx` (`lic_id` ASC),
  CONSTRAINT `fk_licitacao_itens_identificador`
    FOREIGN KEY (`lic_id`)
    REFERENCES `comprasnet_db`.`licitacoes_cab` (`identificador`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `comprasnet_db`.`email_enviados`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comprasnet_db`.`email_enviados` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `item_id` INT(11) NOT NULL,
  `fabricante_id` INT(11) NOT NULL,
  `produto_id` INT(11) NOT NULL,
  `email_enviado` ENUM('Y', 'N') NOT NULL DEFAULT 'N',
  `resposta` VARCHAR(85) NULL DEFAULT NULL,
  `data_envio` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_produto_idx` (`item_id` ASC),
  INDEX `fk_fabricante_idx` (`fabricante_id` ASC),
  CONSTRAINT `fk_fabricante`
    FOREIGN KEY (`fabricante_id`)
    REFERENCES `comprasnet_db`.`fabricantes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_produto`
    FOREIGN KEY (`item_id`)
    REFERENCES `comprasnet_db`.`licitacao_itens` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `comprasnet_db`.`licitacao_orgao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comprasnet_db`.`licitacao_orgao` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uasg` INT(11) NULL DEFAULT NULL,
  `lic_orgao` VARCHAR(90) NULL DEFAULT NULL,
  `lic_estado` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `comprasnet_db`.`materiais`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comprasnet_db`.`materiais` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `cod_material` INT(11) NULL DEFAULT NULL,
  `descricao` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_material_item`
    FOREIGN KEY (`id`)
    REFERENCES `comprasnet_db`.`licitacao_itens` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `comprasnet_db`.`modalidades`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comprasnet_db`.`modalidades` (
  `id` INT(11) NOT NULL,
  `cod_modalidade` INT(11) NULL DEFAULT NULL,
  `descricao` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_material_licitacao`
    FOREIGN KEY (`id`)
    REFERENCES `comprasnet_db`.`licitacoes_cab` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `comprasnet_db`.`produtos_futura`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comprasnet_db`.`produtos_futura` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `fabricante_id` INT(11) NULL DEFAULT NULL,
  `item_id` INT(11) NOT NULL,
  `nome_portal` VARCHAR(999) NULL DEFAULT NULL,
  `num_item_licitacao` INT(11) NOT NULL,
  `cod_jd_produto` INT(11) NULL DEFAULT NULL,
  `desc_licitacao_portal` VARCHAR(9999) NULL DEFAULT NULL,
  `quantidade_item_licitacao` INT(11) NULL DEFAULT NULL,
  `desc_licitacao_jd` TEXT NULL DEFAULT NULL,
  `cod_produto_jd` INT(11) NULL DEFAULT NULL,
  `quantidade_embalagem_produto_jd` INT(11) NULL DEFAULT NULL,
  `desc_produto_jd` VARCHAR(9999) NULL DEFAULT NULL,
  `cod_fabricante_jd` INT(11) NULL DEFAULT NULL,
  `nome_fabricante` VARCHAR(120) NULL DEFAULT NULL,
  `estoque_disp_jd` VARCHAR(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_produtos_futura_licitacao_itens_idx` (`item_id` ASC),
  INDEX `fk_produtos_futura_fabricantes_idx` (`fabricante_id` ASC),
  CONSTRAINT `fk_produtos_futura_fabricantes`
    FOREIGN KEY (`fabricante_id`)
    REFERENCES `comprasnet_db`.`fabricantes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_produtos_futura_licitacao_itens`
    FOREIGN KEY (`item_id`)
    REFERENCES `comprasnet_db`.`licitacao_itens` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `comprasnet_db`.`smtp_body`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comprasnet_db`.`smtp_body` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `smtp_assunto` VARCHAR(90) NULL DEFAULT NULL,
  `smtp_corpo` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `comprasnet_db`.`timeout`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comprasnet_db`.`timeout` (
  `minutos` INT(11) NOT NULL,
  PRIMARY KEY (`minutos`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `comprasnet_db`.`usuarios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comprasnet_db`.`usuarios` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(90) NOT NULL,
  `email` VARCHAR(90) NOT NULL,
  `senha` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

insert into usuarios (nome, email, senha) VALUES ('ActBrasil', 'admin@futura.com', 'e10adc3949ba59abbe56e057f20f883e');


insert into  conn_smtp VALUES  ('1', 'tanaiiir@gmail.com', 'smtp.gmail.com', '465', 'tanaiiir', 'arywivvkudppcwtl', 'l.francelino@outlook.com');


insert into smtp_body VALUES ('1', 'ORGÃO', '<p>Segue em anexo o Edital referente ao pregão em assunto.</p>\n                    <p>Abaixo o item e a estimativa de preço.</p><br>\n\n<tabela>\n\n <p>Solicitamos autorização para participar do referido Certame.</p>\n                       <p>Grata,</p>\n                       \n                        <small>--</small><br>\n                        <small>Elda Silva</small><br>\n                        <small>Auxiliar de Licitação</small><br>\n                        <small>Futura Distribuidora de Medicamentos e Produtos de Saúde</small><br>\n
      <small>Tel: 21-3311-5186</small>');

