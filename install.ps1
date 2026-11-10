<#
  CyberWatch — instalador automatico (Windows + XAMPP)
  Copia o projeto para o htdocs do XAMPP, cria o banco de dados,
  importa o schema com o charset correto e popula dados de teste.
  Uso: basta rodar install.bat (que chama este script).
#>

$ErrorActionPreference = 'Continue'

function Titulo($texto) {
    Write-Host ""
    Write-Host "=== $texto ===" -ForegroundColor Cyan
}

function Erro($texto) {
    Write-Host $texto -ForegroundColor Red
}

function Aviso($texto) {
    Write-Host $texto -ForegroundColor Yellow
}

Titulo "CyberWatch - instalador automatico"

# ------------------------------------------------------------------
# 1) Localizar o XAMPP
# ------------------------------------------------------------------
$xamppRoot = $null
foreach ($letra in @('C', 'D', 'E', 'F')) {
    $candidato = "$letra`:\xampp"
    if (Test-Path (Join-Path $candidato 'php\php.exe')) {
        $xamppRoot = $candidato
        break
    }
}

if (-not $xamppRoot) {
    Aviso "Nao encontrei uma instalacao do XAMPP neste computador."
    Write-Host "1) Baixe e instale o XAMPP (com PHP 8+ e MySQL/MariaDB):"
    Write-Host "   https://www.apachefriends.org/pt_br/download.html"
    Write-Host "2) Depois de instalado, rode este 'install.bat' de novo."
    Read-Host "`nPressione ENTER para fechar"
    exit 1
}

Write-Host "XAMPP encontrado em: $xamppRoot"

$phpExe = Join-Path $xamppRoot 'php\php.exe'
$mysqlExe = Join-Path $xamppRoot 'mysql\bin\mysql.exe'
$htdocs = Join-Path $xamppRoot 'htdocs'
$destino = Join-Path $htdocs 'Cyber'
$origem = $PSScriptRoot

# ------------------------------------------------------------------
# 2) Copiar o projeto para htdocs\Cyber
# ------------------------------------------------------------------
Titulo "Copiando arquivos do projeto"

$origemResolvida = (Resolve-Path $origem).Path.TrimEnd('\')
$destinoNormalizado = $destino.TrimEnd('\')

if ($origemResolvida -ieq $destinoNormalizado) {
    Write-Host "Projeto ja esta em '$destino', nada para copiar."
}
else {
    New-Item -ItemType Directory -Force -Path $destino | Out-Null
    robocopy $origem $destino /MIR /NFL /NDL /NJH /NJS /XD ".git" | Out-Null
    Write-Host "Arquivos copiados para $destino"
}

# ------------------------------------------------------------------
# 3) Garantir que Apache e MySQL estao rodando
# ------------------------------------------------------------------
Titulo "Verificando Apache e MySQL"

function Iniciar-Servico($nomeProcesso, $scriptBat) {
    $proc = Get-Process -Name $nomeProcesso -ErrorAction SilentlyContinue
    if (-not $proc) {
        $caminhoBat = Join-Path $xamppRoot $scriptBat
        if (Test-Path $caminhoBat) {
            Write-Host "Iniciando $nomeProcesso ..."
            Start-Process -FilePath $caminhoBat -WindowStyle Hidden | Out-Null
            Start-Sleep -Seconds 5
        }
    }
    else {
        Write-Host "$nomeProcesso ja esta rodando."
    }
}

Iniciar-Servico 'httpd' 'apache_start.bat'
Iniciar-Servico 'mysqld' 'mysql_start.bat'
Start-Sleep -Seconds 2

if (-not (Get-Process -Name 'mysqld' -ErrorAction SilentlyContinue)) {
    Erro "Nao consegui iniciar o MySQL automaticamente."
    Write-Host "Abra o 'XAMPP Control Panel', inicie o MySQL manualmente e rode este instalador de novo."
    Read-Host "`nPressione ENTER para fechar"
    exit 1
}

if (-not (Get-Process -Name 'httpd' -ErrorAction SilentlyContinue)) {
    Aviso "Nao consegui iniciar o Apache automaticamente. Abra o 'XAMPP Control Panel' e inicie o Apache."
}

# ------------------------------------------------------------------
# 4) Criar banco e importar schema (charset correto)
# ------------------------------------------------------------------
Titulo "Configurando banco de dados"

$schema = Join-Path $destino 'database\schema.sql'

& $mysqlExe -u root --default-character-set=utf8mb4 -e "DROP DATABASE IF EXISTS cyberwatch;" 2>$null

# Importante: usar redirecionamento via cmd (bytes crus do arquivo), nunca
# Get-Content/pipeline do PowerShell — o PowerShell 5.1 re-decodifica o texto
# com a codepage do sistema e corrompe acentos (ex: "ç" vira "Ã§") antes de
# repassar ao mysql.exe, mesmo com --default-character-set=utf8mb4.
cmd /c "`"$mysqlExe`" -u root --default-character-set=utf8mb4 < `"$schema`""

if ($LASTEXITCODE -ne 0) {
    Erro "Nao consegui conectar no MySQL com usuario 'root' sem senha."
    Write-Host "Se o MySQL do seu XAMPP tiver senha para o root, edite:"
    Write-Host "  $destino\config\database.php"
    Write-Host "e ajuste `$DB_PASS antes de rodar este instalador de novo."
    Read-Host "`nPressione ENTER para fechar"
    exit 1
}

Write-Host "Banco de dados 'cyberwatch' criado e importado."

# ------------------------------------------------------------------
# 5) Popular usuarios e noticias de teste
# ------------------------------------------------------------------
Titulo "Criando usuarios e noticias de exemplo"
& $phpExe (Join-Path $destino 'database\seed.php')

# ------------------------------------------------------------------
# 6) Descobrir a porta do Apache e montar a URL final
# ------------------------------------------------------------------
$porta = 80
$httpdConf = Join-Path $xamppRoot 'apache\conf\httpd.conf'
if (Test-Path $httpdConf) {
    $linha = Select-String -Path $httpdConf -Pattern '^\s*Listen\s+(\d+)\s*$' | Select-Object -First 1
    if ($linha) {
        $porta = $linha.Matches[0].Groups[1].Value
    }
}

$url = "http://localhost:$porta/Cyber/index.php"

Titulo "Instalacao concluida!"
Write-Host "Acesse: $url" -ForegroundColor Green
Write-Host ""
Write-Host "Logins de teste:"
Write-Host "  Admin:       admin@cyberwatch.test / admin123"
Write-Host "  Jornalista:  jornalista@cyberwatch.test / jornalista123"
Write-Host "  Leitor:      leitor@cyberwatch.test / leitor123"
Write-Host ""

Start-Process $url
Read-Host "Pressione ENTER para fechar"
