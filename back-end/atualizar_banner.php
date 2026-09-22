<?php
session_start();
require_once __DIR__ . '/conexao.php';

if (!isset($_SESSION['id'])) { header('Location: ../front-end/login.php'); exit; }
function falharBanner(string $mensagem): never { echo '<script>alert(' . json_encode($mensagem, JSON_UNESCAPED_UNICODE) . '); window.location.href="../front-end/perfil.php";</script>'; exit; }

$diretorio = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'banners';
if (!is_dir($diretorio) && !@mkdir($diretorio, 0775, true)) falharBanner('Não foi possível criar uploads/banners.');
if (!is_writable($diretorio)) falharBanner('A pasta uploads/banners não tem permissão de escrita.');
$nome = 'banner_' . (int) $_SESSION['id'] . '_' . time() . '_' . bin2hex(random_bytes(3)) . '.jpg';
$caminhoFisico = $diretorio . DIRECTORY_SEPARATOR . $nome;
$caminhoWeb = '../uploads/banners/' . $nome;

try {
    $recorte = (string) ($_POST['banner_cortado'] ?? '');
    if (preg_match('#^data:image/jpeg;base64,#i', $recorte)) {
        $dados = base64_decode(substr($recorte, strpos($recorte, ',') + 1), true);
        if ($dados === false || strlen($dados) === 0 || strlen($dados) > 5 * 1024 * 1024) falharBanner('O banner editado ficou inválido ou muito grande.');
        if (@file_put_contents($caminhoFisico, $dados, LOCK_EX) === false) falharBanner('Não foi possível gravar o banner.');
    } else {
        $arquivo = $_FILES['banner'] ?? null;
        if (!$arquivo || ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) falharBanner('Selecione um banner antes de enviar.');
        if ($arquivo['size'] > 8 * 1024 * 1024 || !@getimagesize($arquivo['tmp_name'])) falharBanner('Escolha uma imagem válida de até 8 MB.');
        if (!@move_uploaded_file($arquivo['tmp_name'], $caminhoFisico)) falharBanner('Não foi possível gravar o banner.');
    }
    $info = @getimagesize($caminhoFisico);
    if (!$info || $info[0] / max(1, $info[1]) < 2.4 || $info[0] / max(1, $info[1]) > 3.6) { @unlink($caminhoFisico); falharBanner('O banner precisa estar no formato horizontal 3:1.'); }

    $stmt = $pdo->prepare('SELECT banner FROM usuarios WHERE id = ?'); $stmt->execute([$_SESSION['id']]); $antigo = $stmt->fetchColumn();
    $stmt = $pdo->prepare('UPDATE usuarios SET banner = ? WHERE id = ?'); $stmt->execute([$caminhoWeb, $_SESSION['id']]);
    if ($antigo && !preg_match('/^https?:\/\//i', $antigo)) { $arquivoAntigo = dirname(__DIR__) . '/' . ltrim(str_replace('../', '', $antigo), '/'); if (is_file($arquivoAntigo) && realpath($arquivoAntigo) !== realpath($caminhoFisico)) @unlink($arquivoAntigo); }
    header('Location: ../front-end/perfil.php?status=sucesso'); exit;
} catch (Throwable $e) {
    if (is_file($caminhoFisico)) @unlink($caminhoFisico);
    error_log('Falha no upload de banner: ' . $e->getMessage());
    falharBanner('Erro ao atualizar o banner. Verifique a coluna banner e as permissões da pasta.');
}
