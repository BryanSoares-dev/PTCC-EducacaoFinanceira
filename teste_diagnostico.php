<?php
require_once __DIR__ . '/../back-end/seguranca.php';
iniciar_sessao_segura();
require_once("../back-end/conexao.php");

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$tema = 'escuro';
try {
    $stmtTema = $pdo->prepare('SELECT tema FROM usuarios WHERE id = ? LIMIT 1');
    $stmtTema->execute([$_SESSION['id']]);
    $temaUsuario = $stmtTema->fetchColumn();
    if (in_array($temaUsuario, ['claro', 'escuro'], true)) {
        $tema = $temaUsuario;
    }
} catch (PDOException $ignored) {
    $tema = 'escuro';
}

try {
    $pdo->exec("ALTER TABLE usuarios ADD COLUMN patente VARCHAR(30) NULL AFTER banner");
} catch (PDOException $ignored) {
    // A coluna já existe ou será criada pela migration_patente.sql.
}
try {
    $pdo->exec("ALTER TABLE usuarios ADD COLUMN xp INT NOT NULL DEFAULT 0 AFTER patente");
} catch (PDOException $ignored) {
    // A coluna já existe ou será criada pela migration_xp.sql.
}

// Questões atuais: investimentos, gestão financeira e criptoativos.
$questoes = [
['pergunta'=>'Você tem renda líquida de R$ 4.800, despesas essenciais de R$ 3.200 e nenhuma reserva. Antes de investir em ações, qual decisão é mais coerente?', 'alternativas'=>['Investir todo o excedente em ações de maior volatilidade para acelerar a formação de patrimônio','Contratar crédito rotativo para manter os aportes mesmo quando surgir uma despesa imprevista','Escolher o ativo com maior retorno passado, pois a reserva pode ser formada depois','Construir gradualmente uma reserva líquida, mapear o orçamento e só depois assumir risco de mercado'], 'correta'=>3],
['pergunta'=>'Um título rende 10% ao ano, a inflação acumulada é de 6% e há 1% de custos e impostos efetivos. Qual leitura é mais adequada?', 'alternativas'=>['O retorno real é automaticamente 5%, independentemente de impostos e composição','A inflação transforma o rendimento nominal em prejuízo em qualquer situação','O retorno nominal é 10%, mas o retorno real líquido será menor e depende da forma de cálculo e do período','O investidor ganhou exatamente 10% de poder de compra, pois inflação só afeta dinheiro parado'], 'correta'=>2],
['pergunta'=>'Uma carteira planejada para 60% renda fixa e 40% renda variável termina o ano em 48% e 52%. Qual ação representa rebalanceamento disciplinado?', 'alternativas'=>['Mudar a meta para 52% sem avaliar prazo, risco ou motivo da alteração','Comparar o desvio com a regra definida e ajustar gradualmente, considerando custos, impostos e o objetivo','Vender imediatamente toda a renda variável porque qualquer desvio indica erro de planejamento','Comprar apenas o ativo que mais subiu para preservar o ritmo de valorização'], 'correta'=>1],
['pergunta'=>'Você precisa pagar uma despesa médica provável em quatro meses. Qual combinação reduz o risco de precisar vender um ativo em perda?', 'alternativas'=>['Separar o valor em instrumento de alta liquidez e baixo risco, mantendo o prazo compatível com o objetivo','Aplicar em ativo de longo prazo e contar com a possibilidade de resgate antecipado','Concentrar o dinheiro em criptoativo líquido, porque liquidez elimina volatilidade','Comprar ações de uma empresa sólida, pois empresas sólidas não oscilam no curto prazo'], 'correta'=>0],
['pergunta'=>'Duas opções têm a mesma taxa bruta: um CDB de banco menor com liquidez diária e um título sem liquidez até o vencimento. O que ainda precisa ser comparado?', 'alternativas'=>['Somente a taxa anunciada, porque produtos com a mesma taxa têm o mesmo risco','A quantidade de avaliações do aplicativo, que substitui a análise do emissor','A rentabilidade do último mês, pois ela determina o resultado até o vencimento','Risco do emissor, cobertura aplicável, prazo, liquidez, tributação e compatibilidade com o objetivo'], 'correta'=>3],
['pergunta'=>'Uma pessoa paga apenas o mínimo do cartão e rola o saldo por três meses. Qual diagnóstico financeiro é mais preciso?', 'alternativas'=>['Como o cartão é um meio de pagamento, os juros não entram no orçamento mensal','A melhor resposta é contratar outro cartão para distribuir o saldo entre emissores','O custo efetivo da dívida pode crescer rapidamente; renegociar e priorizar a dívida cara tende a ser mais urgente que investir','O pagamento mínimo mantém a dívida estável, então basta reduzir investimentos por alguns dias'], 'correta'=>2],
['pergunta'=>'Uma ação caiu 35% após um resultado ruim. O investidor precisa do dinheiro em seis meses. Qual análise evita uma decisão automática?', 'alternativas'=>['Manter obrigatoriamente até recuperar o preço, pois vender com perda nunca é aceitável','Reavaliar fundamentos, liquidez, prazo, concentração e necessidade do dinheiro antes de decidir','Comprar mais imediatamente para reduzir o preço médio, sem revisar o risco ou o prazo','Vender tudo apenas porque uma queda sempre indica falência da empresa'], 'correta'=>1],
['pergunta'=>'Uma propaganda promete “renda mensal garantida, sem risco e acima do mercado”. Qual sinal exige maior cautela?', 'alternativas'=>['A promessa combina retorno elevado e ausência de risco; é preciso verificar produto, instituição, registro e documentos','A promessa é confiável se o anúncio tiver depoimentos e muitas curtidas','O risco é baixo se o pagamento inicial for pequeno e houver urgência para aderir','Basta verificar se existe um grupo de mensagens com outros investidores'], 'correta'=>0],
['pergunta'=>'Sobre diversificação, qual afirmação melhor descreve seus limites?', 'alternativas'=>['Comprar muitos ativos sempre garante retorno positivo e elimina a necessidade de acompanhar a carteira','Diversificar significa manter ativos da mesma empresa em várias corretoras','A diversificação só funciona quando todos os ativos sobem ao mesmo tempo','Distribuir exposições pode reduzir concentração, mas não elimina riscos sistêmicos, de mercado ou de liquidez'], 'correta'=>3],
['pergunta'=>'Uma stablecoin busca acompanhar o dólar. Qual risco continua existindo?', 'alternativas'=>['O único risco é a cotação do dólar; tecnologia e emissor não importam','Stablecoins têm proteção automática equivalente à de depósitos bancários','Pode haver risco de reserva, emissor, custódia, liquidez, contraparte e perda do mecanismo de paridade','A paridade elimina todos os riscos porque o ativo equivale legalmente a dinheiro em banco'], 'correta'=>2],
['pergunta'=>'Você recebe uma mensagem de “suporte” pedindo a frase-semente para liberar uma transferência. Qual conduta é correta?', 'alternativas'=>['Pedir que o remetente faça uma chamada de vídeo, pois isso prova que é legítimo','Não compartilhar a frase, interromper o contato, verificar o domínio por canal oficial e denunciar a tentativa','Enviar apenas metade da frase para que o suporte confirme a identidade','Compartilhar a frase se a mensagem mencionar o nome correto da carteira'], 'correta'=>1],
['pergunta'=>'Um investimento mostra retorno de 12% no ano, mas cobra taxa de administração, performance e custos de negociação. Como comparar?', 'alternativas'=>['Usar retorno líquido, riscos, prazo, liquidez e tributação; comparar só o número bruto pode distorcer a decisão','Ignorar custos, porque taxas já estão refletidas no marketing do produto','Subtrair apenas a taxa de administração e desconsiderar os demais custos','Escolher o produto com maior retorno bruto, mesmo que o risco e os custos sejam incompatíveis'], 'correta'=>0],
['pergunta'=>'A taxa básica de juros sobe e você possui um título prefixado longo. Qual conclusão é tecnicamente mais cuidadosa?', 'alternativas'=>['O título passa a render mais imediatamente em qualquer hipótese, sem efeito no preço','A alta dos juros garante perda definitiva, mesmo para quem mantém o título até o vencimento','A marcação a mercado só existe para ações e não afeta renda fixa','O preço de mercado pode oscilar antes do vencimento; o efeito depende do título, do prazo e de vender ou manter'], 'correta'=>3],
['pergunta'=>'Você quer medir se uma meta de poupança é viável. Qual método é mais consistente?', 'alternativas'=>['Usar apenas a renda bruta, pois impostos e despesas podem ser ajustados depois','Considerar o melhor mês de renda como se fosse recorrente','Projetar receitas, despesas fixas e variáveis, margem para imprevistos, prazo e valor de aporte revisável','Definir um valor alto e cortar todas as despesas de lazer, sem acompanhar a execução'], 'correta'=>2],
['pergunta'=>'Ao calcular juros compostos, por que a taxa e o período precisam estar na mesma unidade?', 'alternativas'=>['Porque basta multiplicar a taxa pelo número de anos, sem considerar capitalização','Porque converter a taxa e o número de períodos evita aplicar uma taxa anual como se fosse mensal','Porque juros compostos sempre usam apenas taxas mensais, mesmo quando o contrato é anual','Porque a unidade não altera o resultado, mas deixa a fórmula mais organizada'], 'correta'=>1],
['pergunta'=>'Uma pessoa quer financiar um bem e compara apenas o valor da parcela. Qual informação está faltando?', 'alternativas'=>['Custo efetivo total, prazo, entrada, seguros, tarifas, juros e impacto da parcela no orçamento','Somente a cor do anúncio e o número de parcelas promocionais','A cotação de uma ação, porque todo financiamento deve ser comparado com renda variável','Nada: a menor parcela sempre representa o menor custo total'], 'correta'=>0],
['pergunta'=>'Uma carteira tem 90% em ativos de uma única empresa porque o investidor conhece bem o negócio. Qual risco permanece?', 'alternativas'=>['Nenhum risco relevante, porque conhecer a empresa equivale a diversificar','A concentração reduz o risco se o investidor acompanhar notícias diariamente','O risco só existiria se a empresa fosse pequena ou desconhecida','Risco de concentração, mesmo com conhecimento; uma falha específica pode afetar grande parte do patrimônio'], 'correta'=>3],
['pergunta'=>'Você precisa escolher entre manter reserva de emergência em produto com saque imediato ou buscar maior retorno com prazo de carência. Qual critério vem primeiro?', 'alternativas'=>['A carência é irrelevante se o produto tiver rentabilidade histórica positiva','A decisão depende apenas da propaganda da instituição, não do objetivo','A função da reserva exige disponibilidade e preservação de capital; o retorno maior não deve comprometer essa função','O maior retorno deve sempre vencer, pois emergências podem ser pagas com cartão'], 'correta'=>2],
['pergunta'=>'Uma publicação afirma que rentabilidade passada prova que um fundo continuará superior. Qual resposta demonstra pensamento crítico?', 'alternativas'=>['Aceitar a conclusão se o fundo tiver superado o benchmark em um único mês','Comparar apenas o maior retorno observado e ignorar períodos negativos','Concluir que fundos que perderam no passado nunca podem se recuperar','correta','pergunta','Ao avaliar um fundo de investimento, qual conjunto de informações permite uma comparação menos superficial?','alternativas','Política, benchmark, volatilidade, drawdown, taxas, liquidez, prazo e aderência ao perfil do investidor','Somente o maior retorno anual exibido na lâmina comercial','A quantidade de cotistas, pois fundos maiores sempre apresentam menor risco','A rentabilidade do último mês, sem considerar estratégia ou custos','Verificar período, benchmark, volatilidade, taxas, estratégia, mudanças de gestão e riscos; passado não é garantia'], 'correta'=>3],
    ['pergunta'=>'Ao avaliar um fundo de investimento, qual conjunto de informações permite uma comparação menos superficial?', 'alternativas'=>['Política, benchmark, volatilidade, drawdown, taxas, liquidez, prazo e aderência ao perfil do investidor','Somente o maior retorno anual exibido na lâmina comercial','A quantidade de cotistas, pois fundos maiores sempre apresentam menor risco','A rentabilidade do último mês, sem considerar estratégia ou custos'], 'correta'=>0],
];
// Variáveis para controle do teste
$teste_finalizado = false;
$pontuacao = 0;
$patente = "";
$diagnostico = "";

// Processa o envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar'])) {
    // CSRF: sem isso, outro site poderia forçar o envio do teste
    // diagnóstico em nome da vítima, alterando a patente dela.
    csrf_exigir();

    $pontuacao = 0;
    $total_questoes = count($questoes);
    
    for ($i = 0; $i < $total_questoes; $i++) {
        $resposta = isset($_POST['q' . $i]) ? intval($_POST['q' . $i]) : -1;
        if ($resposta === $questoes[$i]['correta']) {
            $pontuacao++;
        }
    }
    
    // O diagnóstico libera no máximo Ouro 1; o restante da progressão ocorre na Arena.
    if ($pontuacao <= 4) {
        $patente = "FERRO 1";
        $diagnostico = "Você está começando: vamos construir uma base financeira segura.";
        $xpInicial = 0;
    } elseif ($pontuacao <= 9) {
        $patente = "FERRO 2";
        $diagnostico = "Você já reconhece conceitos importantes e pode evoluir com prática.";
        $xpInicial = 50;
    } elseif ($pontuacao <= 14) {
        $patente = "FERRO 3";
        $diagnostico = "Você tem uma boa base para estudar carteira, risco e planejamento.";
        $xpInicial = 150;
    } else {
        $patente = "OURO 1";
        $diagnostico = "Você demonstrou domínio dos fundamentos. Continue praticando com responsabilidade.";
        $xpInicial = 300;
    }
    
    // Salva o resultado no banco (opcional)
    try {
        $sql = "INSERT INTO resultados_teste (usuario_id, pontuacao, patente, diagnostico, data_teste) 
                VALUES (?, ?, ?, ?, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION['id'], $pontuacao, $patente, $diagnostico]);
    } catch (PDOException $e) {
        // Se a tabela não existir, apenas ignora
    }

    $stmtPatente = $pdo->prepare("UPDATE usuarios SET patente = ?, xp = GREATEST(COALESCE(xp, 0), ?) WHERE id = ?");
    $stmtPatente->execute([$patente, $xpInicial, $_SESSION['id']]);
    
    $teste_finalizado = true;
}
?>
<!DOCTYPE html>
<html lang="pt-br" class="<?= htmlspecialchars($tema, ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste Diagnóstico</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* =========================
           ESTILOS DO TESTE DIAGNÓSTICO
           Liquid Glass + tema escuro
        ========================= */
        
        .hero_teste {
            padding-top: 120px;
            min-height: 50vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #0A2540 0%, #24314C 50%, #0A2540 100%);
            position: relative;
            overflow: hidden;
        }

        .hero_teste::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: rgba(22, 226, 138, 0.06);
            right: -150px;
            top: -100px;
            filter: blur(120px);
        }

        .hero_teste_container {
            width: 100%;
            max-width: 1300px;
            margin: auto;
            padding: 60px 40px 40px;
            position: relative;
            z-index: 2;
        }

        .hero_teste_content {
            max-width: 750px;
        }

        .badge_teste {
            display: inline-block;
            padding: 10px 18px;
            border-radius: 9999px;
            background: rgba(22, 226, 138, 0.12);
            color: #16E28A;
            border: 1px solid rgba(22, 226, 138, 0.25);
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
        }

        .hero_teste_content h1 {
            font-size: clamp(2.8rem, 5vw, 4rem);
            line-height: 1.15;
            color: #ffffff;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .hero_teste_content h1 .destaque {
            color: #16E28A;
            background: linear-gradient(135deg, #16E28A, #0db873);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero_teste_content p {
            color: rgba(255, 255, 255, 0.85);
            font-size: 1.15rem;
            line-height: 1.8;
            max-width: 600px;
        }

        /* Container do teste */
        .teste_container {
            max-width: 900px;
            margin: -30px auto 60px;
            padding: 0 20px;
            position: relative;
            z-index: 2;
        }

        .teste_card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 28px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.20), inset 0 1px 0 rgba(255, 255, 255, 0.08);
        }

        .progresso_teste {
            margin-bottom: 30px;
            padding: 15px 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .progresso_teste span {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.95rem;
        }

        .progresso_teste strong {
            color: #16E28A;
        }

        .questao {
            margin-bottom: 35px;
            padding-bottom: 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .questao:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .questao_numero {
            display: inline-block;
            background: rgba(22, 226, 138, 0.15);
            color: #16E28A;
            padding: 4px 14px;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .questao p {
            color: #ffffff;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .alternativas {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .alternativa {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 18px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 14px;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .alternativa:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(22, 226, 138, 0.3);
        }

        .alternativa input[type="radio"] {
            accent-color: #16E28A;
            width: 18px;
            height: 18px;
            cursor: pointer;
            flex-shrink: 0;
        }

        .alternativa label {
            color: rgba(255, 255, 255, 0.85);
            cursor: pointer;
            font-size: 0.95rem;
        }

        /* Botão finalizar */
        .btn_finalizar {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 9999px;
            background: linear-gradient(135deg, #16E28A, #29f0a0);
            color: #0A2540;
            font-weight: 800;
            font-size: 1.1rem;
            cursor: pointer;
            transition: 0.3s ease;
            box-shadow: 0 8px 24px rgba(22, 226, 138, 0.30);
            margin-top: 20px;
        }

        .btn_finalizar:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(22, 226, 138, 0.45);
        }

        /* =========================
           RESULTADO
        ========================= */
        .resultado_box {
            text-align: center;
            padding: 30px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .resultado_icone {
            font-size: 5rem;
            margin-bottom: 15px;
        }

        .resultado_box h2 {
            color: #ffffff;
            font-size: 2.2rem;
            margin-bottom: 10px;
        }

        .resultado_patente {
            display: inline-block;
            padding: 8px 24px;
            background: linear-gradient(135deg, #16E28A, #29f0a0);
            color: #0A2540;
            border-radius: 9999px;
            font-size: 1.5rem;
            font-weight: 800;
            margin: 15px 0;
        }

        .resultado_box p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.1rem;
            margin-bottom: 10px;
        }

        .resultado_pontuacao {
            color: #16E28A;
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 25px;
        }

        .btn_voltar_recursos {
            display: inline-block;
            padding: 14px 35px;
            border: none;
            border-radius: 9999px;
            background: #16E28A;
            color: #0A2540;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s ease;
            text-decoration: none;
            box-shadow: 0 8px 20px rgba(22, 226, 138, 0.25);
        }

        .btn_voltar_recursos:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(22, 226, 138, 0.40);
        }

        /* Responsivo */
        @media (max-width: 768px) {
            .hero_teste {
                min-height: 35vh;
                padding-top: 100px;
            }
            .hero_teste_container {
                padding: 40px 20px;
            }
            .hero_teste_content h1 {
                font-size: 2.2rem;
            }
            .teste_card {
                padding: 25px 20px;
            }
            .alternativa {
                padding: 10px 14px;
            }
            .resultado_patente {
                font-size: 1.2rem;
            }
        }
    </style>
    <link rel="stylesheet" href="../css/liquid-glass.css?v=103">
    <link rel="stylesheet" href="../css/diagnostico.css?v=1">

</head>
<body class="diagnostico-page">

<?php include_once 'navbar.php'; ?>

<main>

    <!-- HERO -->
    <section class="hero_teste">
        <div class="hero_teste_container">
            <div class="hero_teste_content">
                <span class="badge_teste">
                    <i class="fas fa-clipboard-check"></i> Teste Diagnóstico
                </span>
                <h1>Descubra seu <span class="destaque">Perfil de Investidor</span></h1>
                <p>Responda as 20 questões abaixo e descubra seu nível de conhecimento sobre investimentos.</p>
            </div>
        </div>
    </section>

    <!-- CONTEÚDO DO TESTE -->
    <section class="teste_container">
        <div class="teste_card">
            
            <?php if (!$teste_finalizado): ?>
            
            <!-- Formulário do teste -->
            <form method="POST" action="">
                <?= csrf_campo() ?>
                <div class="progresso_teste">
                    <span><i class="fas fa-info-circle"></i> Responda todas as questões. Cada questão vale <strong>1 ponto</strong>.</span>
                </div>
                
                <?php foreach ($questoes as $index => $q): ?>
                <div class="questao">
                    <div class="questao_numero">Questão <?php echo $index + 1; ?></div>
                    <p><?php echo $q['pergunta']; ?></p>
                    <div class="alternativas">
                        <?php foreach ($q['alternativas'] as $alt_index => $alt): ?>
                        <div class="alternativa">
                            <input type="radio" name="q<?php echo $index; ?>" id="q<?php echo $index . '_' . $alt_index; ?>" value="<?php echo $alt_index; ?>" required>
                            <label for="q<?php echo $index . '_' . $alt_index; ?>"><?php echo $alt; ?></label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <button type="submit" name="finalizar" class="btn_finalizar">
                    <i class="fas fa-arrow-right"></i> Finalizar Teste
                </button>
            </form>
            
            <?php else: ?>
            
            <!-- Resultado -->
            <div class="resultado_box">
                <div class="resultado_icone">
                    <?php if ($pontuacao <= 4): ?>
                        <i class="fas fa-graduation-cap" style="color: #FF4757;"></i>
                    <?php elseif ($pontuacao <= 14): ?>
                        <i class="fas fa-graduation-cap" style="color: #FFA502;"></i>
                    <?php else: ?>
                        <i class="fas fa-graduation-cap" style="color: #16E28A;"></i>
                    <?php endif; ?>
                </div>
                <h2>Teste Finalizado!</h2>
                <div class="resultado_patente"><?php echo $patente; ?></div>
                <p><?php echo $diagnostico; ?></p>
                <div class="resultado_pontuacao">
                    <i class="fas fa-star"></i> Pontuação: <?php echo $pontuacao; ?> / 20
                </div>
                <!-- Link para voltar - agora apontando para aprendizado.php na mesma pasta -->
                <a href="aprendizado.php" class="btn_voltar_recursos">
                    <i class="fas fa-arrow-left"></i> Voltar para Recursos
                </a>
            </div>
            
            <?php endif; ?>
            
        </div>
    </section>

</main>

<script>
    // Marca a alternativa automaticamente ao clicar em toda a div
    document.querySelectorAll('.alternativa').forEach(function(el) {
        el.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
            }
        });
    });
</script>

<!-- Widget de Acessibilidade — integrado em todas as páginas -->
<script src="../JS/acessibilidade.js" defer></script>
<script src="../JS/liquid-glass.js?v=100" defer></script>
<?php include '../suporte-ia/chatbot-widget.php'; ?>
</body>
</html>

