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

Файлы используются независимо:

```text
.env
→ загружается PHP-приложением через phpdotenv

.env.mysql
→ передаётся Docker в MySQL-контейнер через --env-file
```

Откройте `.env` и `.env.mysql` и при необходимости измените значения переменных окружения.

## 3. Установить зависимости

```bash
composer install
```

## 4. Создать MySQL-контейнер

При первом запуске создайте контейнер:

```bash
docker run -d \
  --name taskforce-yii2-mysql \
  -p 3309:3306 \
  --env-file .env.mysql \
  -v "$(pwd)/docker/mysql/data:/var/lib/mysql" \
  mysql:8.4
```

При последующих запусках достаточно запустить существующий контейнер:

```bash
docker start taskforce-yii2-mysql
```

## 5. Запустить приложение

```bash
php yii serve
```

## 6. Открыть проект

Проект доступен в браузере:

```text
http://localhost:8080
```

## Полезные команды

### Docker

Запустить контейнер:

```bash
docker start taskforce-yii2-mysql
```

Остановить контейнер:

```bash
docker stop taskforce-yii2-mysql
```

Перезапустить контейнер:

```bash
docker restart taskforce-yii2-mysql
```

Посмотреть запущенные контейнеры:

```bash
docker ps
```

Посмотреть все контейнеры:

```bash
docker ps -a
```

Посмотреть логи:

```bash
docker logs taskforce-yii2-mysql
```

Удалить остановленный контейнер:

```bash
docker rm taskforce-yii2-mysql
```

Принудительно остановить и удалить контейнер:

```bash
docker rm -f taskforce-yii2-mysql
```

### Composer

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
