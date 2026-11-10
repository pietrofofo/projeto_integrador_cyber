readme
Acesse em: http://localhost:8080/Cyber/index.php

Logins de teste:

Admin: admin@cyberwatch.test / admin123 → http://localhost:8080/Cyber/admin/index.php
Jornalista: jornalista@cyberwatch.test / jornalista123
Leitor: leitor@cyberwatch.test / leitor123


O que o dev precisa fazer:
Ter o XAMPP instalado (único pré-requisito manual — não dá pra automatizar a instalação de um programa externo sem risco).
Extrair o zip em qualquer pasta.
Dar duplo clique em install.bat.
O script (install.ps1) faz tudo sozinho:

Detecta o XAMPP (procura em C:, D:, E:, F:)
Copia o projeto para htdocs\Cyber
Garante que Apache e MySQL estão rodando (inicia se preciso)
Recria o banco cyberwatch e importa o schema com o charset correto (evitando o bug de acentuação que você viu antes)
Roda o seed (usuários de teste + notícias de exemplo)
Detecta a porta real do Apache e abre o navegador direto na home
No fim ele mostra e abre automaticamente a URL certa, com os 3 logins de teste (admin/jornalista/leitor).