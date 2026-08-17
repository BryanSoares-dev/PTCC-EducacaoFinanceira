<?php
session_start();
require_once("../back-end/conexao.php");

// ===================== AUTENTICAÇÃO =====================
if (!isset($_SESSION['id'])) {
    header("Location: ../front-end/login.php");
    exit;
}

// ===================== MÉTODO =====================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: preferencias.php");
    exit;
}

// ===================== VALIDAÇÃO (WHITELIST) =====================
$moedasValidas   = ['BRL', 'USD', 'EUR'];
$temasValidos    = ['claro', 'escuro', 'sistema'];
$idiomasValidos  = ['pt-BR', 'en-US', 'es-ES'];

$moeda  = in_array($_POST['moeda'] ?? '', $moedasValidas, true) ? $_POST['moeda'] : 'BRL';
$tema   = in_array($_POST['tema'] ?? '', $temasValidos, true) ? $_POST['tema'] : 'sistema';
$idioma = in_array($_POST['idioma'] ?? '', $idiomasValidos, true) ? $_POST['idioma'] : 'pt-BR';

$resumoSemanal = isset($_POST['resumo_semanal']) ? 1 : 0;
$alertasMetas  = isset($_POST['alertas_metas']) ? 1 : 0;

// ===================== SALVAR NO BANCO =====================
try {
    $update = $pdo->prepare("
        UPDATE usuarios
        SET moeda = ?, tema = ?, idioma = ?, resumo_semanal = ?, alertas_metas = ?
        WHERE id = ?
    ");
    $update->execute([$moeda, $tema, $idioma, $resumoSemanal, $alertasMetas, $_SESSION['id']]);

    header("Location: preferencias.php?status=sucesso");
    exit;
} catch (PDOException $e) {
    header("Location: preferencias.php?status=erro");
    exit;
}