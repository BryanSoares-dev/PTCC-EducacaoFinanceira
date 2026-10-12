-- Execute uma vez no banco do projeto para o webhook liberar os planos pagos.
ALTER TABLE usuarios
    ADD COLUMN plano_atual VARCHAR(20) NOT NULL DEFAULT 'gratuito',
    ADD COLUMN plano_expira_em DATETIME NULL;