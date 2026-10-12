<?php
require_once __DIR__ . '/../asaas-pagamento/seguranca.php';
include_once 'navbar.php';
$csrfToken = csrf_token();
$tema = 'sistema';
if (!empty($_SESSION['id'])) {
  try {
    $stmtTema = afdeDb()->prepare('SELECT tema FROM usuarios WHERE id = ? LIMIT 1');
    $stmtTema->execute([(int) $_SESSION['id']]);
    $temaUsuario = $stmtTema->fetchColumn();
    if (in_array($temaUsuario, ['claro', 'escuro', 'sistema'], true)) {
      $tema = $temaUsuario;
    }
  } catch (Throwable $e) {
    // A página de planos continua acessível mesmo se a consulta de tema falhar.
  }
}
// planos.php – Página de Planos
// Ajuste preços, recursos e links de checkout no array abaixo.

$planos = [
  [
    'id' => 'gratuito', 'nome' => 'Gratuito', 'classe' => '',
    'desc' => 'Para quem está começando a organizar a vida financeira.',
    'mensal' => 0, 'anual' => 0,
    'btn' => 'Começar grátis', 'link' => 'cadastro.php', 'primario' => false,
    'recursos' => [
      ['Carteira de investimentos (até 5 ativos)', true],
      ['Calculadora financeira', true],
      ['Controle de receitas e despesas', true],
      ['Analisador de ativos completo', false],
      ['Relatórios avançados e exportação', false],
      ['Suporte prioritário', false],
    ],
  ],
  [
    'id' => 'premium', 'nome' => 'Premium', 'classe' => 'popular', 'tag' => 'Mais popular',
    'desc' => 'Ferramentas completas para investir com mais segurança.',
    'mensal' => 19.90, 'anual' => 15.90,
    'btn' => 'Assinar Premium', 'primario' => true,
    'recursos' => [
      ['Tudo do plano Gratuito', true],
      ['Carteira ilimitada de ativos', true],
      ['Analisador de ativos completo', true],
      ['Relatórios avançados e exportação', true],
      ['Sem anúncios', true],
      ['Suporte prioritário', false],
    ],
  ],
  [
    'id' => 'pro', 'nome' => 'Premium Pro', 'classe' => 'pro',
    'desc' => 'Para investidores que querem o máximo de análise e controle.',
    'mensal' => 39.90, 'anual' => 31.90,
    'btn' => 'Assinar Premium Pro', 'primario' => true,
    'recursos' => [
      ['Tudo do plano Premium', true],
      ['Múltiplas carteiras', true],
      ['Alertas personalizados de preço', true],
      ['Simulações e projeções avançadas', true],
      ['Acesso antecipado a novidades', true],
      ['Suporte prioritário', true],
    ],
  ],
];

function preco_partes(float $v): array {
  $f = number_format($v, 2, ',', '.');
  return explode(',', $f);
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="<?= htmlspecialchars($tema, ENT_QUOTES, 'UTF-8') ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Compare os planos FinControl e escolha o ciclo mensal ou anual com pagamento via Pix.">
  <title>Planos | FinControl</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/liquid-glass.css">
  <link rel="stylesheet" href="../css/style-plano.css">
</head>
<body class="planos-page">

  <?php // include 'includes/navbar.php'; // <- sua navbar padrão ?>

  <main class="planos-main">

    <!-- HERO -->
    <section class="hero-planos">
      <span class="badge">Planos e preços</span>
      <h1>Escolha o plano ideal para <span class="destaque">o seu momento</span></h1>
      <p>Comece de graça e evolua quando quiser. Pagamentos avulsos via Pix, sem renovação automática.</p>

      <div class="ciclo" role="group" aria-label="Ciclo de cobrança">
        <button type="button" class="ativo" data-ciclo="mensal" aria-pressed="true">Mensal</button>
        <button type="button" data-ciclo="anual" aria-pressed="false">Anual <small>-20%</small></button>
      </div>
    </section>

    <div class="content">

      <!-- CARDS -->
      <section class="planos-grid">
        <?php foreach ($planos as $p):
          [$im, $cm] = preco_partes($p['mensal']);
          [$ia, $ca] = preco_partes($p['anual']);
          $gratis = $p['mensal'] == 0;
        ?>
        <article class="plano-card glass-card <?= htmlspecialchars($p['classe']) ?>" data-liquid-glass>
          <?php if (!empty($p['tag'])): ?>
            <span class="plano-tag"><?= htmlspecialchars($p['tag']) ?></span>
          <?php endif; ?>
          <h2><?= htmlspecialchars($p['nome']) ?></h2>
          <p class="plano-desc"><?= htmlspecialchars($p['desc']) ?></p>

          <div class="plano-preco">
            <span class="moeda">R$</span>
            <span class="valor" data-mensal="<?= $im ?>" data-anual="<?= $ia ?>"><?= $im ?></span>
            <span class="centavos" data-mensal=",<?= $cm ?>" data-anual=",<?= $ca ?>">,<?= $cm ?></span>
          </div>
          <p class="plano-periodo"
             data-mensal="<?= $gratis ? 'Grátis para sempre' : 'por mês' ?>"
             data-anual="<?= $gratis ? 'Grátis para sempre' : 'por mês, cobrado anualmente' ?>">
             <?= $gratis ? 'Grátis para sempre' : 'por mês' ?></p>

          <?php if ($gratis): ?>
            <a href="<?= htmlspecialchars($p['link']) ?>" class="btn-plano"><?= htmlspecialchars($p['btn']) ?></a>
          <?php else: ?>
            <button type="button" class="btn-plano primario btn-assinar"
                    data-plano="<?= htmlspecialchars($p['id']) ?>"
                    data-nome="<?= htmlspecialchars($p['nome']) ?>"
                    data-mensal="<?= $p['mensal'] ?>" data-anual="<?= $p['anual'] ?>">
              <?= htmlspecialchars($p['btn']) ?>
            </button>
          <?php endif; ?>

          <ul class="plano-lista">
            <?php foreach ($p['recursos'] as [$texto, $ativo]): ?>
              <li class="<?= $ativo ? '' : 'off' ?>"><?= htmlspecialchars($texto) ?></li>
            <?php endforeach; ?>
          </ul>
        </article>
        <?php endforeach; ?>
      </section>

      <p class="garantia">Pagamento seguro · Garantia de 7 dias · Cancele quando quiser</p>

      <!-- FAQ -->
      <section class="secao glass-card">
        <h2>Perguntas frequentes</h2>
        <div class="faq">
          <details>
            <summary>Posso trocar de plano depois?</summary>
            <p>Você pode assinar outro plano ou ciclo quando quiser. O novo período começa após a confirmação do pagamento, sem cálculo proporcional.</p>
          </details>
          <details>
            <summary>Existe fidelidade ou multa de cancelamento?</summary>
            <p>As cobranças são avulsas e não renovam automaticamente. O acesso continua até o fim do período pago.</p>
          </details>
          <details>
            <summary>Quais formas de pagamento são aceitas?</summary>
            <p>Por enquanto, o pagamento é feito via Pix.</p>
          </details>
          <details>
            <summary>O que acontece com meus dados se eu voltar ao plano Gratuito?</summary>
            <p>Seus dados são mantidos. Apenas os recursos exclusivos dos planos pagos ficam indisponíveis.</p>
          </details>
        </div>
      </section>

    </div>
  </main>

  <!-- MODAL DE COMPRA -->
  <div class="modal-overlay" id="modalCompra" hidden>
    <div class="modal-compra glass-card" role="dialog" aria-modal="true" aria-labelledby="mcTitulo">
      <button type="button" class="mc-fechar" aria-label="Fechar">&times;</button>
      <form action="../asaas-pagamento/cobranca-asaas.php" method="post" id="formCompra" autocomplete="off">
        <input type="hidden" name="plano" id="mcPlano">
        <input type="hidden" name="ciclo" id="mcCiclo" value="mensal">
        <span class="badge">Pagamento via Pix</span>
        <h2 id="mcTitulo">Assinar <span id="mcNome">Premium</span></h2>
        <div class="mc-resumo">
          <div class="ciclo mc-ciclo" role="group" aria-label="Ciclo de cobrança">
            <button type="button" class="ativo" data-ciclo="mensal">Mensal</button>
            <button type="button" data-ciclo="anual">Anual <small>-20%</small></button>
          </div>
          <div class="mc-total">
            <span id="mcPeriodo">Total por mês</span>
            <strong id="mcValor">R$ 0,00</strong>
          </div>
        </div>
        <div class="input-group"><label for="mcCpf">CPF ou CNPJ do pagador</label><input id="mcCpf" name="cpfCnpj" inputmode="numeric" autocomplete="off" maxlength="18" placeholder="Somente números" required></div>
        <div class="mc-pix"><p>O QR Code Pix será gerado ao confirmar. O pagamento será associado à sua conta.</p></div>
        <p id="mcErro" role="alert" hidden></p>
        <button type="submit" class="btn-plano primario mc-confirmar">Gerar cobrança Pix</button>
        <p class="mc-nota">A cobrança vence em 3 dias.</p>
      </form>
      <section id="resultadoPix" hidden aria-live="polite">
        <h3>Finalize o pagamento</h3>
        <p>Pedido <span id="pixPedido"></span> · <strong id="pixValor"></strong></p>
        <img id="pixImagem" alt="QR Code Pix para pagamento" hidden style="max-width:240px;width:100%;margin:16px auto">
        <label for="pixCopiaECola">Pix copia e cola</label>
        <textarea id="pixCopiaECola" readonly rows="4" style="width:100%"></textarea>
        <button type="button" id="copiarPix" class="btn-plano">Copiar código Pix</button>
        <p id="pixSemQr" hidden>O QR Code não ficou disponível. Você pode pagar pelo link do Asaas.</p>
        <a id="pixFatura" class="btn-plano primario" target="_blank" rel="noopener" hidden>Abrir pagamento no Asaas</a>
      </section>
    </div>
  </div>
  <script>
    var ciclo = 'mensal', planoAtual = null;
    var brl = function (v) { return v.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }); };
    function setCiclo(c) {
      ciclo = c;
      document.querySelectorAll('.ciclo button').forEach(function (b) {
        var ativo = b.dataset.ciclo === c;
        b.classList.toggle('ativo', ativo);
        b.setAttribute('aria-pressed', ativo);
      });
      document.querySelectorAll('.plano-card [data-mensal]:not(.btn-assinar)').forEach(function (el) { el.textContent = el.dataset[c]; });
      document.getElementById('mcCiclo').value = c;
      if (planoAtual) {
        var valorMensal = parseFloat(planoAtual.dataset[c]);
        document.getElementById('mcValor').textContent = brl(c === 'anual' ? valorMensal * 12 : valorMensal);
        document.getElementById('mcPeriodo').textContent = c === 'anual' ? 'Total por ano (12x ' + brl(valorMensal) + ')' : 'Total por mês';
      }
    }
    document.querySelectorAll('.ciclo button').forEach(function (b) { b.addEventListener('click', function () { setCiclo(b.dataset.ciclo); }); });

    var modal = document.getElementById('modalCompra');
    var form = document.getElementById('formCompra');
    var ultimoFoco = null;
    function abrirModal(btn) {
      planoAtual = btn; ultimoFoco = btn;
      document.getElementById('mcPlano').value = btn.dataset.plano;
      document.getElementById('mcNome').textContent = btn.dataset.nome;
      form.hidden = false;
      document.getElementById('resultadoPix').hidden = true;
      document.getElementById('mcErro').hidden = true;
      setCiclo(ciclo);
      modal.hidden = false;
      document.body.style.overflow = 'hidden';
    }
    function fecharModal() {
      modal.hidden = true;
      document.body.style.overflow = '';
      if (ultimoFoco) ultimoFoco.focus();
    }
    document.querySelectorAll('.btn-assinar').forEach(function (b) { b.addEventListener('click', function () { abrirModal(b); }); });
    modal.addEventListener('click', function (e) { if (e.target === modal) fecharModal(); });
    modal.querySelector('.mc-fechar').addEventListener('click', fecharModal);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !modal.hidden) fecharModal(); });

    form.addEventListener('submit', async function (e) {
      e.preventDefault();
      var botao = form.querySelector('[type="submit"]');
      var erro = document.getElementById('mcErro');
      erro.hidden = true;
      botao.disabled = true;
      botao.textContent = 'Gerando cobrança…';
      try {
        var resposta = await fetch(form.action, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': <?= json_encode($csrfToken) ?> },
          body: JSON.stringify({ plano: document.getElementById('mcPlano').value, ciclo: document.getElementById('mcCiclo').value, cpfCnpj: document.getElementById('mcCpf').value.replace(/\D/g, '') })
        });
        var respostaTexto = await resposta.text();
        var dados;
        try {
          dados = JSON.parse(respostaTexto);
        } catch (parseError) {
          throw new Error('O servidor retornou uma resposta inesperada (HTTP ' + resposta.status + '). Confira o log do PHP/Apache.');
        }
        if (!resposta.ok || !dados.ok) {
          var mensagem = dados.erro || 'Não foi possível gerar a cobrança.';
          if (resposta.status === 401) mensagem += ' <a href="login.php">Entre na sua conta</a> e tente novamente.';
          erro.innerHTML = mensagem;
          erro.hidden = false;
          return;
        }
        document.getElementById('pixPedido').textContent = '#' + dados.pedidoId;
        document.getElementById('pixValor').textContent = brl(Number(dados.valor));
        document.getElementById('pixCopiaECola').value = dados.pixCopiaECola || '';
        document.getElementById('pixCopiaECola').hidden = !dados.pixCopiaECola;
        document.getElementById('copiarPix').hidden = !dados.pixCopiaECola;
        document.getElementById('pixSemQr').hidden = !!dados.pixCopiaECola;
        var img = document.getElementById('pixImagem');
        img.hidden = !dados.pixImagem;
        if (dados.pixImagem) img.src = dados.pixImagem;
        var fatura = document.getElementById('pixFatura');
        fatura.hidden = !dados.invoiceUrl;
        if (dados.invoiceUrl) fatura.href = dados.invoiceUrl;
        form.hidden = true;
        document.getElementById('resultadoPix').hidden = false;
      } catch (err) {
        erro.textContent = err.message || 'Falha de comunicação. Verifique sua conexão e tente novamente.';
        erro.hidden = false;
      } finally {
        botao.disabled = false;
        botao.textContent = 'Gerar cobrança Pix';
      }
    });
    document.getElementById('mcCpf').addEventListener('input', function () {
      this.value = this.value.replace(/\D/g, '').slice(0, 14);
    });
    document.getElementById('copiarPix').addEventListener('click', async function () {
      var campo = document.getElementById('pixCopiaECola');
      try { await navigator.clipboard.writeText(campo.value); this.textContent = 'Código copiado'; }
      catch (e) { campo.select(); document.execCommand('copy'); this.textContent = 'Código copiado'; }
    });
  </script>
<script src="../JS/liquid-glass.js?v=100" defer></script>
</body>
</html>