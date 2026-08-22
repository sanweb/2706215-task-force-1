# Use PHP 8.5 CLI as the base image
FROM php:8.5-cli-alpine

LABEL Name="taskforce-yii2-php" \
      Version="1.0"

# Install the PDO MySQL extension required by Yii2
RUN docker-php-ext-install pdo_mysql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set the application working directory
WORKDIR /app

# Document the port used by the Yii2 development server
EXPOSE 8080

# Start the Yii2 development server
CMD ["php", "yii", "serve", "0.0.0.0:8080"]
