<?php
session_start();

// Protege a página: só usuários logados podem acessar o analisador
if (!isset($_SESSION['id'])) {
    header('Location: ../front-end/login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisador de Gastos · FinControl</title>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/analisador.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
</head>

<body>

    <?php
        include '../front-end/navbar.php';
    ?>

<main>

    <!-- HERO DO ANALISADOR -->

    <section id="analisador-hero" class="hero">

        <div class="hero_container">

            <div class="hero_content">

                <span class="tag">
                    Análise Inteligente com IA
                </span>

                <h1>
                    Envie seu extrato e descubra para onde seu dinheiro está indo
                </h1>

                <p>
                    Envie um arquivo PDF ou CSV do seu extrato bancário. Nossa IA organiza
                    os lançamentos por categoria e sugere formas práticas de economizar.
                </p>

            </div>

            <div class="hero_card">

                <div class="card_dashboard">

                    <div class="saldo">
                        <span>Exemplo de Resultado</span>
                        <h2>R$ 2.903</h2>
                    </div>

                    <div class="dashboard_info">
                        <div>
                            <span>Maior Categoria</span>
                            <h3>Alimentação</h3>
                        </div>

                        <div>
                            <span>Economia Sugerida</span>
                            <h3>R$ 310</h3>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </section>

    <!-- ÁREA DE UPLOAD -->

    <section id="upload" class="section_dark">

        <div class="section_title">
            <h2>Envie seu extrato</h2>
            <p>
                Aceita arquivos em PDF ou CSV, de até 10&nbsp;MB.
            </p>
        </div>

        <div class="analisador_box">

            <form id="upload-form">

                <label for="file-input" class="upload_slot" id="slot">

                    <svg class="upload_slot_icon" width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 26V32C8 33.1046 8.89543 34 10 34H30C31.1046 34 32 33.1046 32 32V26" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M20 6V25" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M13 15L20 6L27 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>

                    <span class="upload_slot_label" id="slot-label">Solte o extrato aqui ou toque para escolher</span>
                    <span class="upload_slot_hint">PDF ou CSV · até 10&nbsp;MB</span>

                    <input type="file" id="file-input" name="extrato" accept=".pdf,.csv" hidden>
                </label>

                <div class="file_chip" id="file-chip" hidden>
                    <span class="file_chip_name" id="file-chip-name"></span>
                    <button type="button" class="file_chip_remove" id="file-chip-remove" aria-label="Remover arquivo">×</button>
                </div>

                <button type="submit" class="btn_primary btn_full" id="submit-btn" disabled>
                    Analisar Gastos
                </button>

                <p class="upload_status" id="status" role="status"></p>

            </form>

            <div class="resultado_box" id="resultado" hidden>
                <h3>Resumo dos seus gastos</h3>
                <p id="resultado-texto"></p>
            </div>

        </div>

    </section>

    <!-- COMO FUNCIONA -->

    <section class="section_light">

        <div class="section_title">
            <h2>Como funciona a análise</h2>
            <p>
                Três passos simples até você ver o resumo dos seus gastos.
            </p>
        </div>

        <div class="steps">

            <div class="step_card">
                <div class="step_number">01</div>
                <h3>Envie</h3>
                <p>
                    Faça upload do seu extrato em PDF ou CSV.
                </p>
            </div>

            <div class="step_card">
                <div class="step_number">02</div>
                <h3>Processamos</h3>
                <p>
                    A IA lê os lançamentos e identifica categorias de gastos.
                </p>
            </div>

            <div class="step_card">
                <div class="step_number">03</div>
                <h3>Receba o resumo</h3>
                <p>
                    Veja onde seu dinheiro foi e dicas para economizar.
                </p>
            </div>

        </div>

    </section>

</main>

<footer>

    <div class="footer_container">

        <img class="footer_logo" src="img/logo.png" alt="Logo">

        <p>
            © 2026 FinControl. Todos os direitos reservados.
        </p>

    </div>

</footer>

<script>
    const form = document.getElementById('upload-form');
    const slot = document.getElementById('slot');
    const slotLabel = document.getElementById('slot-label');
    const fileInput = document.getElementById('file-input');
    const fileChip = document.getElementById('file-chip');
    const fileChipName = document.getElementById('file-chip-name');
    const removeBtn = document.getElementById('file-chip-remove');
    const submitBtn = document.getElementById('submit-btn');
    const statusEl = document.getElementById('status');
    const resultadoBox = document.getElementById('resultado');
    const resultadoTexto = document.getElementById('resultado-texto');

    // >>> ajuste aqui para o endereço do seu backend Python <<<
    const API_URL = 'http://localhost:5000/api/analisar';

    function setFile(file) {
        if (!file) return;
        fileInput.files = createFileList(file);
        fileChipName.textContent = file.name;
        fileChip.hidden = false;
        slotLabel.textContent = 'Arquivo pronto para envio';
        submitBtn.disabled = false;
    }

    function createFileList(file) {
        const dt = new DataTransfer();
        dt.items.add(file);
        return dt.files;
    }

    function resetFile() {
        fileInput.value = '';
        fileChip.hidden = true;
        slotLabel.textContent = 'Solte o extrato aqui ou toque para escolher';
        submitBtn.disabled = true;
    }

    fileInput.addEventListener('change', () => {
        if (fileInput.files[0]) setFile(fileInput.files[0]);
    });

    removeBtn.addEventListener('click', resetFile);

    ['dragenter', 'dragover'].forEach(evt =>
        slot.addEventListener(evt, e => {
            e.preventDefault();
            slot.classList.add('upload_slot--drag');
        })
    );

    ['dragleave', 'drop'].forEach(evt =>
        slot.addEventListener(evt, e => {
            e.preventDefault();
            slot.classList.remove('upload_slot--drag');
        })
    );

    slot.addEventListener('drop', e => {
        const file = e.dataTransfer.files[0];
        if (file) setFile(file);
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const file = fileInput.files[0];
        if (!file) return;

        submitBtn.disabled = true;
        statusEl.textContent = 'Lendo o extrato…';
        statusEl.className = 'upload_status upload_status--pending';

        const formData = new FormData();
        formData.append('extrato', file);

        try {
            const res = await fetch(API_URL, { method: 'POST', body: formData });
            if (!res.ok) throw new Error('Falha na análise');

            const data = await res.json();
            statusEl.textContent = 'Resumo pronto — veja abaixo.';
            statusEl.className = 'upload_status upload_status--ok';

            resultadoTexto.textContent = data.resumo;
            resultadoBox.hidden = false;
            resultadoBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

            submitBtn.disabled = false;

        } catch (err) {
            statusEl.textContent = 'Não consegui analisar o arquivo. Tente novamente.';
            statusEl.className = 'upload_status upload_status--error';
            submitBtn.disabled = false;
        }
    });
</script>

</body>
</html>