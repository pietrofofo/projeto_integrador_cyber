<?php
require_once __DIR__ . '/config/config.php';

if (esta_logado()) {
    redirect('/index.php');
}

$tituloPagina = 'Entrar — ' . SITE_NAME;
require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-wrapper">
  <div class="auth-card">
    <h1>Bem-vindo de volta</h1>
    <p class="auth-sub">Entre para comentar, favoritar matérias e acompanhar suas denúncias.</p>

    <form action="<?= BASE_URL ?>/actions/login_action.php" method="post">
      <?= csrf_field() ?>
      <div class="form-group">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" class="form-control" required autofocus>
      </div>
      <div class="form-group">
        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Entrar</button>
    </form>

    <p class="form-footer">Ainda não tem conta? <a href="<?= BASE_URL ?>/cadastro.php">Cadastre-se</a></p>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
