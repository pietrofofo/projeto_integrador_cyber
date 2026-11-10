<footer class="site-footer">
  <div class="footer-cols">
    <div class="footer-brand">
      <a href="<?= BASE_URL ?>/index.php" class="brand">
        <span class="brand-dot"></span>
        <?= e(SITE_NAME) ?>
      </a>
      <p>Jornalismo dedicado à conscientização, prevenção e combate ao cyberbullying no Brasil.</p>
      <div class="social-row">
        <a class="social-btn" data-share-social="whatsapp" data-url="<?= e(BASE_URL) ?>" title="WhatsApp">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.5 8.5 0 0 1-12.4 7.5L3 20l1.1-5.4A8.5 8.5 0 1 1 21 11.5z"/></svg>
        </a>
        <a class="social-btn" data-share-social="twitter" data-url="<?= e(BASE_URL) ?>" title="Twitter/X">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 4s-.7 2-2 3c1.4 8-7 13-15 8 2.5.1 4-.6 5-2C6 12 4 10 4 7c1.5 1 3 1 4-1-2-3 1-6 3-4 1.5-1.5 4-1.5 5 0"/></svg>
        </a>
        <a class="social-btn" data-share-social="facebook" data-url="<?= e(BASE_URL) ?>" title="Facebook">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h-2a5 5 0 0 0-5 5v2H6v4h2v7h4v-7h3l1-4h-4V8a1 1 0 0 1 1-1h3z"/></svg>
        </a>
      </div>
    </div>
    <div>
      <h4>CATEGORIAS</h4>
      <div class="footer-col-links">
        <a href="<?= BASE_URL ?>/index.php?categoria=prevencao">Prevenção</a>
        <a href="<?= BASE_URL ?>/index.php?categoria=legislacao">Legislação</a>
        <a href="<?= BASE_URL ?>/index.php?categoria=depoimentos">Depoimentos</a>
        <a href="<?= BASE_URL ?>/index.php?categoria=escolas">Escolas</a>
        <a href="<?= BASE_URL ?>/index.php?categoria=tecnologia">Tecnologia</a>
      </div>
    </div>
    <div>
      <h4>AJUDA</h4>
      <div class="footer-col-links">
        <a href="<?= BASE_URL ?>/central-denuncias.php">Central de Denúncias</a>
        <a href="<?= BASE_URL ?>/central-denuncias.php#canais">Canais de apoio</a>
        <a href="<?= BASE_URL ?>/cadastro.php">Criar conta</a>
      </div>
    </div>
    <div>
      <h4>RECEBA ATUALIZAÇÕES</h4>
      <form style="display:flex; gap:8px;" onsubmit="return false;">
        <input type="email" class="form-control" placeholder="seu@email.com" style="padding:10px 12px; font-size:13px;">
        <button type="submit" class="btn btn-primary btn-sm">Assinar</button>
      </form>
    </div>
  </div>
  <div class="footer-bottom">
    <span>&copy; <?= date('Y') ?> CyberWatch — Projeto acadêmico. Todos os direitos reservados.</span>
    <div style="display:flex; gap:18px;">
      <a href="#">Privacidade</a>
      <a href="#">Termos de uso</a>
    </div>
  </div>
</footer>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
