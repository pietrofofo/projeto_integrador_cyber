<?php
/**
 * Executar UMA VEZ pelo navegador (ex: http://localhost/Cyber/database/seed.php)
 * depois de importar schema.sql. Cria usuários de teste e algumas notícias
 * de exemplo já publicadas, para o feed da home não nascer vazio.
 */
require_once __DIR__ . '/../config/database.php';

function slugify(string $texto): string
{
    $texto = strtolower($texto);
    $texto = preg_replace('/[áàâãä]/u', 'a', $texto);
    $texto = preg_replace('/[éèêë]/u', 'e', $texto);
    $texto = preg_replace('/[íìîï]/u', 'i', $texto);
    $texto = preg_replace('/[óòôõö]/u', 'o', $texto);
    $texto = preg_replace('/[úùûü]/u', 'u', $texto);
    $texto = preg_replace('/[ç]/u', 'c', $texto);
    $texto = preg_replace('/[^a-z0-9]+/', '-', $texto);
    return trim($texto, '-');
}

$usuarios = [
    ['nome' => 'Admin CyberWatch', 'email' => 'admin@cyberwatch.test', 'senha' => 'admin123', 'papel' => 'admin'],
    ['nome' => 'Juliana Prado',    'email' => 'jornalista@cyberwatch.test', 'senha' => 'jornalista123', 'papel' => 'jornalista'],
    ['nome' => 'Bianca Ferreira',  'email' => 'leitor@cyberwatch.test', 'senha' => 'leitor123', 'papel' => 'leitor'],
];

$idsUsuarios = [];
foreach ($usuarios as $u) {
    $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
    $stmt->execute([$u['email']]);
    $existente = $stmt->fetch();
    if ($existente) {
        $idsUsuarios[$u['papel']] = $existente['id'];
        continue;
    }
    $hash = password_hash($u['senha'], PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, senha_hash, papel) VALUES (?, ?, ?, ?)');
    $stmt->execute([$u['nome'], $u['email'], $hash, $u['papel']]);
    $idsUsuarios[$u['papel']] = $pdo->lastInsertId();
}

$categorias = $pdo->query('SELECT slug, id FROM categorias')->fetchAll(PDO::FETCH_KEY_PAIR);

$noticias = [
    [
        'titulo' => 'Como o cyberbullying está mudando o comportamento de uma geração inteira de estudantes',
        'subtitulo' => 'Levantamento inédito do CyberWatch com psicólogos e educadores mostra como a exposição constante a ataques on-line altera rotina de sono, desempenho escolar e relações sociais de adolescentes brasileiros.',
        'categoria' => 'escolas',
        'conteudo' => "<p>Levantamento inédito do CyberWatch, conduzido em parceria com psicólogos escolares e educadores de doze escolas em três estados brasileiros, mostra como a exposição constante a ataques on-line está alterando profundamente a rotina de milhares de estudantes.</p><p>Segundo os dados coletados, adolescentes que relatam episódios frequentes de cyberbullying apresentam queda significativa na qualidade do sono, no desempenho escolar e nas relações sociais presenciais.</p><p>Especialistas ouvidos pela reportagem reforçam a importância de canais de escuta ativa dentro das escolas e da criação de comitês de mediação entre os próprios estudantes.</p>",
    ],
    [
        'titulo' => 'Novo projeto de lei propõe pena mais dura para assédio virtual contra menores',
        'subtitulo' => 'Câmara analisa proposta que endurece punições para casos de perseguição digital envolvendo crianças e adolescentes.',
        'categoria' => 'legislacao',
        'conteudo' => "<p>Está em tramitação na Câmara dos Deputados um projeto de lei que endurece as penas para crimes de assédio virtual quando as vítimas são crianças ou adolescentes.</p><p>O texto prevê aumento de pena em até dois terços quando o crime for cometido por meio de perfis falsos ou grupos de mensagens.</p>",
    ],
    [
        'titulo' => '"Fiquei um ano sem sair de casa", relata jovem vítima de perseguição on-line',
        'subtitulo' => 'Aos 16 anos, Bianca reconstruiu a autoestima após meses de ataques em grupos de mensagens.',
        'categoria' => 'depoimentos',
        'conteudo' => "<p>Bianca tinha 15 anos quando um grupo de colegas de escola começou a compartilhar montagens ofensivas com sua imagem em grupos de mensagens.</p><p>\"Fiquei praticamente um ano sem sair de casa. Só depois de procurar ajuda psicológica e denunciar formalmente consegui voltar a ter uma rotina normal\", relata.</p>",
    ],
    [
        'titulo' => 'Redes sociais lançam ferramenta de denúncia automática de conteúdo ofensivo',
        'subtitulo' => 'Novo recurso usa inteligência artificial para identificar comentários agressivos antes que sejam publicados.',
        'categoria' => 'tecnologia',
        'conteudo' => "<p>Uma das maiores redes sociais do país anunciou nesta semana um novo recurso de moderação automática, capaz de identificar comentários potencialmente ofensivos antes mesmo de serem publicados.</p><p>A ferramenta usa um modelo de linguagem treinado especificamente para reconhecer padrões de assédio e discurso de ódio em português.</p>",
    ],
    [
        'titulo' => 'Cinco sinais de que seu filho pode estar sendo vítima de cyberbullying',
        'subtitulo' => 'Especialistas listam mudanças de comportamento que merecem atenção dos pais.',
        'categoria' => 'prevencao',
        'conteudo' => "<p>Mudanças bruscas de humor, isolamento social, queda no rendimento escolar e recusa em usar o celular são alguns dos sinais que pais e responsáveis devem observar.</p><p>Especialistas reforçam que o diálogo aberto e sem julgamento é o primeiro passo para que a criança ou adolescente se sinta segura para pedir ajuda.</p>",
    ],
    [
        'titulo' => 'Escolas públicas de SP criam comitês de mediação digital entre alunos',
        'subtitulo' => 'Iniciativa reúne estudantes para mediar conflitos on-line antes que virem casos disciplinares.',
        'categoria' => 'escolas',
        'conteudo' => "<p>Uma rede de escolas públicas da capital paulista passou a adotar comitês formados pelos próprios estudantes para mediar conflitos que nascem nas redes sociais.</p><p>A iniciativa já reduziu em 30% os encaminhamentos disciplinares relacionados a brigas iniciadas on-line, segundo a coordenação pedagógica.</p>",
    ],
];

foreach ($noticias as $n) {
    $slug = slugify($n['titulo']);
    $stmt = $pdo->prepare('SELECT id FROM noticias WHERE slug = ?');
    $stmt->execute([$slug]);
    if ($stmt->fetch()) {
        continue;
    }
    $stmt = $pdo->prepare(
        'INSERT INTO noticias (titulo, subtitulo, slug, conteudo, categoria_id, autor_id, status, publicado_em)
         VALUES (?, ?, ?, ?, ?, ?, "publicado", NOW())'
    );
    $stmt->execute([
        $n['titulo'],
        $n['subtitulo'],
        $slug,
        $n['conteudo'],
        $categorias[$n['categoria']],
        $idsUsuarios['jornalista'],
    ]);
}

echo "Seed concluído.\n";
echo "Login admin: admin@cyberwatch.test / admin123\n";
echo "Login jornalista: jornalista@cyberwatch.test / jornalista123\n";
echo "Login leitor: leitor@cyberwatch.test / leitor123\n";
