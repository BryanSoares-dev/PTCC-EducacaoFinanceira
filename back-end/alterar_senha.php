<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$senhaAtual = $_POST['senha_atual'];
$novaSenha = $_POST['nova_senha'];
$confirmarSenha = $_POST['confirmar_senha'];

// Busca a senha atual do usuário
$stmt = $pdo->prepare("
    SELECT senha
    FROM usuarios
    WHERE id = ?
");

$stmt->execute([$_SESSION['id']]);

$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se a senha atual está correta
if (!password_verify($senhaAtual, $usuario['senha'])) {

    echo "
    <script>
        alert('A senha atual está incorreta!');
        window.location='perfil.php';
    </script>
    ";

    exit;
}

// Verifica confirmação da nova senha
if ($novaSenha != $confirmarSenha) {

    echo "
    <script>
        alert('As novas senhas não coincidem!');
        window.location='perfil.php';
    </script>
    ";

    exit;
}

// Gera novo hash
$novaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

// Atualiza a senha
$stmt = $pdo->prepare("
    UPDATE usuarios
    SET senha = ?
    WHERE id = ?
");

$stmt->execute([
    $novaHash,
    $_SESSION['id']
]);

echo "
<script>
    alert('Senha alterada com sucesso!');
    window.location='perfil.php';
</script>
";
?>