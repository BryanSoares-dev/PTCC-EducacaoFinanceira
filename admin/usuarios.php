<?php
require_once 'admin_auth.php';
require_once __DIR__ . '/../back-end/admin_api_token.php';
$paginaAtual = 'usuarios';
$apiUrl = rtrim(afdeConfig('ADMIN_API_URL', 'http://127.0.0.1:5001'), '/');
$apiToken = afde_admin_api_token($adminLogadoId);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contas | Admin AFDE</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <link rel="stylesheet" href="../css/liquid-glass.css?v=103">
    <style>
        .crud-toolbar{display:flex;gap:12px;align-items:center;justify-content:space-between;flex-wrap:wrap;margin:20px 0}
        .crud-toolbar .filtros{display:flex;gap:8px;flex:1;max-width:720px;margin:0}
        .crud-toolbar input,.crud-toolbar select,.crud-form input,.crud-form select{min-height:42px;padding:10px 12px;border:1px solid rgba(255,255,255,.18);border-radius:10px;background:rgba(11,19,32,.7);color:inherit}
        .crud-toolbar input{flex:1}.btn-primario{background:#ffad52;color:#17120b;border:0}.btn-primario:hover{filter:brightness(1.08)}
        .crud-form{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;align-items:end;padding:18px;margin-bottom:20px;border:1px solid rgba(255,255,255,.12);border-radius:14px;background:rgba(255,255,255,.04)}
        .crud-form label{display:flex;flex-direction:column;gap:6px;font-size:.84rem;color:var(--texto-fraco)}.crud-form .acoes-form{display:flex;gap:8px}.crud-form .campo-senha{grid-column:span 2}
        .crud-msg{display:none;margin:14px 0;padding:12px 14px;border-radius:10px}.crud-msg.visivel{display:block}.crud-msg.sucesso{background:rgba(75,190,125,.16);color:#9ef0bb}.crud-msg.erro{background:rgba(230,80,80,.16);color:#ffadad}
        .tipo-tabs{display:flex;gap:8px}.tipo-tabs button.ativo{background:#ffad52;color:#17120b}.tipo-tabs button{cursor:pointer}
        .btn-sm{cursor:pointer}.btn-editar{background:rgba(90,160,255,.2);color:#b9d7ff;border:0}.btn-perigo{border:0}.vazio{padding:25px;text-align:center;color:var(--texto-fraco)}
        @media(max-width:900px){.crud-form{grid-template-columns:repeat(2,minmax(0,1fr))}.crud-form .campo-senha{grid-column:span 1}}
        @media(max-width:560px){.crud-form{grid-template-columns:1fr}.crud-form .campo-senha{grid-column:auto}}
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<main class="admin-content">
    <div class="admin-topo">
        <div><h1>Contas</h1><p id="contador">Carregando contas...</p></div>
        <a class="btn btn-secundario" href="<?= htmlspecialchars($apiUrl) ?>/docs" target="_blank" rel="noopener"><i class="fas fa-book"></i> Swagger da API</a>
    </div>

    <div id="mensagem" class="crud-msg" role="status"></div>
    <form id="form-conta" class="crud-form" autocomplete="off">
        <input type="hidden" id="conta-id">
        <label>Nome<input id="nome" name="nome" required minlength="2" maxlength="255"></label>
        <label>E-mail<input id="email" name="email" type="email" required maxlength="190"></label>
        <label>Telefone<input id="telefone" name="telefone" maxlength="25"></label>
        <label>Tipo<select id="tipo" name="tipo"><option value="usuario">Usuário comum</option><option value="admin">Administrador</option></select></label>
        <label class="campo-senha">Senha <small>(obrigatória ao criar; deixe vazia para manter)</small><input id="senha" name="senha" type="password" minlength="8"></label>
        <div class="acoes-form"><button class="btn btn-primario" type="submit" id="salvar"><i class="fas fa-plus"></i> Criar conta</button><button class="btn btn-secundario" type="button" id="cancelar" hidden>Cancelar</button></div>
    </form>

    <div class="crud-toolbar">
        <div class="tipo-tabs" role="group" aria-label="Filtro de tipo"><button class="btn btn-secundario ativo" data-tipo="">Todas</button><button class="btn btn-secundario" data-tipo="admin">Administradores</button><button class="btn btn-secundario" data-tipo="usuario">Usuários comuns</button></div>
        <form id="busca-form" class="filtros"><input id="busca" placeholder="Pesquisar por nome ou e-mail"><button class="btn btn-secundario" type="submit">Pesquisar</button></form>
    </div>

    <div class="tabela-wrap"><table><thead><tr><th>ID</th><th>Nome</th><th>E-mail</th><th>Telefone</th><th>Tipo</th><th>Provedor</th><th>Cadastro</th><th>Ações</th></tr></thead><tbody id="tabela-corpo"><tr><td colspan="8" class="vazio">Carregando...</td></tr></tbody></table></div>
</main>
<script>
const API_URL = <?= json_encode($apiUrl, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
const API_TOKEN = <?= json_encode($apiToken) ?>;
const ADMIN_ID = <?= (int)$adminLogadoId ?>;
let filtroTipo = '';
const $ = (id) => document.getElementById(id);
function mostrarMensagem(texto, tipo='sucesso'){ const el=$('mensagem'); el.textContent=texto; el.className='crud-msg visivel '+tipo; window.scrollTo({top:0,behavior:'smooth'}); }
async function api(path, options={}) { const response=await fetch(API_URL+path,{...options,headers:{'Content-Type':'application/json','Authorization':'Bearer '+API_TOKEN,...(options.headers||{})}}); const body=await response.json().catch(()=>({message:'Resposta inválida da API.'})); if(!response.ok) throw new Error(body.message||'A API recusou a operação.'); return body; }
function esc(value){return String(value??'').replace(/[&<>'"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[c]));}
async function carregar(){ const params=new URLSearchParams(); if(filtroTipo)params.set('tipo',filtroTipo); if($('busca').value.trim())params.set('busca',$('busca').value.trim()); const corpo=$('tabela-corpo'); try { const resposta=await api('/api/admin/users?'+params); const contas=resposta.data||[]; $('contador').textContent=contas.length+' conta(s) encontrada(s).'; corpo.innerHTML=contas.length?contas.map(u=>`<tr><td>#${u.id}</td><td>${esc(u.nome)}</td><td>${esc(u.email)}</td><td>${esc(u.telefone||'—')}</td><td><span class="badge ${esc(u.tipo)}">${u.tipo==='admin'?'Administrador':'Usuário'}</span></td><td>${esc(u.provedor)}</td><td>${esc(new Date(u.data_criacao).toLocaleDateString('pt-BR'))}</td><td><button class="btn btn-sm btn-editar" data-editar='${JSON.stringify(u).replace(/'/g,'&#039;')}'><i class="fas fa-pen"></i> Editar</button> <button class="btn btn-perigo btn-sm" data-excluir="${u.id}" data-nome="${esc(u.nome)}" ${u.id===ADMIN_ID?'disabled title="Sua própria conta não pode ser excluída"':''}><i class="fas fa-trash"></i> Excluir</button></td></tr>`).join(''):'<tr><td colspan="8" class="vazio">Nenhuma conta encontrada.</td></tr>'; } catch(error){ corpo.innerHTML='<tr><td colspan="8" class="vazio">Não foi possível carregar a API Flask.</td></tr>'; mostrarMensagem(error.message,'erro'); } }
function limparForm(){ $('form-conta').reset(); $('conta-id').value=''; $('tipo').value='usuario'; $('salvar').innerHTML='<i class="fas fa-plus"></i> Criar conta'; $('cancelar').hidden=true; }
$('form-conta').addEventListener('submit',async event=>{event.preventDefault(); const id=$('conta-id').value; const payload={nome:$('nome').value.trim(),email:$('email').value.trim(),telefone:$('telefone').value.trim(),tipo:$('tipo').value}; if($('senha').value)payload.senha=$('senha').value; if(!id&&!payload.senha){mostrarMensagem('Informe uma senha com pelo menos 8 caracteres para criar a conta.','erro');return;} try{await api(id?'/api/admin/users/'+id:'/api/admin/users',{method:id?'PUT':'POST',body:JSON.stringify(payload)}); mostrarMensagem(id?'Conta atualizada com sucesso.':'Conta criada com sucesso.'); limparForm(); carregar();}catch(error){mostrarMensagem(error.message,'erro');}});
$('cancelar').addEventListener('click',limparForm); $('busca-form').addEventListener('submit',e=>{e.preventDefault();carregar();});
document.querySelectorAll('[data-tipo]').forEach(button=>button.addEventListener('click',()=>{document.querySelectorAll('[data-tipo]').forEach(b=>b.classList.remove('ativo'));button.classList.add('ativo');filtroTipo=button.dataset.tipo;carregar();}));
$('tabela-corpo').addEventListener('click',async event=>{const editar=event.target.closest('[data-editar]'); const excluir=event.target.closest('[data-excluir]'); if(editar){const u=JSON.parse(editar.dataset.editar);$('conta-id').value=u.id;$('nome').value=u.nome;$('email').value=u.email;$('telefone').value=u.telefone||'';$('tipo').value=u.tipo;$('senha').value='';$('salvar').innerHTML='<i class="fas fa-save"></i> Salvar alterações';$('cancelar').hidden=false;$('nome').focus();return;} if(excluir&&!excluir.disabled&&confirm('Excluir a conta de '+excluir.dataset.nome+'? Esta ação não pode ser desfeita.')){try{await api('/api/admin/users/'+excluir.dataset.excluir,{method:'DELETE'});mostrarMensagem('Conta excluída com sucesso.');carregar();}catch(error){mostrarMensagem(error.message,'erro');}}});
carregar();
</script>
<script src="../JS/acessibilidade.js" defer></script><script src="../JS/liquid-glass.js?v=99" defer></script>
</body>
</html>
