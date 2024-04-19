CREATE DATABASE IF NOT EXISTS shorts;

USE shorts;

CREATE TABLE IF NOT EXISTS users
(
    id       BIGINT AUTO_INCREMENT PRIMARY KEY,
    email    VARCHAR(255) UNIQUE,
    password CHAR(60)
) COMMENT 'Таблица пользователей';


CREATE TABLE IF NOT EXISTS urls
(
    id         BIGINT AUTO_INCREMENT PRIMARY KEY,
    full_url   VARCHAR(2083) COMMENT 'Полный адрес',
    token      CHAR(7) UNIQUE COMMENT 'Уникальный короткий адрес',
    user_id    BIGINT COMMENT 'ID пользователя',
    view_count INT DEFAULT 0 COMMENT 'Количество переходов по ссылке',
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    FULLTEXT INDEX (full_url)
) COMMENT 'Таблица URL-адресов';


INSERT INTO users (id, email, password) VALUES (1, 'demo@demo.demo', '$2y$10$7r/WHRX7CswgT0pSoG/YXOtuIGWDGE.P0Om0Mgq45D74XJFzOWdUi');
INSERT INTO urls (id, full_url, token, user_id) VALUES (1, 'https://github.com/the-nepodarok', 'demo123', 1);