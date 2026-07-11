CREATE DATABASE IF NOT EXISTS taskforce_db
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_0900_ai_ci;

USE taskforce_db;

-- City dictionary the first table because of user.city_id FK
CREATE TABLE IF NOT EXISTS `city` (
    `id`    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name`  VARCHAR(255)    NOT NULL,
    `lat`   DECIMAL(10,7)   NOT NULL,
    `lng`   DECIMAL(10,7)   NOT NULL,

    UNIQUE INDEX `uq_city_name` (`name`)

) ENGINE=InnoDB;

-- User
CREATE TABLE IF NOT EXISTS `user` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, -- bigint | uuid 7-9 ver for all tables in db
    `email`         VARCHAR(255)    NOT NULL, -- unique
    `name`          VARCHAR(128)    NOT NULL,
    `password`      VARCHAR(255)    NULL, -- not null if user have to enter login/password during OAuth registration?
    `city_id`       BIGINT UNSIGNED NULL, -- FK city.id

    `avatar`        VARCHAR(255)    NULL, -- user image path (uploads/user/avatar/hashed_image_name.ext)
    `birthday`      DATE            NULL, -- valid date

    -- checkbox я собираюсь откликаться на заказы (all users can create task, but only executors can bid)
    `is_executor`   BOOLEAN         NOT NULL DEFAULT FALSE,

    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP       NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE INDEX `uq_user_email` (`email`),
    -- CONSTRAINT `uq_user_email`
    --    UNIQUE (`email`),

    -- CONSTRAINT `chk_user_is_executor`
        CHECK (`is_executor` IN (FALSE, TRUE)), -- or CHECK (`is_executor` IN (0, 1)), ?

    CONSTRAINT `fk_user_city`
        FOREIGN KEY (`city_id`)
        REFERENCES `city` (`id`)

) ENGINE=InnoDB;

-- Executor profile
-- ТЗ: Страница для показа подробной информации об исполнителе. Страница предназначена только для показа профилей исполнителей.
-- ТЗ: Соответственно, если этот пользователь не является исполнителем, то страница должна быть недоступна: вместо неё надо показывать ошибку 404.
CREATE TABLE IF NOT EXISTS `executor_profile` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, -- use user_id instead?
    `user_id`       BIGINT UNSIGNED NOT NULL, -- FK user.id

    `phone`         VARCHAR(20)     NULL, -- 11 numbers
    `telegram`      VARCHAR(64)     NULL, -- up to 64 chars without @
    `about`         TEXT            NULL, -- about me

    -- отключить показ своих контактных данных для всех, кроме заказчика
    `hide_my_contacts` BOOLEAN      NOT NULL DEFAULT FALSE,

    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP       NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE INDEX `uq_user_id` (`user_id`),
    -- CONSTRAINT `uq_executor_profile_user_id`
    --    UNIQUE (`user_id`),

    -- CONSTRAINT `chk_executor_profile_hide_contacts`
    --    CHECK (`hide_my_contacts` IN (0, 1)),
        CHECK (`hide_my_contacts` IN (FALSE, TRUE)),

    CONSTRAINT `fk_executor_profile_user`
        FOREIGN KEY (`user_id`)
        REFERENCES `user` (`id`)

) ENGINE=InnoDB;

-- Category
CREATE TABLE IF NOT EXISTS `category` (
    `id`    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name`  VARCHAR(64)     NOT NULL,
    `slug`  VARCHAR(64)     NOT NULL,

    UNIQUE INDEX `uq_category_name` (`name`),
    UNIQUE INDEX `uq_category_slug` (`slug`)

    -- CONSTRAINT `uq_category_name`
    --    UNIQUE (`name`),

    -- CONSTRAINT `uq_category_slug`
    --    UNIQUE (`slug`)

) ENGINE=InnoDB;

-- Executor specialization (user-to-category or executor_profile-to-category ON executor_profile_id?)
CREATE TABLE IF NOT EXISTS `executor_specialization` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id`       BIGINT UNSIGNED NOT NULL, -- FK user.id
    `category_id`   BIGINT UNSIGNED NOT NULL, -- FK category.id

    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE INDEX `uq_user_category` (`user_id`, `category_id`),
    -- CONSTRAINT `uq_executor_specialization_user_category`
    --    UNIQUE (`user_id`, `category_id`),

    CONSTRAINT `fk_executor_specialization_user`
        FOREIGN KEY (`user_id`)
        REFERENCES `user` (`id`),

    CONSTRAINT `fk_executor_specialization_category`
        FOREIGN KEY (`category_id`)
        REFERENCES `category` (`id`)

) ENGINE=InnoDB;

-- Task
CREATE TABLE IF NOT EXISTS `task` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `customer_id`   BIGINT UNSIGNED NOT NULL, -- FK user.id
    `category_id`   BIGINT UNSIGNED NOT NULL, -- FK category.id
    `executor_id`   BIGINT UNSIGNED NULL,     -- FK user.id

    `title`         VARCHAR(255)    NOT NULL, -- min length 10
    `description`   TEXT            NOT NULL, -- min length 30
    `budget`        BIGINT UNSIGNED NOT NULL, -- int > 0
    `expire_date`   DATE            NOT NULL, -- ГГГГ-ММ-ДД

    `status`        VARCHAR(32)     NOT NULL DEFAULT 'new',

    -- ТЗ: «Удалённая работа» — добавляет к условию фильтрации показ заданий только без географической привязки
    -- ТЗ: При выборе локации пользователь вводит город/район/улицу, а геокодер на стороне клиента подставляет значения (широта, долгота, название города) в скрытые поля формы.
    -- ТЗ: Если поле «Локация» не было заполнено, то задание сохраняется без географической привязки. В этом случае id города и координаты в задании отсутствуют.
    `location`      VARCHAR(255)    NULL, -- location address typed by user
    `city_id`       BIGINT UNSIGNED NULL, -- FK city.id
    `lat`           DECIMAL(10,7)   NULL,
    `lng`           DECIMAL(10,7)   NULL,

    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP       NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT `chk_task_budget_positive`
        CHECK (`budget` > 0),

    CONSTRAINT `chk_task_status`
        CHECK (`status` IN ('new', 'canceled', 'in_progress', 'completed', 'failed')),

    INDEX `idx_task_expire_date` (`expire_date`),
    INDEX `idx_task_created_at`  (`created_at`),

    CONSTRAINT `fk_task_customer`
        FOREIGN KEY (`customer_id`)
        REFERENCES `user` (`id`),

    CONSTRAINT `fk_task_category`
        FOREIGN KEY (`category_id`)
        REFERENCES `category` (`id`),

    CONSTRAINT `fk_task_executor`
        FOREIGN KEY (`executor_id`)
        REFERENCES `user` (`id`),

    CONSTRAINT `fk_task_city`
        FOREIGN KEY (`city_id`)
        REFERENCES `city` (`id`)

) ENGINE=InnoDB;

-- Task attachment
-- ТЗ: Файлы. Задание не обязано содержать прикреплённые файлы. Загруженные файлы могут быть любого формата.
CREATE TABLE IF NOT EXISTS `attachment` (
    `id`            BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT PRIMARY KEY,
    -- `customer_id`   BIGINT UNSIGNED     NOT NULL, -- FK user.id extra field?
    `task_id`       BIGINT UNSIGNED     NOT NULL, -- FK task.id

    `file_path`     VARCHAR(255)        NOT NULL, -- file path with hashed name (uploads/task/task_id/hashed_file_name.ext)
    `original_name` VARCHAR(255)        NOT NULL, -- user file original name to show in task and download

    `created_at`    TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT `fk_attachment_task`
        FOREIGN KEY (`task_id`)
        REFERENCES `task` (`id`)

) ENGINE=InnoDB;

-- Bid
CREATE TABLE IF NOT EXISTS `bid` (
    `id`        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id`   BIGINT UNSIGNED NOT NULL, -- FK user.id
    `task_id`   BIGINT UNSIGNED NOT NULL, -- FK task.id

    `price`     BIGINT UNSIGNED NOT NULL, -- int > 0

    `status`    VARCHAR(16) NOT NULL DEFAULT 'new',

    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT `uq_bid_user_task`
        UNIQUE (`user_id`, `task_id`),

    CONSTRAINT `chk_bid_price_positive`
        CHECK (`price` > 0),

    CONSTRAINT `chk_bid_status`
        CHECK (`status` IN ('new', 'accepted', 'rejected')),

    CONSTRAINT `fk_bid_user`
        FOREIGN KEY (`user_id`)
        REFERENCES `user` (`id`),

    CONSTRAINT `fk_bid_task`
        FOREIGN KEY (`task_id`)
        REFERENCES `task` (`id`)

) ENGINE=InnoDB;

-- Review (customer`s review)
CREATE TABLE IF NOT EXISTS `review` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `customer_id` BIGINT UNSIGNED NOT NULL, -- FK user.id
    `task_id`     BIGINT UNSIGNED NOT NULL, -- FK task.id
    `executor_id` BIGINT UNSIGNED NOT NULL, -- FK user.id to show reviews on executor page without extra join task table

    `score`       TINYINT UNSIGNED NOT NULL, -- score: 1..5
    `comment`     TEXT NULL, -- customer's review

    `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT `chk_review_score`
        CHECK (`score` BETWEEN 1 AND 5),

    UNIQUE INDEX `uq_review_task` (`task_id`),
    -- CONSTRAINT `uq_review_task`
    --    UNIQUE (`task_id`),

    INDEX `idx_review_customer_created_at` (`customer_id`, `created_at`),
    INDEX `idx_review_executor_created_at` (`executor_id`, `created_at`),

    CONSTRAINT `fk_review_customer`
        FOREIGN KEY (`customer_id`)
        REFERENCES `user` (`id`),

    CONSTRAINT `fk_review_task`
        FOREIGN KEY (`task_id`)
        REFERENCES `task` (`id`),

    CONSTRAINT `fk_review_executor`
        FOREIGN KEY (`executor_id`)
        REFERENCES `user` (`id`)

) ENGINE=InnoDB;

-- Executor statistics (view)
CREATE OR REPLACE VIEW `executor_stats_view` AS
SELECT
    u.id AS executor_id,
    u.name,
    COUNT(DISTINCT CASE WHEN t.status = 'completed' THEN t.id END) AS completed_tasks,
    COUNT(DISTINCT CASE WHEN t.status = 'failed' THEN t.id END) AS failed_tasks,
    ROUND(AVG(r.score), 2) AS avg_score,
    -- use window function to calculate rating_position
    ROW_NUMBER() OVER (ORDER BY ROUND(AVG(r.score), 2) DESC) AS rating_position
FROM `user` u
LEFT JOIN `task` AS t
    ON t.executor_id = u.id
LEFT JOIN `review` AS r
    ON r.executor_id = u.id
WHERE u.is_executor = TRUE
GROUP BY u.id;
