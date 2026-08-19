<?php
require_once 'admin_auth.php';
$paginaAtual = 'usuarios';

$mensagem = '';
$tipoMensagem = '';

// ===================== EXCLUIR USUÁRIO =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_id'])) {
    $idExcluir = (int) $_POST['excluir_id'];

    if ($idExcluir === $adminLogadoId) {
        $mensagem = 'Você não pode excluir a sua própria conta de administrador.';
        $tipoMensagem = 'erro';
    } else {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$idExcluir]);
        $mensagem = 'Usuário excluído com sucesso.';
        $tipoMensagem = 'sucesso';
    }
}

// ===================== BUSCA =====================
$busca = trim($_GET['busca'] ?? '');

if ($busca !== '') {
    $stmt = $pdo->prepare(
        "SELECT id, nome, email, telefone, provedor, tipo, data_criacao
         FROM usuarios
         WHERE nome LIKE ? OR email LIKE ?
         ORDER BY data_criacao DESC"
    );
    $termo = '%' . $busca . '%';
    $stmt->execute([$termo, $termo]);
} else {
    $stmt = $pdo->query(
        "SELECT id, nome, email, telefone, provedor, tipo, data_criacao
         FROM usuarios ORDER BY data_criacao DESC"
    );
}
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários | Admin FinControl</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="../img/favicon.png">
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="admin-content">
    <div class="admin-topo">
        <div>
            <h1>Usuários</h1>
            <p><?= count($usuarios) ?> usuário(s) encontrado(s).</p>
        </div>
    </div>

    <?php if ($mensagem): ?>
        <div class="alerta <?= $tipoMensagem ?>"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <form method="GET" class="filtros">
        <input type="text" name="busca" placeholder="Pesquisar por nome ou e-mail..." value="<?= htmlspecialchars($busca) ?>">
        <button type="submit" class="btn btn-secundario">Pesquisar</button>
        <?php if ($busca !== ''): ?>
            <a href="usuarios.php" class="btn btn-secundario">Limpar</a>
        <?php endif; ?>
    </form>

    <div class="tabela-wrap">
        <?php if (empty($usuarios)): ?>
            <p class="vazio">Nenhum usuário encontrado.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Telefone</th>
                    <th>Provedor</th>
                    <th>Tipo</th>
                    <th>Cadastrado em</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td>#<?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['nome']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['telefone'] ?: '—') ?></td>
                    <td><?= htmlspecialchars($u['provedor'] ?? 'local') ?></td>
                    <td><span class="badge <?= $u['tipo'] ?>"><?= htmlspecialchars($u['tipo']) ?></span></td>
                    <td><?= date('d/m/Y', strtotime($u['data_criacao'])) ?></td>
                    <td>
                        <?php if ((int)$u['id'] === $adminLogadoId): ?>
                            <span style="color:var(--texto-fraco); font-size:0.8rem;">Sua conta</span>
                        <?php else: ?>
                            <form method="POST" onsubmit="return confirm('Tem certeza que deseja excluir o usuário <?= htmlspecialchars(addslashes($u['nome'])) ?>? Esta ação não pode ser desfeita.');" style="display:inline;">
                                <input type="hidden" name="excluir_id" value="<?= $u['id'] ?>">
                                <button type="submit" class="btn btn-perigo btn-sm">
                                    <i class="fas fa-trash"></i> Excluir
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</main>

</body>
</html>