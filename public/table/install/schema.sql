-- KuKu'Ku Table Game — MySQL / MariaDB schema
-- Import once:  mysql -u USER -p DBNAME < install/schema.sql

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS tracks (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title        VARCHAR(120) NOT NULL,
  audio_file   VARCHAR(255) NULL,          -- uploads/audio/...
  cover_file   VARCHAR(255) NULL,          -- uploads/covers/...
  apple_url    VARCHAR(500) NULL,          -- link to the track on Apple Music
  start_sec    INT UNSIGNED NOT NULL DEFAULT 0,  -- where the fragment starts (e.g. the chorus)
  sort_order   INT NOT NULL DEFAULT 0,
  active       TINYINT(1) NOT NULL DEFAULT 1,
  created_at   INT UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS games (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code            CHAR(4) NOT NULL,
  phase           VARCHAR(16) NOT NULL DEFAULT 'lobby',  -- lobby | listen | answer | summarizing | summary | finished
  position        INT NOT NULL DEFAULT -1,               -- index in game_tracks
  phase_started   INT UNSIGNED NOT NULL DEFAULT 0,
  answer_deadline INT UNSIGNED NOT NULL DEFAULT 0,
  host_lang       CHAR(2) NOT NULL DEFAULT 'en',
  version         INT UNSIGNED NOT NULL DEFAULT 0,       -- bumps on every change, clients re-render
  created_at      INT UNSIGNED NOT NULL,
  finished_at     INT UNSIGNED NULL,
  KEY idx_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS game_tracks (
  game_id   INT UNSIGNED NOT NULL,
  position  INT NOT NULL,
  track_id  INT UNSIGNED NOT NULL,
  PRIMARY KEY (game_id, position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS players (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  game_id    INT UNSIGNED NOT NULL,
  token      CHAR(32) NOT NULL,
  lang       CHAR(2) NOT NULL DEFAULT 'en',
  track_id   INT UNSIGNED NULL,          -- the card = track title, works as a pseudonym
  last_seen  INT UNSIGNED NOT NULL,
  created_at INT UNSIGNED NOT NULL,
  UNIQUE KEY uq_token (token),
  UNIQUE KEY uq_card (game_id, track_id),
  KEY idx_game (game_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS answers (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  game_id    INT UNSIGNED NOT NULL,
  position   INT NOT NULL,
  player_id  INT UNSIGNED NOT NULL,
  body       VARCHAR(300) NOT NULL,     -- empty string = skipped
  created_at INT UNSIGNED NOT NULL,
  UNIQUE KEY uq_answer (game_id, position, player_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS summaries (
  game_id    INT UNSIGNED NOT NULL,
  position   INT NOT NULL,
  source     VARCHAR(10) NOT NULL,      -- ai | fallback
  body_json  TEXT NOT NULL,             -- {"en":{"summary":"","talk":""},"ru":{...},"el":{...}} or {"quotes":[...]}
  created_at INT UNSIGNED NOT NULL,
  PRIMARY KEY (game_id, position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS leads (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  game_id    INT UNSIGNED NOT NULL,
  player_id  INT UNSIGNED NOT NULL,
  card_title VARCHAR(120) NULL,
  name       VARCHAR(120) NOT NULL,
  phone      VARCHAR(40)  NULL,
  email      VARCHAR(160) NULL,
  instagram  VARCHAR(80)  NULL,
  lang       CHAR(2) NOT NULL DEFAULT 'en',
  consent    TINYINT(1) NOT NULL DEFAULT 0,
  created_at INT UNSIGNED NOT NULL,
  UNIQUE KEY uq_player (player_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS join_attempts (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ip         VARCHAR(45) NOT NULL,
  created_at INT UNSIGNED NOT NULL,
  KEY idx_ip (ip, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
