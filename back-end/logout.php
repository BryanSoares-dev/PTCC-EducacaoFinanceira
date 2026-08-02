<?php
session_start();
// Destroi todas as variaveis
session_destroy();
header('Location: ../front-end/home.php');
?>