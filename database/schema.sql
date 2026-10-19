-- DWP Arabia website — MySQL schema (MySQL 8+ / MariaDB 10.5+)
-- Usage: mysql -u <user> -p <database> < database/schema.sql

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS leads (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  form_type       ENUM('lead','demo','quote','short') NOT NULL DEFAULT 'lead',
  lang            CHAR(2) NOT NULL,
  full_name       VARCHAR(120) NOT NULL,
  company         VARCHAR(160) NOT NULL,
  job_title       VARCHAR(120) NULL,
  email           VARCHAR(190) NOT NULL,
  mobile          VARCHAR(40)  NOT NULL,
  employees       VARCHAR(10)  NULL,
  interest        VARCHAR(20)  NULL,
  current_system  VARCHAR(20)  NULL,
  users_count     INT UNSIGNED NULL,
  demo_date       DATE NULL,
  demo_time       VARCHAR(20) NULL,
  demo_language   CHAR(2) NULL,
  demo_format     VARCHAR(10) NULL,
  message         TEXT NULL,
  consent         TINYINT(1) NOT NULL DEFAULT 0,
  utm_source      VARCHAR(255) NULL,
  utm_medium      VARCHAR(255) NULL,
  utm_campaign    VARCHAR(255) NULL,
  utm_term        VARCHAR(255) NULL,
  utm_content     VARCHAR(255) NULL,
  gclid           VARCHAR(255) NULL,
  li_fat_id       VARCHAR(255) NULL,
  fbclid          VARCHAR(255) NULL,
  landing_page    VARCHAR(500) NULL,
  page_path       VARCHAR(500) NULL,
  ip              VARCHAR(45)  NULL,
  user_agent      VARCHAR(255) NULL,
  email_status    ENUM('pending','sent','failed') NOT NULL DEFAULT 'pending',
  crm_status      ENUM('pending','sent','failed','skipped') NOT NULL DEFAULT 'pending',
  status          ENUM('new','contacted','qualified','closed') NOT NULL DEFAULT 'new',
  KEY idx_created (created_at),
  KEY idx_ip_created (ip, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS assessments (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  token         CHAR(32) NOT NULL UNIQUE,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  lang          CHAR(2) NOT NULL,
  full_name     VARCHAR(120) NOT NULL,
  company       VARCHAR(160) NOT NULL,
  job_title     VARCHAR(120) NULL,
  email         VARCHAR(190) NOT NULL,
  mobile        VARCHAR(40)  NOT NULL,
  answers_json  JSON NOT NULL,
  score         TINYINT UNSIGNED NOT NULL,
  band          ENUM('strong','good','odoo') NOT NULL,
  consent       TINYINT(1) NOT NULL DEFAULT 0,
  utm_source    VARCHAR(255) NULL,
  utm_medium    VARCHAR(255) NULL,
  utm_campaign  VARCHAR(255) NULL,
  utm_term      VARCHAR(255) NULL,
  utm_content   VARCHAR(255) NULL,
  gclid         VARCHAR(255) NULL,
  li_fat_id     VARCHAR(255) NULL,
  fbclid        VARCHAR(255) NULL,
  landing_page  VARCHAR(500) NULL,
  page_path     VARCHAR(500) NULL,
  ip            VARCHAR(45)  NULL,
  crm_status    ENUM('pending','sent','failed','skipped') NOT NULL DEFAULT 'pending',
  KEY idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS applications (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  lang        CHAR(2) NOT NULL,
  full_name   VARCHAR(120) NOT NULL,
  email       VARCHAR(190) NOT NULL,
  mobile      VARCHAR(40)  NOT NULL,
  role_slug   VARCHAR(120) NULL,
  message     TEXT NULL,
  cv_path     VARCHAR(255) NOT NULL,
  consent     TINYINT(1) NOT NULL DEFAULT 0,
  ip          VARCHAR(45) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insights (blog). One row per language; EN and AR share the slug.
CREATE TABLE IF NOT EXISTS insights (
  id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug              VARCHAR(160) NOT NULL,
  lang              CHAR(2) NOT NULL,
  title             VARCHAR(200) NOT NULL,
  meta_description  VARCHAR(300) NOT NULL,
  h1                VARCHAR(200) NULL,
  excerpt           VARCHAR(400) NULL,
  author_name       VARCHAR(120) NULL,
  author_title      VARCHAR(120) NULL,
  author_photo      VARCHAR(255) NULL,
  author_linkedin   VARCHAR(255) NULL,
  published_at      DATE NULL,
  updated_at        DATE NULL,
  reading_minutes   TINYINT UNSIGNED NOT NULL DEFAULT 5,
  body_html         MEDIUMTEXT NOT NULL,
  faq_json          JSON NULL,
  related_url       VARCHAR(200) NULL,
  related_label     VARCHAR(200) NULL,
  status            ENUM('draft','published') NOT NULL DEFAULT 'draft',
  UNIQUE KEY uq_slug_lang (slug, lang)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS careers (
  id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug              VARCHAR(160) NOT NULL,
  lang              CHAR(2) NOT NULL,
  title             VARCHAR(200) NOT NULL,
  meta_description  VARCHAR(300) NOT NULL,
  location          VARCHAR(120) NOT NULL DEFAULT 'Riyadh',
  employment_type   ENUM('FULL_TIME','PART_TIME','CONTRACTOR','INTERN') NOT NULL DEFAULT 'FULL_TIME',
  date_posted       DATE NOT NULL,
  valid_through     DATE NULL,
  body_html         MEDIUMTEXT NOT NULL,
  status            ENUM('open','closed') NOT NULL DEFAULT 'open',
  UNIQUE KEY uq_slug_lang (slug, lang)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS case_studies (
  id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug              VARCHAR(160) NOT NULL,
  lang              CHAR(2) NOT NULL,
  client            VARCHAR(160) NOT NULL,
  industry          VARCHAR(120) NOT NULL,
  title             VARCHAR(200) NOT NULL,
  meta_description  VARCHAR(300) NOT NULL,
  summary           VARCHAR(400) NOT NULL,
  challenge_html    TEXT NOT NULL,
  solution_html     TEXT NOT NULL,
  results_html      TEXT NOT NULL,
  quote             TEXT NULL,
  quote_by          VARCHAR(200) NULL,
  sort              INT NOT NULL DEFAULT 0,
  status            ENUM('draft','published') NOT NULL DEFAULT 'draft',
  updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_slug_lang (slug, lang)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Only real, approved testimonials are rendered (spec B7).
CREATE TABLE IF NOT EXISTS testimonials (
  id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  lang      CHAR(2) NOT NULL,
  quote     TEXT NOT NULL,
  person    VARCHAR(120) NOT NULL,
  role      VARCHAR(120) NULL,
  company   VARCHAR(160) NULL,
  approved  TINYINT(1) NOT NULL DEFAULT 0,
  sort      INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Client logos for the trust strip / clients page; only approved = 1 (written permission) are shown.
CREATE TABLE IF NOT EXISTS client_logos (
  id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name      VARCHAR(160) NOT NULL,
  logo      VARCHAR(255) NOT NULL COMMENT 'path under public/assets/, e.g. img/clients/acme.webp',
  approved  TINYINT(1) NOT NULL DEFAULT 0,
  sort      INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_users (
  id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email          VARCHAR(190) NOT NULL UNIQUE,
  password_hash  VARCHAR(255) NOT NULL,
  created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_login_at  DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_login_attempts (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ip          VARCHAR(45) NOT NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_ip_created (ip, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
