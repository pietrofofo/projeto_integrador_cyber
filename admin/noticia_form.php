<?php
require_once __DIR__ . '/../config/config.php';
exigir_papel('jornalista', 'admin');

$usuario = usuario_logado();
$ehAdmin = tem_papel('admin');
$id = $_GET['id'] ?? null;
$noticia = null;

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM noticias WHERE id = ?');
    $stmt->execute([$id]);
    $noticia = $stmt->fetch();

    if (!$noticia) {
        flash('erro', 'Matéria não encontrada.');
        redirect('/admin/noticias.php');
    }
    if (!$ehAdmin && (int) $noticia['autor_id'] !== (int) $usuario['id']) {
        http_response_code(403);
        die('Você só pode editar suas próprias matérias.');
    }
}

$categorias = $pdo->query('SELECT * FROM categorias ORDER BY nome')->fetchAll();

$tituloPagina = $noticia ? 'Editar matéria' : 'Nova matéria';
$paginaAtiva = 'noticias';
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-topbar">
  <h1 style="font-size:22px;"><?= $noticia ? 'Editar matéria' : 'Nova matéria' ?></h1>
</div>

<form action="<?= BASE_URL ?>/admin/noticia_save.php" method="post">
  <?= csrf_field() ?>
  <?php if ($noticia): ?>
    <input type="hidden" name="id" value="<?= $noticia['id'] ?>">
  <?php endif; ?>

  <div class="form-group">
    <label for="titulo">Título</label>
    <input type="text" id="titulo" name="titulo" class="form-control" required value="<?= e($noticia['titulo'] ?? '') ?>">
  </div>

  <div class="form-group">
    <label for="subtitulo">Subtítulo / linha fina</label>
    <textarea id="subtitulo" name="subtitulo" class="form-control" style="min-height:60px;"><?= e($noticia['subtitulo'] ?? '') ?></textarea>
  </div>

  <div class="form-group" style="display:flex; gap:16px;">
    <div style="flex:1;">
      <label for="categoria_id">Categoria</label>
      <select id="categoria_id" name="categoria_id" class="form-control">
        <?php foreach ($categorias as $c): ?>
          <option value="<?= $c['id'] ?>" <?= ($noticia && $noticia['categoria_id'] == $c['id']) ? 'selected' : '' ?>><?= e($c['nome']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div style="flex:1;">
      <label for="status">Status</label>
      <select id="status" name="status" class="form-control">
        <option value="rascunho" <?= (!$noticia || $noticia['status'] === 'rascunho') ? 'selected' : '' ?>>Rascunho</option>
        <option value="publicado" <?= ($noticia && $noticia['status'] === 'publicado') ? 'selected' : '' ?>>Publicado</option>
      </select>
    </div>
  </div>

  <div class="form-group">
    <label>Conteúdo</label>
    <div class="editor-toolbar">
      <button type="button" data-cmd="bold"><b>B</b></button>
      <button type="button" data-cmd="italic"><i>I</i></button>
      <button type="button" data-cmd="insertUnorderedList">• Lista</button>
      <button type="button" data-cmd="formatBlock" data-value="H2">H2</button>
      <button type="button" data-cmd="formatBlock" data-value="P">P</button>
      <button type="button" data-cmd="createLink" onclick="this.dataset.value = prompt('URL do link:', 'https://')">Link</button>
    </div>
    <div id="editor-conteudo" class="editor-content" contenteditable="true"><?= $noticia['conteudo'] ?? '<p>Escreva a matéria aqui...</p>' ?></div>
    <input type="hidden" id="conteudo-html" name="conteudo">
  </div>

  <div style="display:flex; gap:12px; margin-top:24px;">
    <button type="submit" class="btn btn-primary">Salvar</button>
    <a href="<?= BASE_URL ?>/admin/noticias.php" class="btn btn-outline">Cancelar</a>
  </div>
</form>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
