<?php
session_start();
require_once '../back-end/conexao.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$usuarioId = (int) $_SESSION['id'];
$resumo = $pdo->prepare("SELECT
    COALESCE(SUM(CASE WHEN tipo = 'entrada' THEN valor ELSE -valor END), 0) AS saldo,
    COALESCE(SUM(CASE WHEN tipo = 'entrada' AND MONTH(data_criacao) = MONTH(CURDATE()) AND YEAR(data_criacao) = YEAR(CURDATE()) THEN valor ELSE 0 END), 0) AS receitas_mes,
    COALESCE(SUM(CASE WHEN tipo = 'saida' AND MONTH(data_criacao) = MONTH(CURDATE()) AND YEAR(data_criacao) = YEAR(CURDATE()) THEN valor ELSE 0 END), 0) AS despesas_mes
    FROM movimentacoes WHERE usuario_id = ?");
$resumo->execute([$usuarioId]);
$dadosResumo = $resumo->fetch(PDO::FETCH_ASSOC) ?: [];
$saldo = (float) ($dadosResumo['saldo'] ?? 0);
$receitasMes = (float) ($dadosResumo['receitas_mes'] ?? 0);
$despesasMes = (float) ($dadosResumo['despesas_mes'] ?? 0);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carteira financeira | FinControl</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/carteira.css">
    <link rel="icon" type="image/png" href="../img/favicon.png">
</head>
<body>
<?php include_once 'navbar.php'; ?>

<main class="main">
    <section class="carteira-hero">
        <div>
            <span class="carteira-tag">Visão geral</span>
            <h1>Sua carteira financeira</h1>
            <p>Acompanhe seu saldo, organize movimentações e consulte os dados da sua conta conectada.</p>
        </div>
        <div class="hero-saldo">
            <span>Saldo disponível</span>
            <strong>R$ <?= number_format($saldo, 2, ',', '.') ?></strong>
        </div>
    </section>

    <section class="cards" aria-label="Resumo financeiro">
        <article class="card"><h4>Saldo atual</h4><p class="green">R$ <?= number_format($saldo, 2, ',', '.') ?></p></article>
        <article class="card"><h4>Receitas do mês</h4><p class="green">+ R$ <?= number_format($receitasMes, 2, ',', '.') ?></p></article>
        <article class="card"><h4>Despesas do mês</h4><p class="red">- R$ <?= number_format($despesasMes, 2, ',', '.') ?></p></article>
        <article class="card"><h4>Resultado do mês</h4><p class="<?= $receitasMes >= $despesasMes ? 'green' : 'red' ?>">R$ <?= number_format($receitasMes - $despesasMes, 2, ',', '.') ?></p></article>
    </section>

    <section class="controle-financeiro">
        <div class="section-header">
            <div><h2>Controle financeiro</h2><p>Cadastre receitas e despesas para acompanhar suas finanças.</p></div>
            <div class="carteira-acoes">
                <button type="button" class="btn-movimentacao" onclick="abrirModal()">Nova movimentação</button>
                <button type="button" class="btn-movimentacao" id="pluggyConnectButton">Conectar conta bancária</button>
            </div>
        </div>
    </section>

    <section class="dados-open-finance" aria-live="polite" aria-busy="true">
        <div class="open-finance-cabecalho">
            <div>
                <h2>Dados da conta conectada</h2>
                <p id="openFinanceStatus">Verificando sua conta conectada…</p>
            </div>
            <button type="button" class="btn-atualizar" id="atualizarOpenFinance" hidden>Atualizar dados</button>
        </div>
        <div class="open-finance-grid">
            <article class="open-finance-bloco transacoes-bloco"><div class="titulo-open-finance"><h3>Transações</h3><span id="totalTransacoes">0</span></div><div id="listaTransacoes" class="lista-open-finance"></div></article>
            <article class="open-finance-bloco emprestimos-bloco"><div class="titulo-open-finance"><h3>Empréstimos</h3><span id="totalEmprestimos">0</span></div><div id="listaEmprestimos" class="lista-open-finance"></div></article>
            <article class="open-finance-bloco investimentos-bloco"><div class="titulo-open-finance"><h3>Investimentos</h3><span id="totalInvestimentos">0</span></div><div id="listaInvestimentos" class="lista-open-finance"></div></article>
        </div>
    </section>
</main>

<div id="modalMovimentacao" class="modal" role="dialog" aria-modal="true" aria-labelledby="tituloMovimentacao">
    <div class="modal-content">
        <div class="modal-header"><h2 id="tituloMovimentacao">Nova movimentação</h2><button type="button" class="close-modal" onclick="fecharModal()" aria-label="Fechar">×</button></div>
        <form action="../back-end/processa_movimentacao.php" method="POST">
            <div class="form-group"><label for="valor">Valor</label><input id="valor" type="number" step="0.01" min="0.01" name="valor" required></div>
            <div class="form-group"><label for="tipoSelect">Tipo</label><select name="tipo" id="tipoSelect" required><option value="" selected disabled>Selecione</option><option value="entrada">Entrada</option><option value="saida">Saída</option></select></div>
            <div class="form-group"><label for="categoriaSelect">Categoria</label><select name="categoria" id="categoriaSelect" required disabled><option value="" selected disabled>Selecione o tipo primeiro</option></select></div>
            <div class="form-group"><label for="descricao">Descrição</label><input id="descricao" type="text" name="descricao" placeholder="Ex.: Compra do mês" required></div>
            <button type="submit" class="btn-salvar">Salvar movimentação</button>
        </form>
    </div>
</div>

<script src="https://cdn.pluggy.ai/pluggy-connect/latest/pluggy-connect.js"></script>
<script>
const categorias = { entrada: ['Salário', 'Freelance', 'Investimentos', 'Presente', 'Outros'], saida: ['Alimentação', 'Transporte', 'Moradia', 'Saúde', 'Educação', 'Lazer', 'Outros'] };
const modal = document.getElementById('modalMovimentacao');
const tipoSelect = document.getElementById('tipoSelect');
const categoriaSelect = document.getElementById('categoriaSelect');
const statusOpenFinance = document.getElementById('openFinanceStatus');
const botaoAtualizar = document.getElementById('atualizarOpenFinance');
const secaoOpenFinance = document.querySelector('.dados-open-finance');
const moeda = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });
const escapar = valor => String(valor ?? '').replace(/[&<>'"]/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[char]));

function abrirModal() { modal.classList.add('active'); document.body.style.overflow = 'hidden'; }
function fecharModal() { modal.classList.remove('active'); document.body.style.overflow = ''; modal.querySelector('form').reset(); atualizarCategorias(); }
function atualizarCategorias() {
    const itens = categorias[tipoSelect.value] || [];
    categoriaSelect.disabled = !itens.length;
    categoriaSelect.innerHTML = itens.length ? '<option value="" selected disabled>Selecione uma categoria</option>' : '<option value="" selected disabled>Selecione o tipo primeiro</option>';
    itens.forEach(item => categoriaSelect.add(new Option(item, item)));
}

function valorDoItem(item, tipo) {
    if (tipo === 'investimentos') return item.amountCurrent ?? item.currentAmount ?? item.amount ?? item.value ?? item.balance ?? 0;
    if (tipo === 'emprestimos') return item.balance ?? item.outstandingBalance ?? item.amount ?? item.currentAmount ?? 0;
    return item.amount ?? 0;
}
function nomeDoItem(item, tipo) {
    return item.description || item.name || item.marketingName || item.type || (tipo === 'emprestimos' ? 'Empréstimo' : tipo === 'investimentos' ? 'Investimento' : 'Transação sem descrição');
}
function detalheDoItem(item, tipo) {
    if (tipo === 'transacoes') return item.contaNome || item.category || item.type || 'Conta conectada';
    return item.institution?.name || item.institution || item.issuer || item.type || item.status || 'Conta conectada';
}
function dataDoItem(item) { return item.date || item.dueDate || item.createdAt || item.updatedAt || item.issueDate || null; }
function formatarData(data) { return data ? new Intl.DateTimeFormat('pt-BR').format(new Date(data.includes('T') ? data : data + 'T12:00:00')) : 'Atualizado agora'; }

function renderizarLista(elemento, itens, tipo) {
    document.getElementById('total' + tipo.charAt(0).toUpperCase() + tipo.slice(1)).textContent = itens.length;
    if (!itens.length) { elemento.innerHTML = '<p class="sem-dados">Nenhum dado disponível para esta conta.</p>'; return; }
    const icones = { transacoes: '↕', emprestimos: '¤', investimentos: '↗' };
    elemento.innerHTML = itens.slice(0, 10).map(item => {
        const valor = Number(valorDoItem(item, tipo)) || 0;
        const classe = tipo === 'transacoes' && (item.type === 'DEBIT' || valor < 0) ? 'valor-negativo' : 'valor-positivo';
        return `<div class="item-open-finance ${tipo}"><div class="icone-open-finance">${icones[tipo]}</div><div class="descricao-open-finance"><strong>${escapar(nomeDoItem(item, tipo))}</strong><small>${escapar(detalheDoItem(item, tipo))} · ${escapar(formatarData(dataDoItem(item)))}</small></div><span class="${classe}">${moeda.format(Math.abs(valor))}</span></div>`;
    }).join('');
}
function renderizarVazio(mensagem) { ['Transacoes', 'Emprestimos', 'Investimentos'].forEach(tipo => { document.getElementById('lista' + tipo).innerHTML = `<p class="sem-dados">${escapar(mensagem)}</p>`; document.getElementById('total' + tipo).textContent = '0'; }); }

async function carregarDadosOpenFinance() {
    secaoOpenFinance.setAttribute('aria-busy', 'true');
    statusOpenFinance.textContent = 'Atualizando os dados da sua conta conectada…';
    try {
        const response = await fetch('../open-finance/dados.php', { credentials: 'same-origin', cache: 'no-store' });
        const dados = await response.json();
        if (!response.ok) throw new Error(dados.error || 'Não foi possível consultar os dados bancários.');
        if (!dados.connected) { renderizarVazio('Conecte uma conta bancária para visualizar os dados.'); statusOpenFinance.textContent = 'Nenhuma conta bancária conectada.'; return; }
        renderizarLista(document.getElementById('listaTransacoes'), dados.transacoes || [], 'transacoes');
        renderizarLista(document.getElementById('listaEmprestimos'), dados.emprestimos || [], 'emprestimos');
        renderizarLista(document.getElementById('listaInvestimentos'), dados.investimentos || [], 'investimentos');
        statusOpenFinance.textContent = dados.avisos?.length ? dados.avisos.join(' ') : 'Dados atualizados da sua conta conectada.';
    } catch (error) { renderizarVazio('Não foi possível carregar os dados agora.'); statusOpenFinance.textContent = error.message; }
    finally { secaoOpenFinance.setAttribute('aria-busy', 'false'); botaoAtualizar.hidden = false; }
}

async function conectarConta() {
    const botao = document.getElementById('pluggyConnectButton');
    botao.disabled = true; botao.textContent = 'Preparando conexão…';
    try {
        const response = await fetch('../open-finance/connect-token.php', { method: 'POST', credentials: 'same-origin' });
        const dados = await response.json();
        if (!response.ok || !dados.accessToken) throw new Error(dados.error || 'Não foi possível iniciar a conexão.');
        if (typeof window.PluggyConnect !== 'function') throw new Error('O serviço de conexão bancária não foi carregado.');
        new window.PluggyConnect({ connectToken: dados.accessToken, includeSandbox: true, onSuccess: async resultado => {
            try {
                const itemId = resultado?.item?.id;
                if (!itemId) throw new Error('A conexão foi concluída, mas não retornou uma conta válida.');
                const salvar = await fetch('../open-finance/item.php', { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ itemId }) });
                const salvo = await salvar.json();
                if (!salvar.ok || !salvo.success) throw new Error(salvo.error || 'Não foi possível salvar a conta conectada.');
                await carregarDadosOpenFinance();
            } catch (error) { statusOpenFinance.textContent = error.message; }
        }, onError: erro => { statusOpenFinance.textContent = erro?.message || 'A conexão bancária foi cancelada ou falhou.'; } }).init();
    } catch (error) { statusOpenFinance.textContent = error.message; }
    finally { botao.disabled = false; botao.textContent = 'Conectar conta bancária'; }
}

tipoSelect.addEventListener('change', atualizarCategorias);
modal.addEventListener('click', event => { if (event.target === modal) fecharModal(); });
document.addEventListener('keydown', event => { if (event.key === 'Escape' && modal.classList.contains('active')) fecharModal(); });
botaoAtualizar.addEventListener('click', carregarDadosOpenFinance);
document.getElementById('pluggyConnectButton').addEventListener('click', conectarConta);
carregarDadosOpenFinance();
</script>
</body>
</html>
