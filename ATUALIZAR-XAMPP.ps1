$ErrorActionPreference = 'Stop'

$origem = $PSScriptRoot
$destino = 'C:\xampp\htdocs\PTCC-EducacaoFinanceira'
$arquivos = @(
    'back-end\google-callback.php',
    'back-end\chatbot.php',
    'back-end\seguranca.php',
    'front-end\login.php',
    'css\login.css',
    'suporte-ia\app_md.py',
    'suporte-ia\run.py',
    'suporte-ia\requirements.txt',
    'suporte-ia\README.md',
    '.env.example'
)

if (-not (Test-Path -LiteralPath $destino -PathType Container)) {
    throw "Projeto não encontrado em $destino. Edite a variável `$destino no início deste script se necessário."
}

$backup = Join-Path $destino ('.backup-correcao-' + (Get-Date -Format 'yyyyMMdd-HHmmss'))
New-Item -ItemType Directory -Path $backup -Force | Out-Null

foreach ($relativo in $arquivos) {
    $arquivoOrigem = Join-Path $origem $relativo
    $arquivoDestino = Join-Path $destino $relativo
    if (-not (Test-Path -LiteralPath $arquivoOrigem -PathType Leaf)) {
        throw "Arquivo corrigido ausente no pacote: $relativo"
    }
    if (Test-Path -LiteralPath $arquivoDestino -PathType Leaf) {
        $backupDestino = Join-Path $backup $relativo
        New-Item -ItemType Directory -Path (Split-Path $backupDestino -Parent) -Force | Out-Null
        Copy-Item -LiteralPath $arquivoDestino -Destination $backupDestino -Force
    }
    New-Item -ItemType Directory -Path (Split-Path $arquivoDestino -Parent) -Force | Out-Null
    Copy-Item -LiteralPath $arquivoOrigem -Destination $arquivoDestino -Force
}

$callback = Get-Content -Raw -LiteralPath (Join-Path $destino 'back-end\google-callback.php')
if ($callback -match 'google-config\.php') {
    throw 'A atualização não foi aplicada: o callback ainda referencia google-config.php.'
}

Write-Host ''
Write-Host 'Atualização concluída.' -ForegroundColor Green
Write-Host "Backup dos arquivos antigos: $backup"
Write-Host ''
Write-Host 'Próximo passo: crie/preencha C:\xampp\htdocs\PTCC-EducacaoFinanceira\.env com:'
Write-Host 'GOOGLE_CLIENT_ID=...'
Write-Host 'GOOGLE_CLIENT_SECRET=...'
Write-Host 'GOOGLE_REDIRECT_URL=http://localhost/PTCC-EducacaoFinanceira/back-end/google-callback.php'
Write-Host ''
Write-Host 'Depois reinicie o Apache no XAMPP e teste o botão Google.'
