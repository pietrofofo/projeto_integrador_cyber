-- CyberWatch — Esquema do banco de dados (MySQL)
-- Importe este arquivo no phpMyAdmin ou via:
--   mysql -u root -p --default-character-set=utf8mb4 < schema.sql
-- IMPORTANTE (Windows): sem "--default-character-set=utf8mb4" o cliente mysql
-- costuma assumir o codepage do terminal (ex: cp850) e corrompe acentos (ção, ã, é...).

CREATE DATABASE IF NOT EXISTS cyberwatch CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cyberwatch;

-- ==========================================================
-- USUÁRIOS
-- ==========================================================
CREATE TABLE usuarios (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    nome          VARCHAR(120) NOT NULL,
    email         VARCHAR(160) NOT NULL UNIQUE,
    senha_hash    VARCHAR(255) NOT NULL,
    papel         ENUM('leitor', 'jornalista', 'admin') NOT NULL DEFAULT 'leitor',
    avatar_url    VARCHAR(255) NULL,
    criado_em     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ==========================================================
-- CATEGORIAS
-- ==========================================================
CREATE TABLE categorias (
    id      INT AUTO_INCREMENT PRIMARY KEY,
    nome    VARCHAR(60) NOT NULL,
    slug    VARCHAR(60) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ==========================================================
-- NOTÍCIAS
-- ==========================================================
CREATE TABLE noticias (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    titulo         VARCHAR(220) NOT NULL,
    subtitulo      VARCHAR(400) NULL,
    slug           VARCHAR(255) NOT NULL UNIQUE,
    conteudo       LONGTEXT NOT NULL,
    imagem_capa    VARCHAR(255) NULL,
    categoria_id   INT NOT NULL,
    autor_id       INT NOT NULL,
    status         ENUM('rascunho', 'publicado') NOT NULL DEFAULT 'rascunho',
    publicado_em   TIMESTAMP NULL,
    criado_em      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT,
    FOREIGN KEY (autor_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FULLTEXT KEY ft_busca (titulo, subtitulo, conteudo)
) ENGINE=InnoDB;

-- ==========================================================
-- COMENTÁRIOS
-- Publicação imediata (fica visível na hora); admin modera depois
-- ==========================================================
CREATE TABLE comentarios (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    noticia_id   INT NOT NULL,
    usuario_id   INT NOT NULL,
    texto        TEXT NOT NULL,
    status       ENUM('visivel', 'oculto') NOT NULL DEFAULT 'visivel',
    criado_em    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (noticia_id) REFERENCES noticias(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==========================================================
-- DENÚNCIAS
-- Central de Apoio e Denúncias: denúncia de notícia, comentário ou geral
-- ==========================================================
CREATE TABLE denuncias (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    tipo            ENUM('noticia', 'comentario', 'geral') NOT NULL DEFAULT 'geral',
    noticia_id      INT NULL,
    comentario_id   INT NULL,
    denunciante_id  INT NULL,
    nome_contato    VARCHAR(120) NULL,
    email_contato   VARCHAR(160) NULL,
    descricao       TEXT NOT NULL,
    status          ENUM('pendente', 'em_analise', 'resolvida') NOT NULL DEFAULT 'pendente',
    criado_em       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (noticia_id) REFERENCES noticias(id) ON DELETE SET NULL,
    FOREIGN KEY (comentario_id) REFERENCES comentarios(id) ON DELETE SET NULL,
    FOREIGN KEY (denunciante_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ==========================================================
-- FAVORITOS (leitor favorita notícias)
-- ==========================================================
CREATE TABLE favoritos (
    usuario_id  INT NOT NULL,
    noticia_id  INT NOT NULL,
    criado_em   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (usuario_id, noticia_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (noticia_id) REFERENCES noticias(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==========================================================
-- SEED — categorias fixas do portal
-- ==========================================================
INSERT INTO categorias (nome, slug) VALUES
    ('Prevenção', 'prevencao'),
    ('Legislação', 'legislacao'),
    ('Depoimentos', 'depoimentos'),
    ('Escolas', 'escolas'),
    ('Tecnologia', 'tecnologia');
