<?php
// A ponte PHP mantém a chamada no mesmo domínio e encaminha para a API interna.
$cbiaApiUrl = '../back-end/chatbot.php';
?>
<!-- ===== Widget de chat com IA (inclua este arquivo em todas as páginas) ===== -->
<style>
  #cbia-botao, #cbia-janela, #cbia-janela * { box-sizing: border-box; }

  /* Botão flutuante com o robô */
  #cbia-botao {
    position: fixed;
    right: 20px;
    bottom: 100px; /* acima do ícone de acessibilidade; ajuste se precisar */
    width: 62px;
    height: 62px;
    border: 0;
    border-radius: 50%;
    background: #0f766e;
    color: #fff;
    cursor: pointer;
    box-shadow: 0 6px 18px rgba(0, 0, 0, .28);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 99998;
    transition: transform .2s ease, background .2s ease;
  }
  #cbia-botao:hover { transform: scale(1.08); background: #115e59; }
  #cbia-botao:focus-visible { outline: 3px solid #5eead4; outline-offset: 3px; }
  #cbia-botao svg { width: 36px; height: 36px; }

  /* Janela do chat */
  #cbia-janela {
    position: fixed;
    right: 20px;
    bottom: 174px;
    width: 360px;
    max-width: calc(100vw - 24px);
    height: 520px;
    max-height: calc(100vh - 200px);
    display: none;
    flex-direction: column;
    background: #fff;
    color: #1f2937;
    border-radius: 16px;
    box-shadow: 0 12px 40px rgba(0, 0, 0, .28);
    overflow: hidden;
    z-index: 99999;
    font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
    font-size: 15px;
  }
  #cbia-janela.cbia-aberta { display: flex; animation: cbia-sobe .2s ease; }
  @keyframes cbia-sobe {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  .cbia-topo {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 14px;
    background: #0f766e;
    color: #fff;
  }
  .cbia-topo svg { width: 30px; height: 30px; flex-shrink: 0; }
  .cbia-topo div { flex: 1; line-height: 1.25; }
  .cbia-topo strong { display: block; font-size: .98rem; }
  .cbia-topo span { font-size: .78rem; opacity: .85; }
  .cbia-fechar {
    background: transparent;
    border: 0;
    color: #fff;
    font-size: 1.5rem;
    line-height: 1;
    cursor: pointer;
    padding: 2px 6px;
    border-radius: 6px;
  }
  .cbia-fechar:hover { background: rgba(255, 255, 255, .18); }

  #cbia-mensagens {
    flex: 1;
    overflow-y: auto;
    padding: 14px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    background: #f8fafc;
  }
  .cbia-msg {
    max-width: 88%;
    padding: 9px 12px;
    border-radius: 12px;
    line-height: 1.45;
    overflow-wrap: anywhere;
  }
  .cbia-usuario {
    align-self: flex-end;
    background: #0f766e;
    color: #fff;
    border-bottom-right-radius: 3px;
  }
  .cbia-ia {
    align-self: flex-start;
    background: #e8eef5;
    border-bottom-left-radius: 3px;
  }
  .cbia-erro { background: #fee2e2; color: #991b1b; }
  .cbia-ia p { margin: 0 0 8px; }
  .cbia-ia p:last-child, .cbia-ia ul:last-child, .cbia-ia ol:last-child { margin-bottom: 0; }
  .cbia-ia ul, .cbia-ia ol { margin: 0 0 8px; padding-left: 20px; }
  .cbia-ia table { border-collapse: collapse; margin: 6px 0; font-size: .85rem; display: block; overflow-x: auto; }
  .cbia-ia th, .cbia-ia td { border: 1px solid #cbd5e1; padding: 3px 7px; }
  .cbia-ia code { background: #d5dfeb; padding: 1px 5px; border-radius: 4px; font-size: .88em; }
  .cbia-ia pre { background: #d5dfeb; padding: 8px; border-radius: 8px; overflow-x: auto; }

  #cbia-form {
    display: flex;
    gap: 8px;
    padding: 10px 12px;
    border-top: 1px solid #e5e7eb;
    background: #fff;
  }
  #cbia-campo {
    flex: 1;
    min-width: 0;
    padding: 10px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    font-size: .95rem;
    font-family: inherit;
  }
  #cbia-campo:focus { outline: 2px solid #0f766e; border-color: transparent; }
  #cbia-enviar {
    padding: 0 16px;
    border: 0;
    border-radius: 10px;
    background: #0f766e;
    color: #fff;
    font-size: .95rem;
    cursor: pointer;
  }
  #cbia-enviar:disabled { background: #94a3b8; cursor: not-allowed; }

  /* Celular: janela ocupa quase a tela inteira */
  @media (max-width: 480px) {
    #cbia-janela {
      right: 12px;
      left: 12px;
      width: auto;
      bottom: 170px;
      height: calc(100vh - 190px);
    }
  }
</style>

<!-- Botão do robô -->
<button id="cbia-botao" type="button" aria-label="Abrir chat com a IA" aria-expanded="false" aria-controls="cbia-janela">
  <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <line x1="32" y1="6" x2="32" y2="14"/>
    <circle cx="32" cy="6" r="2.5" fill="currentColor"/>
    <rect x="12" y="14" width="40" height="32" rx="9"/>
    <line x1="6" y1="28" x2="6" y2="34"/>
    <line x1="58" y1="28" x2="58" y2="34"/>
    <circle cx="24" cy="29" r="3.5" fill="currentColor" stroke="none"/>
    <circle cx="40" cy="29" r="3.5" fill="currentColor" stroke="none"/>
    <path d="M24 38 Q32 43 40 38"/>
    <path d="M22 46 V54 H42 V46"/>
  </svg>
</button>

<!-- Janela do chat -->
<div id="cbia-janela" role="dialog" aria-label="Chat com assistente de educação financeira">
  <div class="cbia-topo">
    <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <line x1="32" y1="6" x2="32" y2="14"/>
      <rect x="12" y="14" width="40" height="32" rx="9"/>
      <circle cx="24" cy="29" r="3.5" fill="currentColor" stroke="none"/>
      <circle cx="40" cy="29" r="3.5" fill="currentColor" stroke="none"/>
      <path d="M24 38 Q32 43 40 38"/>
    </svg>
    <div>
      <strong>Assistente Financeiro</strong>
      <span>Tire suas dúvidas sobre finanças</span>
    </div>
    <button type="button" class="cbia-fechar" id="cbia-fechar" aria-label="Fechar chat">&times;</button>
  </div>

  <div id="cbia-mensagens" aria-live="polite"></div>

  <form id="cbia-form">
    <input type="text" id="cbia-campo" placeholder="Digite sua pergunta..." maxlength="1000" autocomplete="off" required>
    <button type="submit" id="cbia-enviar">Enviar</button>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/marked@12.0.2/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dompurify@3.1.6/dist/purify.min.js"></script>
<script>
(function () {
  const API_URL = <?= json_encode($cbiaApiUrl) ?>;
  const CHAVE_HISTORICO = "cbia_historico";
  const SAUDACAO = "Olá! Sou o assistente de educação financeira. Como posso ajudar?";

  const botao = document.getElementById("cbia-botao");
  const janela = document.getElementById("cbia-janela");
  const fechar = document.getElementById("cbia-fechar");
  const mensagens = document.getElementById("cbia-mensagens");
  const formulario = document.getElementById("cbia-form");
  const campo = document.getElementById("cbia-campo");
  const enviar = document.getElementById("cbia-enviar");

  let historico = [];

  // O histórico fica no sessionStorage para a conversa continuar ao trocar de página.
  function salvarHistorico() {
    try { sessionStorage.setItem(CHAVE_HISTORICO, JSON.stringify(historico.slice(-40))); } catch (e) {}
  }
  function carregarHistorico() {
    try {
      const salvo = sessionStorage.getItem(CHAVE_HISTORICO);
        if (salvo) {
          const parsed = JSON.parse(salvo);
          historico = Array.isArray(parsed)
            ? parsed.filter(m => m && typeof m.texto === "string" && ["usuario", "ia"].includes(m.tipo)).slice(-40)
            : [];
        }
    } catch (e) { historico = []; }
  }

  function renderizar(texto, tipo, comoMarkdown) {
    const div = document.createElement("div");
    div.className = "cbia-msg cbia-" + tipo.split(" ").join(" cbia-");
    if (comoMarkdown) {
        div.innerHTML = DOMPurify.sanitize(marked.parse(texto));
    } else {
      div.textContent = texto; // textContent evita injeção de HTML
    }
    mensagens.appendChild(div);
    mensagens.scrollTop = mensagens.scrollHeight;
    return div;
  }

  function adicionar(texto, tipo, comoMarkdown) {
    const div = renderizar(texto, tipo, comoMarkdown);
    if (tipo === "usuario" || tipo === "ia") {
      historico.push({ texto, tipo });
      salvarHistorico();
    }
    return div;
  }

  function abrir() {
    janela.classList.add("cbia-aberta");
    botao.setAttribute("aria-expanded", "true");
    botao.setAttribute("aria-label", "Fechar chat com a IA");
    campo.focus();
    mensagens.scrollTop = mensagens.scrollHeight;
  }
  function fecharJanela() {
    janela.classList.remove("cbia-aberta");
    botao.setAttribute("aria-expanded", "false");
    botao.setAttribute("aria-label", "Abrir chat com a IA");
    botao.focus();
  }

  botao.addEventListener("click", () => {
    janela.classList.contains("cbia-aberta") ? fecharJanela() : abrir();
  });
  fechar.addEventListener("click", fecharJanela);
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && janela.classList.contains("cbia-aberta")) fecharJanela();
  });

  formulario.addEventListener("submit", async (e) => {
    e.preventDefault();
    const pergunta = campo.value.trim();
    if (!pergunta) return;

    adicionar(pergunta, "usuario", false);
    campo.value = "";
    enviar.disabled = true;
    const carregando = renderizar("Pensando...", "ia", false);

    try {
      const resp = await fetch(API_URL, {
        method: "POST",
        headers: { "Content-Type": "application/json", "Accept": "application/json" },
        credentials: "same-origin",
        body: JSON.stringify({ pergunta })
      });
      const dados = await resp.json().catch(() => ({}));
      carregando.remove();

      if (!resp.ok) {
        renderizar(dados.erro || "Não foi possível responder agora.", "ia erro", false);
      } else if (typeof dados.resposta !== "string" || !dados.resposta.trim()) {
        renderizar("O assistente não retornou uma resposta válida.", "ia erro", false);
      } else {
        adicionar(dados.resposta, "ia", true);
      }
    } catch (err) {
      carregando.remove();
      renderizar("Não foi possível conectar ao assistente. Tente novamente em instantes.", "ia erro", false);
    } finally {
      enviar.disabled = false;
      campo.focus();
    }
  });

  // Inicialização: restaura o histórico ou mostra a saudação
  carregarHistorico();
  if (historico.length === 0) {
    renderizar(SAUDACAO, "ia", false);
  } else {
    historico.forEach((m) => renderizar(m.texto, m.tipo, m.tipo === "ia"));
  }
})();
</script>
<!-- ===== Fim do widget ===== -->
