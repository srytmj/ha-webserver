-- ============================================================
-- HA Web Server — Database Initialization Script
-- Jalankan di Aurora Master Endpoint:
-- mysql -h [AURORA_WRITER_ENDPOINT] -u admin -p < init.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS ha_webserver
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE ha_webserver;

CREATE TABLE IF NOT EXISTS user (
    id         INT          NOT NULL AUTO_INCREMENT,
    nama       VARCHAR(100) NOT NULL,
    nim        VARCHAR(20)  NOT NULL,
    foto       VARCHAR(500) DEFAULT NULL COMMENT 'S3 public URL path',
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_nim (nim),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample data (opsional)
INSERT IGNORE INTO user (nama, nim, foto) VALUES
    ('Budi Santoso', '2021001001', NULL),
    ('Siti Rahayu',  '2021001002', NULL),
    ('Ahmad Fauzi',  '2021001003', NULL);

SHOW TABLES;
DESCRIBE user;
SELECT COUNT(*) AS total_rows FROM user;
