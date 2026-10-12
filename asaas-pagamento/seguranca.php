<?php
/** Configuração de segurança compartilhada com o restante do site. */
require_once __DIR__ . '/../back-end/seguranca.php';

// A sessão do site guarda a identidade em $_SESSION['id'].
iniciar_sessao_segura();