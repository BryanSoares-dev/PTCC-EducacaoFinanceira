<?php
require_once __DIR__ . '/../back-end/bootstrap.php';
require_once("../back-end/conexao.php");

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Array com as questões e respostas corretas (índice 0-based)
$questoes = [
    // Q1
    [
        'pergunta' => 'O que significa investir?',
        'alternativas' => [
            'A) Guardar dinheiro sem possibilidade de perda',
            'B) Colocar dinheiro em algum ativo buscando obter retorno no futuro',
            'C) Gastar dinheiro para aumentar o patrimônio',
            'D) Guardar dinheiro exclusivamente em uma conta corrente'
        ],
        'correta' => 1
    ],
    // Q2
    [
        'pergunta' => 'Qual é a principal diferença entre poupar e investir?',
        'alternativas' => [
            'A) Poupar envolve guardar dinheiro, enquanto investir busca fazê-lo render',
            'B) Investir nunca envolve riscos',
            'C) Poupar sempre gera mais retorno que investir',
            'D) Não existe diferença entre os dois conceitos'
        ],
        'correta' => 0
    ],
    // Q3
    [
        'pergunta' => 'Se uma pessoa deixa R$ 1.000 parados durante um ano enquanto os preços aumentam 5%, o que pode acontecer?',
        'alternativas' => [
            'A) O dinheiro necessariamente passa a valer R$ 1.050',
            'B) O poder de compra do dinheiro pode diminuir',
            'C) O dinheiro perde exatamente 5% do seu valor nominal',
            'D) O dinheiro automaticamente rende 5%'
        ],
        'correta' => 1
    ],
    // Q4
    [
        'pergunta' => 'O que é inflação?',
        'alternativas' => [
            'A) Aumento generalizado dos preços ao longo do tempo',
            'B) Redução dos juros cobrados pelos bancos',
            'C) Aumento do salário médio da população',
            'D) Crescimento da quantidade de investimentos disponíveis'
        ],
        'correta' => 0
    ],
    // Q5
    [
        'pergunta' => 'Uma pessoa investiu R$ 1.000 e, após determinado período, possui R$ 1.100. Qual foi a rentabilidade nominal?',
        'alternativas' => [
            'A) 1%',
            'B) 5%',
            'C) 10%',
            'D) 11%'
        ],
        'correta' => 2
    ],
    // Q6
    [
        'pergunta' => 'O que representa o risco de um investimento?',
        'alternativas' => [
            'A) A possibilidade de o investimento apresentar um resultado diferente do esperado',
            'B) A certeza de perder dinheiro',
            'C) O valor mínimo necessário para investir',
            'D) A quantidade de dinheiro que o investidor possui'
        ],
        'correta' => 0
    ],
    // Q7
    [
        'pergunta' => 'Qual investimento tende a apresentar maior oscilação de preço no curto prazo?',
        'alternativas' => [
            'A) Ações',
            'B) Dinheiro parado em conta corrente',
            'C) Título de renda fixa com remuneração previamente definida',
            'D) Reserva em espécie'
        ],
        'correta' => 0
    ],
    // Q8
    [
        'pergunta' => 'O que significa diversificar uma carteira de investimentos?',
        'alternativas' => [
            'A) Colocar todo o dinheiro no investimento mais rentável',
            'B) Distribuir o dinheiro entre diferentes investimentos e/ou classes de ativos',
            'C) Investir somente em empresas grandes',
            'D) Comprar o mesmo ativo em diferentes bancos'
        ],
        'correta' => 1
    ],
    // Q9
    [
        'pergunta' => 'Por que a diversificação pode ser importante?',
        'alternativas' => [
            'A) Porque elimina completamente o risco',
            'B) Porque garante lucro em qualquer situação',
            'C) Porque pode reduzir a exposição da carteira aos problemas de um único investimento',
            'D) Porque sempre aumenta a rentabilidade'
        ],
        'correta' => 2
    ],
    // Q10
    [
        'pergunta' => 'Uma pessoa pretende investir um dinheiro que poderá precisar daqui a duas semanas. Qual característica deveria receber bastante atenção?',
        'alternativas' => [
            'A) Liquidez',
            'B) Volatilidade histórica da bolsa',
            'C) Dividendos',
            'D) Crescimento da empresa'
        ],
        'correta' => 0
    ],
    // Q11
    [
        'pergunta' => 'O que significa liquidez em um investimento?',
        'alternativas' => [
            'A) A capacidade de transformar o investimento em dinheiro com facilidade e rapidez',
            'B) A possibilidade de ganhar dinheiro todos os dias',
            'C) O percentual de imposto pago sobre o investimento',
            'D) O tamanho da instituição financeira responsável pelo investimento'
        ],
        'correta' => 0
    ],
    // Q12
    [
        'pergunta' => 'Dois investimentos apresentam a mesma rentabilidade nominal de 10% ao ano. Se a inflação do período foi de 6%, o investidor:',
        'alternativas' => [
            'A) Necessariamente perdeu dinheiro',
            'B) Teve ganho real positivo, aproximadamente',
            'C) Teve ganho real exatamente de 10%',
            'D) Teve ganho real exatamente de 16%'
        ],
        'correta' => 1
    ],
    // Q13
    [
        'pergunta' => 'Qual alternativa representa melhor a relação entre risco e retorno?',
        'alternativas' => [
            'A) Quanto maior o retorno, menor será sempre o risco',
            'B) Investimentos de maior risco podem oferecer maior potencial de retorno, mas não há garantia',
            'C) Todo investimento de alto risco obrigatoriamente dará mais lucro',
            'D) Risco e retorno não possuem nenhuma relação'
        ],
        'correta' => 1
    ],
    // Q14
    [
        'pergunta' => 'Uma pessoa possui uma reserva financeira para emergências. Qual característica é especialmente importante para esse dinheiro?',
        'alternativas' => [
            'A) Alta liquidez e baixo risco',
            'B) Alta volatilidade',
            'C) Baixa liquidez e alto risco',
            'D) Investimento exclusivamente em ações'
        ],
        'correta' => 0
    ],
    // Q15
    [
        'pergunta' => 'Um investimento apresentou os seguintes resultados: Ano 1: +20%, Ano 2: -20%. Qual afirmação é correta?',
        'alternativas' => [
            'A) O investidor terminou exatamente com o mesmo valor inicial',
            'B) O investidor terminou com 4% a menos que o valor inicial',
            'C) O investidor ganhou 40% no período',
            'D) O investidor perdeu 20% no total'
        ],
        'correta' => 1
    ],
    // Q16
    [
        'pergunta' => 'Uma pessoa recebeu uma recomendação para investir todo o seu dinheiro em uma única ação porque "essa empresa vai subir com certeza". Qual é o principal problema dessa estratégia?',
        'alternativas' => [
            'A) Ações não podem gerar retorno',
            'B) Existe concentração de risco e não há garantia de valorização',
            'C) Investimentos devem sempre ser feitos em dinheiro físico',
            'D) Uma ação só pode ser comprada por empresas'
        ],
        'correta' => 1
    ],
    // Q17
    [
        'pergunta' => 'Qual situação demonstra melhor uma decisão de investimento coerente?',
        'alternativas' => [
            'A) Escolher um investimento apenas porque um influenciador afirmou que ele vai subir',
            'B) Escolher um investimento considerando objetivo, prazo, liquidez, risco e potencial de retorno',
            'C) Escolher sempre o investimento com a maior rentabilidade passada',
            'D) Escolher o investimento que promete lucro garantido acima de todos os outros'
        ],
        'correta' => 1
    ],
    // Q18
    [
        'pergunta' => 'Uma pessoa investe R$ 5.000 e recebe R$ 500 de rendimento. Durante o mesmo período, a inflação foi de 8%. Considerando apenas essas informações, qual afirmação é mais adequada?',
        'alternativas' => [
            'A) A rentabilidade nominal foi de 10%, e a rentabilidade real foi menor que 10%',
            'B) A rentabilidade real foi exatamente 10%',
            'C) A inflação não influencia o resultado do investimento',
            'D) A rentabilidade nominal foi de 8%'
        ],
        'correta' => 0
    ],
    // Q19
    [
        'pergunta' => 'João possui dois investimentos: Investimento A: baixo risco, alta liquidez e retorno esperado menor. Investimento B: maior risco, menor liquidez e retorno esperado maior. João pretende usar o dinheiro daqui a três meses para uma despesa importante. Qual alternativa demonstra melhor compreensão sobre investimentos?',
        'alternativas' => [
            'A) B é melhor porque sempre devemos buscar o maior retorno',
            'B) A pode ser mais adequado devido ao curto prazo e à necessidade de acesso ao dinheiro',
            'C) B é melhor porque investimentos de maior risco sempre compensam',
            'D) Os dois são necessariamente equivalentes'
        ],
        'correta' => 1
    ],
    // Q20
    [
        'pergunta' => 'Maria possui uma carteira diversificada e está pensando em trocar todos os seus investimentos por um único ativo que apresentou grande valorização recentemente. Qual seria a análise mais adequada?',
        'alternativas' => [
            'A) Fazer a troca, pois rentabilidade passada garante rentabilidade futura',
            'B) Fazer a troca, pois diversificação reduz a rentabilidade',
            'C) Avaliar novamente seus objetivos, prazo, risco, liquidez e diversificação antes de tomar a decisão',
            'D) Evitar qualquer investimento que tenha apresentado valorização'
        ],
        'correta' => 2
    ]
];

// Variáveis para controle do teste
$teste_finalizado = false;
$pontuacao = 0;
$patente = "";
$diagnostico = "";

// Processa o envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar'])) {
    $pontuacao = 0;
    $total_questoes = count($questoes);
    
    for ($i = 0; $i < $total_questoes; $i++) {
        $resposta = isset($_POST['q' . $i]) ? intval($_POST['q' . $i]) : -1;
        if ($resposta === $questoes[$i]['correta']) {
            $pontuacao++;
        }
    }
    
    // Define a patente baseada na pontuação
    if ($pontuacao <= 2) {
        $patente = "Ferro 1";
        $diagnostico = "Conhecimento muito inicial";
    } elseif ($pontuacao <= 4) {
        $patente = "Ferro 2";
        $diagnostico = "Reconhece alguns conceitos básicos";
    } elseif ($pontuacao <= 6) {
        $patente = "Ferro 3";
        $diagnostico = "Possui noções básicas de investimentos";
    } elseif ($pontuacao <= 8) {
        $patente = "Ouro 1";
        $diagnostico = "Compreende conceitos fundamentais";
    } elseif ($pontuacao <= 10) {
        $patente = "Ouro 2";
        $diagnostico = "Demonstra conhecimento básico consistente";
    } elseif ($pontuacao <= 12) {
        $patente = "Ouro 3";
        $diagnostico = "Já compreende risco, retorno e planejamento";
    } else {
        $patente = "Esmeralda 1";
        $diagnostico = "Conhecimento intermediário";
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
    
    $teste_finalizado = true;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste Diagnóstico</title>
    <link rel="stylesheet" href="../css/app.css">
    
</head>
<body>

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
            <?= csrf_field() ?>
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
                    <?php if ($pontuacao <= 6): ?>
                        <i class="fas fa-graduation-cap" style="color: #FF4757;"></i>
                    <?php elseif ($pontuacao <= 12): ?>
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

</body>
</html>
