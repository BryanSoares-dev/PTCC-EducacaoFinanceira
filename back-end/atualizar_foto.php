<?php

declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/conexao.php';

if (empty($_SESSION['id'])) {
    header('Location: ../front-end/login.php');
    exit;
}

function voltarPerfil(string $erro): never
{
    header('Location: ../front-end/perfil.php?erro=' . rawurlencode($erro));
    exit;
}

require_csrf();

$campo = (($_POST['tipo'] ?? 'foto') === 'banner') ? 'banner' : 'foto';
$arquivo = $_FILES[$campo] ?? null;

if (!$arquivo || !isset($arquivo['error'], $arquivo['tmp_name'], $arquivo['size']) || $arquivo['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($arquivo['tmp_name'])) {
    voltarPerfil('upload_falhou');
}

$limite = $campo === 'banner' ? 8 * 1024 * 1024 : 5 * 1024 * 1024;
if ((int) $arquivo['size'] > $limite) {
    voltarPerfil($campo === 'banner' ? 'banner_grande' : 'arquivo_grande');
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($arquivo['tmp_name']);
$tipos = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/gif'  => 'gif',
];

$imagem = @getimagesize($arquivo['tmp_name']);
if (!isset($tipos[$mime]) || $imagem === false || ($imagem[0] < 1 || $imagem[1] < 1)) {
    voltarPerfil('formato_invalido');
}

$uploadDir = dirname(__DIR__) . '/uploads/perfis';
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
    voltarPerfil('pasta_indisponivel');
}

$usuarioId = (int) $_SESSION['id'];
$nomeArquivo = $campo . '_' . $usuarioId . '_' . bin2hex(random_bytes(12)) . '.' . $tipos[$mime];
$caminhoServidor = $uploadDir . DIRECTORY_SEPARATOR . $nomeArquivo;
$caminhoPublico = '../uploads/perfis/' . $nomeArquivo;

try {
    $stmt = $pdo->prepare("SELECT {$campo} FROM usuarios WHERE id = ? LIMIT 1");
    $stmt->execute([$usuarioId]);
    $arquivoAntigo = $stmt->fetchColumn();

    if (!move_uploaded_file($arquivo['tmp_name'], $caminhoServidor)) {
        voltarPerfil('upload_falhou');
    }

    $update = $pdo->prepare("UPDATE usuarios SET {$campo} = ? WHERE id = ?");
    $update->execute([$caminhoPublico, $usuarioId]);
    $_SESSION[$campo] = $caminhoPublico;

    if (is_string($arquivoAntigo) && str_starts_with($arquivoAntigo, '../uploads/perfis/')) {
        $antigoServidor = dirname(__DIR__) . '/' . ltrim($arquivoAntigo, './');
        if (is_file($antigoServidor) && realpath($antigoServidor) !== realpath($caminhoServidor)) {
            @unlink($antigoServidor);
        }
    }

    header('Location: ../front-end/perfil.php?sucesso=' . rawurlencode($campo));
    exit;
} catch (Throwable $e) {
    if (is_file($caminhoServidor)) {
        @unlink($caminhoServidor);
    }
    error_log('Erro ao atualizar ' . $campo . ': ' . $e->getMessage());
    voltarPerfil('indisponivel');
}
