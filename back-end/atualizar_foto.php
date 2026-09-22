<?php
session_start();
require_once __DIR__ . '/conexao.php';

if (!isset($_SESSION['id'])) {
    header('Location: ../front-end/login.php');
    exit;
}

function falharFoto(string $mensagem): never
{
    echo '<script>alert(' . json_encode($mensagem, JSON_UNESCAPED_UNICODE) . '); window.location.href="../front-end/perfil.php";</script>';
    exit;
}

$diretorio = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'perfis';
if (!is_dir($diretorio) && !@mkdir($diretorio, 0775, true)) {
    falharFoto('Não foi possível criar a pasta uploads/perfis. Verifique a permissão de escrita do XAMPP.');
}
if (!is_writable($diretorio)) {
    falharFoto('A pasta uploads/perfis não tem permissão de escrita.');
}

$novoNome = 'perfil_' . (int) $_SESSION['id'] . '_' . time() . '_' . bin2hex(random_bytes(3)) . '.jpg';
$caminhoFisico = $diretorio . DIRECTORY_SEPARATOR . $novoNome;
$caminhoWeb = '../uploads/perfis/' . $novoNome;

try {
    $imagemSalva = false;
    $recorte = (string) ($_POST['foto_cortada'] ?? '');
    if (preg_match('#^data:image/jpeg;base64,#i', $recorte)) {
        $dados = base64_decode(substr($recorte, strpos($recorte, ',') + 1), true);
        if ($dados === false || strlen($dados) === 0 || strlen($dados) > 5 * 1024 * 1024) {
            falharFoto('A imagem editada ficou inválida ou muito grande.');
        }
        if (@file_put_contents($caminhoFisico, $dados, LOCK_EX) === false) {
            falharFoto('Não foi possível gravar a foto. Verifique a permissão de uploads/perfis.');
        }
        $informacoes = @getimagesize($caminhoFisico);
        if (!$informacoes || $informacoes[0] !== $informacoes[1] || ($informacoes['mime'] ?? '') !== 'image/jpeg') {
            @unlink($caminhoFisico);
            falharFoto('A foto precisa ser um JPEG quadrado.');
        }
        $imagemSalva = true;
    } elseif (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $arquivo = $_FILES['foto'];
        if ($arquivo['size'] > 8 * 1024 * 1024 || !@getimagesize($arquivo['tmp_name'])) {
            falharFoto('A imagem enviada não é válida ou excede 8 MB.');
        }
        $imagemSalva = @move_uploaded_file($arquivo['tmp_name'], $caminhoFisico);
    }
    if (!$imagemSalva || !is_file($caminhoFisico)) falharFoto('Selecione uma imagem e confirme o recorte.');

    $stmt = $pdo->prepare('SELECT foto FROM usuarios WHERE id = ?');
    $stmt->execute([$_SESSION['id']]);
    $antiga = $stmt->fetchColumn();
    $stmt = $pdo->prepare('UPDATE usuarios SET foto = ? WHERE id = ?');
    $stmt->execute([$caminhoWeb, $_SESSION['id']]);
    $_SESSION['foto'] = $caminhoWeb;

    if ($antiga && !preg_match('/^https?:\/\//i', $antiga)) {
        $arquivoAntigo = dirname(__DIR__) . '/' . ltrim(str_replace('../', '', $antiga), '/');
        if (is_file($arquivoAntigo) && realpath($arquivoAntigo) !== realpath($caminhoFisico)) @unlink($arquivoAntigo);
    }
    header('Location: ../front-end/perfil.php?status=sucesso');
    exit;
} catch (Throwable $e) {
    if (is_file($caminhoFisico)) @unlink($caminhoFisico);
    error_log('Falha no upload de foto: ' . $e->getMessage());
    falharFoto('Erro ao atualizar a foto. Confira a permissão da pasta uploads/perfis e tente novamente.');
}
