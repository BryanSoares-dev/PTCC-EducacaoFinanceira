<?php
require_once 'admin_auth.php';
$paginaAtual = 'usuarios-comuns';
$apiUrl = rtrim(afdeConfig('ADMIN_API_URL', 'http://127.0.0.1:5001'), '/');
$apiToken = token_api_admin($adminLogadoId);
$apiConfigurada = $apiToken !== '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários comuns | Admin AFDE</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <link rel="stylesheet" href="../css/liquid-glass.css?v=103">
</head>
<body>
<?php include 'sidebar.php'; ?>
<main class="admin-content">
    <div class="admin-topo">
        <div>
            <h1>Usuários comuns</h1>
            <p>Crie, edite, visualize e remova contas sem acesso administrativo.</p>
        </div>
        <button class="btn btn-primario" type="button" id="novoUsuario"><i class="fas fa-plus"></i> Novo usuário</button>
    </div>

    <?php if (!$apiConfigurada): ?>
        <div class="alerta erro">Defina <strong>ADMIN_API_SECRET</strong> no arquivo .env para habilitar as chamadas seguras da API Flask.</div>
    <?php endif; ?>
    <div class="alerta" id="feedback" hidden></div>

    <div class="tabela-wrap">
        <table>
            <thead><tr><th>ID</th><th>Nome</th><th>E-mail</th><th>Telefone</th><th>Provedor</th><th>Criado em</th><th>Ações</th></tr></thead>
            <tbody id="usersBody"><tr><td colspan="7" class="vazio">Carregando usuários...</td></tr></tbody>
        </table>
    </div>

    <div id="adminModal" class="admin-modal" hidden>
        <div class="form-card admin-modal-card" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
            <button type="button" class="modal-fechar" id="fecharModal" aria-label="Fechar">&times;</button>
            <h2 id="modalTitle">Novo usuário</h2>
            <p class="modal-ajuda">A senha deve ter entre 8 e 72 caracteres.</p>
            <form id="adminForm">
                <input type="hidden" id="userId">
                <div class="form-group"><label for="nome">Nome</label><input id="nome" name="nome" required maxlength="120"></div>
                <div class="form-group"><label for="email">E-mail</label><input id="email" name="email" type="email" required maxlength="180"></div>
                <div class="form-group"><label for="telefone">Telefone</label><input id="telefone" name="telefone" maxlength="30"></div>
                <div class="form-group"><label for="senha">Senha <span id="senhaOpcional">(obrigatória)</span></label><input id="senha" name="senha" type="password" minlength="8" maxlength="72" autocomplete="new-password"></div>
                <div class="form-acoes"><button type="submit" class="btn btn-primario">Salvar</button><button type="button" class="btn btn-secundario" id="cancelarModal">Cancelar</button></div>
            </form>
        </div>
    </div>
</main>
<script>
const API_URL = <?= json_encode($apiUrl, JSON_UNESCAPED_SLASHES) ?>;
const API_TOKEN = <?= json_encode($apiToken) ?>;
const body = document.getElementById('usersBody');
const feedback = document.getElementById('feedback');
const modal = document.getElementById('adminModal');
const form = document.getElementById('adminForm');
const esc = value => String(value ?? '').replace(/[&<>'"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[c]));
function aviso(text, error = false) { feedback.hidden = !text; feedback.className = `alerta ${error ? 'erro' : 'sucesso'}`; feedback.textContent = text; }
async function api(path = '', options = {}) {
    const response = await fetch(`${API_URL}${path}`, { ...options, headers: {'Authorization': `Bearer ${API_TOKEN}`, 'Content-Type': 'application/json', ...(options.headers || {})} });
    const data = await response.json().catch(() => ({}));
    if (!response.ok) throw new Error((data.erros || []).join(' ') || data.erro || 'A API não respondeu corretamente.');
    return data;
}
function render(items) {
    if (!items.length) { body.innerHTML = '<tr><td colspan="7" class="vazio">Nenhum usuário cadastrado.</td></tr>'; return; }
    body.innerHTML = items.map(item => `<tr><td>#${item.id}</td><td>${esc(item.nome)}</td><td>${esc(item.email)}</td><td>${esc(item.telefone || '—')}</td><td>${esc(item.provedor)}</td><td>${item.data_criacao ? new Date(item.data_criacao.replace(' ', 'T')).toLocaleDateString('pt-BR') : '—'}</td><td><button class="btn btn-secundario btn-sm editar" data-item='${JSON.stringify(item).replace(/'/g, '&#39;')}'><i class="fas fa-pen"></i> Editar</button> <button class="btn btn-perigo btn-sm excluir" data-id="${item.id}" data-nome="${esc(item.nome)}"><i class="fas fa-trash"></i> Excluir</button></td></tr>`).join('');
    document.querySelectorAll('.editar').forEach(button => button.addEventListener('click', () => abrirModal(JSON.parse(button.dataset.item))));
    document.querySelectorAll('.excluir').forEach(button => button.addEventListener('click', () => excluir(button.dataset.id, button.dataset.nome)));
}
async function carregar() { try { const result = await api('/api/users'); render(result.dados || []); } catch (error) { body.innerHTML = `<tr><td colspan="7" class="vazio">${esc(error.message)}</td></tr>`; } }
function abrirModal(item = null) { form.reset(); document.getElementById('userId').value = item?.id || ''; document.getElementById('modalTitle').textContent = item ? 'Editar usuário' : 'Novo usuário'; document.getElementById('senhaOpcional').textContent = item ? '(opcional)' : '(obrigatória)'; document.getElementById('nome').value = item?.nome || ''; document.getElementById('email').value = item?.email || ''; document.getElementById('telefone').value = item?.telefone || ''; document.getElementById('senha').required = !item; modal.hidden = false; document.getElementById('nome').focus(); }
function fecharModal() { modal.hidden = true; }
async function excluir(id, nome) { if (!confirm(`Excluir o usuário ${nome}? Esta ação não pode ser desfeita.`)) return; try { const result = await api(`/api/users/${id}`, {method:'DELETE'}); aviso(result.mensagem); await carregar(); } catch (error) { aviso(error.message, true); } }
form.addEventListener('submit', async event => { event.preventDefault(); const id = document.getElementById('userId').value; const payload = Object.fromEntries(new FormData(form).entries()); delete payload.adminId; if (!payload.senha) delete payload.senha; try { const result = await api(id ? `/api/users/${id}` : '/api/users', {method: id ? 'PUT' : 'POST', body: JSON.stringify(payload)}); fecharModal(); aviso(result.mensagem); await carregar(); } catch (error) { aviso(error.message, true); } });
document.getElementById('novoUsuario').addEventListener('click', () => abrirModal()); document.getElementById('fecharModal').addEventListener('click', fecharModal); document.getElementById('cancelarModal').addEventListener('click', fecharModal); modal.addEventListener('click', e => { if (e.target === modal) fecharModal(); }); carregar();
</script>
<style>
.admin-modal{position:fixed;inset:0;background:rgba(0,0,0,.65);display:grid;place-items:center;padding:20px;z-index:10}.admin-modal[hidden]{display:none}.admin-modal-card{position:relative;width:min(100%,560px);background:#10315a}.admin-modal-card h2{margin-bottom:6px}.modal-ajuda{color:var(--texto-fraco);font-size:.85rem;margin-bottom:20px}.modal-fechar{position:absolute;right:20px;top:15px;background:none;border:0;color:var(--texto-fraco);font-size:1.8rem;cursor:pointer}.form-group input{background:rgba(0,0,0,.16)}
</style>
<script src="../JS/acessibilidade.js" defer></script><script src="../JS/liquid-glass.js?v=99" defer></script>
</body>
</html>
