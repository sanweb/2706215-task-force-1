CREATE DATABASE IF NOT EXISTS yeticave
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_0900_ai_ci;

USE yeticave;

-- User
CREATE TABLE IF NOT EXISTS `user` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `email`         VARCHAR(255) NOT NULL,
    `name`          VARCHAR(128) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `city_id`       INT UNSIGNED NOT NULL, -- FK city.id

    -- avatar field should be placed here if only users with excutor role have user_profile
    -- otherwise we shoud create two separate tables: customer_profile and executor_profile
    `avatar`        VARCHAR(255) NULL, -- profile image path (uploads/user/avatar/hashed_image_name.ext)

    -- checkbox я собираюсь откликаться на заказы (role = executor, user has executor_stats and user_profile|executor_profile)
    -- `can_bid`       TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    `role`          ENUM('customer', 'executor') NOT NULL,

    -- register_way (enum and separate table for OAuth registrations)?

    `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP    NULL, -- managed by application model

    UNIQUE KEY `uq_user_email` (`email`)

) ENGINE=InnoDB;

-- User profile (executor_profile?)
-- ТЗ: Страница для показа подробной информации об исполнителе. Страница предназначена только для показа профилей исполнителей.
-- ТЗ: Соответственно, если этот пользователь не является исполнителем, то страница должна быть недоступна: вместо неё надо показывать ошибку 404.
CREATE TABLE IF NOT EXISTS `user_profile` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id`       INT UNSIGNED NOT NULL, -- FK user.id

    -- `avatar`        VARCHAR(255) NULL, -- image path (uploads/user/avatar/hashed_image_name.ext)
    `birthday`      DATE         NULL, -- valid date
    `phone`         VARCHAR(20)  NULL, -- 11 numbers
    `telegram`      VARCHAR(64)  NULL, -- up to 64 chars without @
    `about`         TEXT         NULL, -- about me

    `hide_my_contacts` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0, -- profile option отключить показ своих контактных данных для всех, кроме заказчика

    `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP    NULL, -- managed by application model

    UNIQUE KEY `uq_user_id` (`user_id`)

) ENGINE=InnoDB;

-- Category
CREATE TABLE IF NOT EXISTS `category` (
    `id`    INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name`  VARCHAR(64)  NOT NULL,
    `slug`  VARCHAR(64)  NOT NULL,

    UNIQUE KEY `uq_category_name` (`name`),
    UNIQUE KEY `uq_category_slug` (`slug`)

) ENGINE=InnoDB;

-- Executor specialization (user-to-category)
CREATE TABLE IF NOT EXISTS `user_specialization` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id`       INT UNSIGNED NOT NULL, -- FK user.id
    `category_id`   INT UNSIGNED NOT NULL, -- FK category.id

    `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY `uq_user_category` (`user_id`, `category_id`),
    KEY `idx_user_specialization_user_id` (`user_id`),
    KEY `idx_user_specialization_category_id` (`category_id`)

) ENGINE=InnoDB;

-- Task
CREATE TABLE IF NOT EXISTS `task` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `customer_id`   INT UNSIGNED NOT NULL, -- FK user.id
    `category_id`   INT UNSIGNED NOT NULL, -- FK category.id
    `executor_id`   INT UNSIGNED NULL,     -- FK user.id

    `title`         VARCHAR(255) NOT NULL, -- min length 10
    `description`   TEXT         NOT NULL, -- min length 30
    `budget`        INT UNSIGNED NOT NULL, -- int > 0
    `expire_date`   DATE         NOT NULL, -- ГГГГ-ММ-ДД

    `status`        ENUM('new', 'canceled', 'assigned', 'completed', 'failed') NOT NULL DEFAULT 'new',

    -- ТЗ: «Удалённая работа» — добавляет к условию фильтрации показ заданий только без географической привязки
    `is_remote`     TINYINT UNSIGNED NOT NULL DEFAULT 0, -- extra field ? to index for filter/search
    -- ТЗ: При выборе локации пользователь вводит город/район/улицу, а геокодер на стороне клиента подставляет значения (широта, долгота, название города) в скрытые поля формы.
    -- ТЗ: Если поле «Локация» не было заполнено, то задание сохраняется без географической привязки. В этом случае id города и координаты в задании отсутствуют.
    `location`      VARCHAR(255) NULL, -- location address typed by user
    `city_id`       INT UNSIGNED NULL, -- FK city.id
    `lat`           DECIMAL(10,7) NULL,
    `lng`           DECIMAL(10,7) NULL,

    `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP    NULL, -- managed by application model

    -- Indexes for filtering tasks
    KEY `idx_task_customer_id` (`customer_id`),
    KEY `idx_task_category_id` (`category_id`),
    KEY `idx_task_status` (`status`),
    KEY `idx_task_city_id` (`city_id`),
    KEY `idx_task_executor_id` (`executor_id`),
    KEY `idx_task_expire_date` (`expire_date`),
    KEY `idx_task_created_at`  (`created_at`)

    -- Full-text index for searching task by title and description ТЗ: Поиск заданий по категориям и названию
    -- FULLTEXT KEY `ft_task_title_description` (`title`, `description`)

) ENGINE=InnoDB;

-- Task attachment
-- ТЗ: Файлы. Задание не обязано содержать прикреплённые файлы. Загруженные файлы могут быть любого формата.
CREATE TABLE IF NOT EXISTS `task_attachment` (
    `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    -- `customer_id` INT UNSIGNED NOT NULL, -- FK user.id extra field
    `task_id`   INT UNSIGNED NOT NULL, -- FK user.id

    `file_path` VARCHAR(255) NULL, -- file path without name (uploads/task/task_id/)
    `file_name` VARCHAR(255) NULL, -- file name (hashed_file_name.ext) to get the file
    `file_original_name` VARCHAR(255) NULL, -- user file original name (hashed_file_name.ext) to show in task

    `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    -- Indexes
    KEY `idx_task_id` (`task_id`)

) ENGINE=InnoDB;

-- Bid
CREATE TABLE IF NOT EXISTS `bid` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id`    INT UNSIGNED NOT NULL, -- FK user.id
    `task_id`    INT UNSIGNED NOT NULL, -- FK task.id

    `price`      INT UNSIGNED NOT NULL, -- int > 0

    `status`     ENUM('new', 'accepted', 'rejected') NOT NULL DEFAULT 'new',

    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP    NULL, -- managed by application model

    UNIQUE KEY `uq_bid_user_task` (`user_id`, `task_id`),
    KEY `idx_bid_user_created_at` (`user_id`, `created_at`),
    KEY `idx_bid_task_created_at` (`task_id`, `created_at`)

) ENGINE=InnoDB;

-- Review (customer`s review)
CREATE TABLE IF NOT EXISTS `review` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INT UNSIGNED NOT NULL, -- FK user.id
    `task_id`     INT UNSIGNED NOT NULL, -- FK task.id
    `executor_id` INT UNSIGNED NOT NULL, -- FK user.id to show reviews on executor page without extra join task table

    `score`       TINYINT UNSIGNED NOT NULL, -- score: 1..5
    `comment`     TEXT NULL, -- customer's review

    `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY `uq_review_task` (`task_id`),
    KEY `idx_review_customer_created_at` (`customer_id`, `created_at`),
    KEY `idx_review_executor_created_at` (`customer_id`, `created_at`),
    KEY `idx_review_task_created_at` (`task_id`, `created_at`)

) ENGINE=InnoDB;

-- Executor statistics (updates on adding a new review)
CREATE TABLE IF NOT EXISTS `executor_stats` (
    `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `executor_id`       INT UNSIGNED NOT NULL, -- FK user.id

    `completed_tasks`   INT UNSIGNED NOT NULL DEFAULT 0, -- completed task counter
    `failed_tasks`      INT UNSIGNED NOT NULL DEFAULT 0, -- failed task counter
    `avg_score`         DECIMAL(3,2) NULL, -- based on reviews = сумма всех оценок из отзывов / (кол-во отзывов + счетчик проваленных заданий).
    `rating_position`   INT UNSIGNED NULL, -- based on avg_score

    `status`            ENUM('open', 'busy') NOT NULL DEFAULT 'open',

    `updated_at`        TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP, -- updates on change

    UNIQUE KEY `uq_executor_stats_executor_id` (`executor_id`)

) ENGINE=InnoDB;

-- City dictionary
CREATE TABLE IF NOT EXISTS `city` (
    `id`    INT UNSIGNED  NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name`  VARCHAR(255)  NOT NULL,
    `lat`   DECIMAL(10,7) NOT NULL,
    `lng`   DECIMAL(10,7) NOT NULL,

    UNIQUE KEY `uq_city_name` (`name`)

) ENGINE=InnoDB;
