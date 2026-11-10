<?php
require_once __DIR__ . '/config/config.php';

$tipo = $_GET['tipo'] ?? 'geral';
$noticiaId = $_GET['noticia_id'] ?? '';
$comentarioId = $_GET['comentario_id'] ?? '';
$usuario = usuario_logado();

$tituloPagina = 'Central de Apoio e Denúncias — ' . SITE_NAME;
require_once __DIR__ . '/includes/header.php';
?>

<div class="support-page-hero">
  <span class="tag">APOIO</span>
  <h1 style="margin-top:14px;">Central de Apoio e Denúncias</h1>
  <p>Você não está sozinho(a). Encontre orientação, apoio psicológico e canais oficiais para denunciar casos de cyberbullying com segurança e sigilo. Toda denúncia enviada por aqui é registrada e analisada pela nossa equipe de moderação.</p>
</div>

<div id="canais" class="help-channels-grid">
  <div class="help-channel-card">
    <strong>CVV — 188</strong>
    <span>Apoio emocional 24h por telefone, chat e e-mail, totalmente gratuito.</span>
  </div>
  <div class="help-channel-card">
    <strong>SaferNet Brasil</strong>
    <span>Canal especializado em denúncias de crimes e violações de direitos na internet.</span>
  </div>
  <div class="help-channel-card">
    <strong>Disque 100</strong>
    <span>Disque Direitos Humanos, para denúncias de violência contra crianças e adolescentes.</span>
  </div>
</div>

<div id="denunciar" class="report-form-wrapper">
  <div class="auth-card">
    <h1>Fazer uma denúncia</h1>
    <p class="auth-sub">Preencha os campos abaixo. Se preferir, você pode denunciar sem se identificar.</p>

    <form action="<?= BASE_URL ?>/actions/denuncia_action.php" method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="noticia_id" value="<?= e($noticiaId) ?>">
      <input type="hidden" name="comentario_id" value="<?= e($comentarioId) ?>">

      <div class="form-group">
        <label for="tipo_select">Tipo de denúncia</label>
        <select id="tipo_select" name="tipo_select" class="form-control">
          <option value="geral" <?= $tipo === 'geral' ? 'selected' : '' ?>>Denúncia geral / relato</option>
          <option value="noticia" <?= $tipo === 'noticia' ? 'selected' : '' ?>>Sobre uma matéria específica</option>
          <option value="comentario" <?= $tipo === 'comentario' ? 'selected' : '' ?>>Sobre um comentário específico</option>
        </select>
      </div>

      <?php if (!$usuario): ?>
        <div class="form-group">
          <label for="nome_contato">Seu nome (opcional)</label>
          <input type="text" id="nome_contato" name="nome_contato" class="form-control">
        </div>
        <div class="form-group">
          <label for="email_contato">Seu e-mail (opcional, para retorno)</label>
          <input type="email" id="email_contato" name="email_contato" class="form-control">
        </div>
      <?php endif; ?>

      <div class="form-group">
        <label for="descricao">Descreva o que aconteceu</label>
        <textarea id="descricao" name="descricao" class="form-control" required minlength="10" maxlength="2000" placeholder="Conte com detalhes o que você quer denunciar..."></textarea>
      </div>

      <button type="submit" class="btn btn-primary btn-block">Enviar denúncia</button>
      <p class="form-hint" style="text-align:center; margin-top:14px;">Sua denúncia é registrada de forma segura e analisada pela equipe de moderação do CyberWatch.</p>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
