<?php
/** Funções utilitárias compartilhadas por todo o site. */

function e(?string $texto): string
{
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_verify(): void
{
    $enviado = $_POST['csrf_token'] ?? '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $enviado)) {
        http_response_code(403);
        die('Sessão expirada ou requisição inválida. Volte e tente novamente.');
    }
}

function flash(string $tipo, string $mensagem): void
{
    $_SESSION['flash'][] = ['tipo' => $tipo, 'mensagem' => $mensagem];
}

function pegar_flashes(): array
{
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

function slugify(string $texto): string
{
    $mapa = [
        'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
        'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
        'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
        'ç' => 'c', 'ñ' => 'n',
    ];
    $texto = strtolower(strtr($texto, $mapa));
    $texto = preg_replace('/[^a-z0-9]+/', '-', $texto);
    return trim($texto, '-');
}

function slug_unico(PDO $pdo, string $titulo, ?int $ignorarId = null): string
{
    $base = slugify($titulo);
    $slug = $base;
    $i = 2;
    while (true) {
        $sql = 'SELECT id FROM noticias WHERE slug = ?';
        $params = [$slug];
        if ($ignorarId) {
            $sql .= ' AND id != ?';
            $params[] = $ignorarId;
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        if (!$stmt->fetch()) {
            return $slug;
        }
        $slug = $base . '-' . $i;
        $i++;
    }
}

function resumo(string $textoHtml, int $tamanho = 160): string
{
    $texto = trim(strip_tags($textoHtml));
    if (mb_strlen($texto) <= $tamanho) {
        return $texto;
    }
    return mb_substr($texto, 0, $tamanho) . '…';
}

function tempo_leitura(string $textoHtml): int
{
    $palavras = str_word_count(strip_tags($textoHtml));
    return max(1, (int) ceil($palavras / 200));
}

function data_br(string $timestamp): string
{
    $meses = [1=>'jan',2=>'fev',3=>'mar',4=>'abr',5=>'mai',6=>'jun',7=>'jul',8=>'ago',9=>'set',10=>'out',11=>'nov',12=>'dez'];
    $dt = new DateTime($timestamp);
    return $dt->format('d') . ' ' . $meses[(int) $dt->format('n')] . ' ' . $dt->format('Y');
}

function url_atual(): string
{
    $esquema = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $esquema . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}
