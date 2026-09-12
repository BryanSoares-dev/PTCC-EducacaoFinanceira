-- Execute uma vez em instalações existentes do AFDE.
-- MariaDB 10.4+ e MySQL recentes aceitam IF NOT EXISTS nesta instrução.
USE `educacaofinanceira`;
ALTER TABLE `usuarios`
    ADD COLUMN IF NOT EXISTS `banner` VARCHAR(255) DEFAULT NULL AFTER `foto`;
