(() => {
  const apiUrl = 'http://localhost:3000/api';
  const select = document.getElementById('brapiStock');
  const button = document.getElementById('brapiRefresh');
  const result = document.getElementById('brapiResult');
  if (!select || !button || !result) return;

  const escapeHtml = (value) => String(value ?? '-').replace(/[&<>"']/g, (char) => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
  })[char]);
  const showMessage = (message, error = false) => {
    result.innerHTML = `<p class="cotacoes-message${error ? ' is-error' : ''}">${escapeHtml(message)}</p>`;
  };

  async function loadStocks() {
    try {
      const response = await fetch(`${apiUrl}/stocks`);
      if (!response.ok) throw new Error('O servidor de cotações não respondeu corretamente.');
      const stocks = await response.json();
      if (!Array.isArray(stocks) || stocks.length === 0) throw new Error('Nenhuma ação foi encontrada.');

      select.innerHTML = '<option value="">Selecione uma ação...</option>';
      stocks.forEach((stock) => {
        if (!stock?.stock) return;
        const option = document.createElement('option');
        option.value = stock.stock;
        option.textContent = `${stock.stock} — ${stock.name || stock.stock}`;
        select.appendChild(option);
      });
      select.disabled = false;
      button.disabled = false;
      showMessage('Selecione uma ação e busque a cotação mais recente.');
    } catch (error) {
      select.innerHTML = '<option value="">Lista indisponível</option>';
      showMessage('Não foi possível carregar as ações. Verifique se o servidor brapi está rodando em localhost:3000.', true);
    }
  }

  async function loadQuote() {
    const ticker = select.value;
    if (!ticker) {
      showMessage('Selecione uma ação para consultar.', true);
      return;
    }
    button.disabled = true;
    button.textContent = 'Buscando...';
    showMessage(`Consultando ${ticker}...`);
    try {
      const response = await fetch(`${apiUrl}/quote/${encodeURIComponent(ticker)}`);
      const data = await response.json();
      const quote = data?.results?.[0];
      if (!response.ok || !quote) throw new Error('Não foi possível encontrar essa cotação.');

      const change = Number(quote.regularMarketChangePercent) || 0;
      const price = Number(quote.regularMarketPrice);
      const currency = quote.currency === 'BRL' || !quote.currency ? 'R$' : quote.currency;
      const formattedPrice = Number.isFinite(price)
        ? new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(price)
        : '—';
      const formattedChange = new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(change);
      const marketTime = quote.regularMarketTime;
      const marketDate = marketTime
        ? new Date(typeof marketTime === 'number' && marketTime < 1e12 ? marketTime * 1000 : marketTime)
        : null;
      const updated = marketDate && !Number.isNaN(marketDate.getTime())
        ? marketDate.toLocaleString('pt-BR')
        : 'Horário não informado pela API';

      result.innerHTML = `
        <div class="cotacao-dados">
          <div><span class="cotacao-ticker">${escapeHtml(quote.symbol || ticker)}</span><span class="cotacao-nome">${escapeHtml(quote.shortName || quote.longName || '')}</span></div>
          <strong class="cotacao-preco">${escapeHtml(currency)} ${formattedPrice}</strong>
          <span class="cotacao-variacao ${change >= 0 ? 'positive' : 'negative'}">${change >= 0 ? '+' : ''}${formattedChange}% hoje</span>
        </div>
        <p class="cotacao-horario">Última atualização informada: ${escapeHtml(updated)}. Use “Buscar cotação” para consultar novamente.</p>`;
    } catch (error) {
      showMessage(`${error.message} Confirme se o servidor brapi está ativo e se a chave da API é válida.`, true);
    } finally {
      button.disabled = false;
      button.textContent = 'Buscar cotação';
    }
  }

  button.addEventListener('click', loadQuote);
  select.addEventListener('change', () => {
    if (select.value) loadQuote();
  });
  loadStocks();
})();
