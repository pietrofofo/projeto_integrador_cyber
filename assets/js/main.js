// CyberWatch — interatividade do front-end (sem dependências externas)

document.addEventListener('DOMContentLoaded', function () {
  aplicarTemaSalvo();
  configurarToggleTema();
  configurarMenuMobile();
  configurarCompartilhamento();
  configurarEditorRico();
});

/* ---------- Modo escuro ---------- */
function aplicarTemaSalvo() {
  const salvo = localStorage.getItem('cyberwatch_tema');
  if (salvo === 'dark') {
    document.documentElement.setAttribute('data-theme', 'dark');
  }
}

function configurarToggleTema() {
  const botao = document.getElementById('theme-toggle');
  if (!botao) return;

  botao.addEventListener('click', function () {
    const atual = document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
    const novo = atual === 'dark' ? 'light' : 'dark';
    if (novo === 'dark') {
      document.documentElement.setAttribute('data-theme', 'dark');
    } else {
      document.documentElement.removeAttribute('data-theme');
    }
    localStorage.setItem('cyberwatch_tema', novo);
  });
}

/* ---------- Menu mobile ---------- */
function configurarMenuMobile() {
  const botao = document.getElementById('menu-toggle');
  const menu = document.getElementById('mobile-nav');
  if (!botao || !menu) return;

  botao.addEventListener('click', function () {
    menu.classList.toggle('open');
  });
}

/* ---------- Compartilhamento (nativo + redes sociais) ---------- */
function configurarCompartilhamento() {
  document.querySelectorAll('[data-share-native]').forEach(function (botao) {
    botao.addEventListener('click', async function () {
      const url = botao.dataset.url || window.location.href;
      const titulo = botao.dataset.title || document.title;

      if (navigator.share) {
        try {
          await navigator.share({ title: titulo, url: url });
        } catch (erro) {
          // usuário cancelou o compartilhamento — nada a fazer
        }
      } else {
        try {
          await navigator.clipboard.writeText(url);
          botao.dataset.labelOriginal = botao.dataset.labelOriginal || botao.textContent;
          botao.textContent = 'Link copiado!';
          setTimeout(function () {
            botao.textContent = botao.dataset.labelOriginal;
          }, 2000);
        } catch (erro) {
          window.prompt('Copie o link da matéria:', url);
        }
      }
    });
  });

  document.querySelectorAll('[data-share-social]').forEach(function (link) {
    const rede = link.dataset.shareSocial;
    const url = encodeURIComponent(link.dataset.url || window.location.href);
    const titulo = encodeURIComponent(link.dataset.title || document.title);

    const destinos = {
      whatsapp: 'https://wa.me/?text=' + titulo + '%20' + url,
      twitter: 'https://twitter.com/intent/tweet?text=' + titulo + '&url=' + url,
      facebook: 'https://www.facebook.com/sharer/sharer.php?u=' + url,
    };

    if (destinos[rede]) {
      link.setAttribute('href', destinos[rede]);
      link.setAttribute('target', '_blank');
      link.setAttribute('rel', 'noopener noreferrer');
    }
  });
}

/* ---------- Editor de texto rico (painel do jornalista) ---------- */
function configurarEditorRico() {
  const editor = document.getElementById('editor-conteudo');
  const campoOculto = document.getElementById('conteudo-html');
  if (!editor || !campoOculto) return;

  document.execCommand('defaultParagraphSeparator', false, 'p');

  document.querySelectorAll('[data-cmd]').forEach(function (botao) {
    botao.addEventListener('click', function (evento) {
      evento.preventDefault();
      const cmd = botao.dataset.cmd;
      const valor = botao.dataset.value || null;
      editor.focus();
      document.execCommand(cmd, false, valor);
      sincronizarEditor();
    });
  });

  editor.addEventListener('input', sincronizarEditor);
  sincronizarEditor();

  function sincronizarEditor() {
    campoOculto.value = editor.innerHTML;
  }
}
