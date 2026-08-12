# Быстрый старт

Инструкция по локальному запуску проекта.

## 1. Клонировать проект

```bash
git clone <repository-url>
cd <project-directory>
```

## 2. Создать файлы окружения

Скопируйте примеры файлов окружения:

```bash
cp .env.example .env
cp .env.mysql.example .env.mysql
```

Файлы используются независимо и предназначены для разных компонентов:

```text
.env
→ загружается PHP-приложением через phpdotenv
→ содержит настройки приложения и подключения к базе данных

.env.mysql
→ передаётся Docker в MySQL-контейнер через --env-file
→ содержит переменные инициализации официального MySQL-образа
```

В `.env.mysql` используются стандартные переменные окружения MySQL:

```dotenv
MYSQL_DATABASE=
MYSQL_USER=
MYSQL_PASSWORD=
MYSQL_ROOT_PASSWORD=
```

В `.env` используются переменные подключения PHP-приложения:

```dotenv
DB_HOST=
DB_PORT=
DB_NAME=
DB_USER=
DB_PASSWORD=
```

Значения `DB_NAME`, `DB_USER` и `DB_PASSWORD` должны соответствовать `MYSQL_DATABASE`, `MYSQL_USER` и `MYSQL_PASSWORD`.

При запуске приложения через Docker PHP-контейнер подключается к MySQL-контейнеру по имени контейнера внутри Docker-сети:

```dotenv
DB_HOST=taskforce-yii2-mysql
DB_PORT=3306
```

Порт `3309` используется для доступа к MySQL с хостовой машины:

```text
localhost:3309 → taskforce-yii2-mysql:3306
```

При необходимости измените остальные значения переменных окружения.

### Автоматическая инициализация базы данных

При первом создании MySQL-контейнера файл:

```text
data/sql/schema.sql
```

автоматически подключается в стандартную директорию инициализации MySQL:

```text
/docker-entrypoint-initdb.d/
```

Официальный образ MySQL автоматически выполняет SQL-файлы из этой директории при первой инициализации пустого каталога данных.

Поэтому схема базы данных создаётся автоматически при первом запуске проекта:

```bash
./start.sh
```

При последующих запусках schema.sql повторно не выполняется, так как данные MySQL уже сохранены в:

```text
docker/mysql/data
```

Если schema.sql был изменён и требуется полностью пересоздать базу данных, необходимо удалить существующий MySQL-контейнер и данные, после чего снова запустить проект.

## 3. Запустить проект

Проект можно запустить двумя способами:

1. **Через Docker — рекомендуемый способ**
2. **Локально через PHP**, используя MySQL-контейнер отдельно

Ниже сначала рассмотрен рекомендуемый вариант запуска через Docker.

### Запуск через Docker

```bash
./start.sh
```

Скрипт выполняет необходимые действия для подготовки и запуска проекта:

* создаёт Docker-сеть `taskforce-yii2`, если она ещё не существует;
* создаёт или запускает MySQL-контейнер;
* подключает MySQL-контейнер к Docker-сети при необходимости;
* собирает PHP-образ;
* пересоздаёт PHP-контейнер;
* запускает PHP-контейнер и подключает его к той же Docker-сети.

PHP-приложение и MySQL взаимодействуют внутри сети `taskforce-yii2`.

Альтернативный способ запуска PHP непосредственно на хостовой машине рассмотрен ниже в разделе [Запуск PHP без Docker](#запуск-php-без-docker).

## 4. Открыть проект

Проект доступен в браузере:

```text
http://localhost:8080
```

## 5. Остановить проект

```bash
./stop.sh
```

---

# Полезные команды

## Docker

Посмотреть запущенные контейнеры:

```bash
docker ps
```

Посмотреть все контейнеры:

```bash
docker ps -a
```

Посмотреть логи MySQL-контейнера:

```bash
docker logs taskforce-yii2-mysql
```

Посмотреть логи PHP-контейнера:

```bash
docker logs taskforce-yii2-php
```

Запустить существующий MySQL-контейнер:

```bash
docker start taskforce-yii2-mysql
```

Остановить MySQL-контейнер:

```bash
docker stop taskforce-yii2-mysql
```

Перезапустить MySQL-контейнер:

```bash
docker restart taskforce-yii2-mysql
```

Удалить остановленный MySQL-контейнер:

```bash
docker rm taskforce-yii2-mysql
```

Принудительно остановить и удалить MySQL-контейнер:

```bash
docker rm -f taskforce-yii2-mysql
```

## Docker-сеть

Проверить сеть проекта:

```bash
docker network inspect taskforce-yii2
```

Проверить DNS-разрешение имени MySQL-контейнера непосредственно из PHP-контейнера:

```bash
docker exec taskforce-yii2-php getent hosts taskforce-yii2-mysql
```

Подключить уже существующий контейнер к сети проекта:

```bash
docker network connect taskforce-yii2 taskforce-yii2-mysql
```

## PHP

Проверить версию PHP в образе приложения:

```bash
docker run --rm taskforce-yii2-php php -v
```

Проверить версию PHP в уже запущенном контейнере:

```bash
docker exec taskforce-yii2-php php -v
```

## Composer

Установить зависимости:

```bash
composer install
```

Обновить зависимости:

```bash
composer update
```

Пересоздать файлы автозагрузки:

```bash
composer dump-autoload
```

Если Composer используется внутри PHP-контейнера, команды можно выполнять через `docker exec`, например:

```bash
docker exec taskforce-yii2-php composer install
```

## Пересоздать базу данных

Если файл `data/sql/schema.sql` был изменён и нужно заново применить начальную схему базы данных, необходимо удалить MySQL-контейнер и сохранённые данные.

Остановить и удалить контейнер:

```bash
docker rm -f taskforce-yii2-mysql
```

Удалить данные MySQL:

```bash
rm -rf docker/mysql/data/*
```

После этого снова запустить проект:

```bash
./start.sh
```

При создании нового MySQL-контейнера файл data/sql/schema.sql будет автоматически выполнен через /docker-entrypoint-initdb.d/.

> Внимание: удаление содержимого docker/mysql/data полностью удаляет локальную базу данных.

---

# Запуск PHP без Docker

При необходимости PHP-приложение можно запустить непосредственно на хостовой машине, оставив MySQL в Docker.

В этом случае PHP не имеет доступа к имени контейнера `taskforce-yii2-mysql`, поэтому в `.env` необходимо использовать опубликованный Docker-порт:

```dotenv
DB_HOST=localhost
DB_PORT=3309
```

Остальные параметры подключения должны соответствовать настройкам MySQL-контейнера:

```dotenv
DB_NAME=taskforce_yii2_db
DB_USER=taskforce_yii2_dbuser
DB_PASSWORD=taskforce_yii2_dbpass
```

Запустите MySQL-контейнер, если он ещё не запущен:

```bash
docker start taskforce-yii2-mysql
```

Установите зависимости:

```bash
composer install
```

Запустите встроенный сервер Yii:

```bash
php yii serve
```

Приложение будет доступно по адресу:

```text
http://localhost:8080
```

При возврате к рекомендуемому запуску через Docker верните настройки подключения в `.env`:

```dotenv
DB_HOST=taskforce-yii2-mysql
DB_PORT=3306
```
