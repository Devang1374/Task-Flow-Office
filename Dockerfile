FROM php:8.2-cli

WORKDIR /app

COPY . .

# Install system packages
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libzip-dev \
    default-mysql-client \
    nodejs \
    npm \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql zip gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN php artisan storage:link || true
RUN php artisan migrate:fresh

# Install PHP packages
RUN composer install --no-dev --optimize-autoloader

# Install frontend packages
RUN npm install

# Build frontend
RUN npm run build

EXPOSE 10000

CMD php artisan serve --host=0.0.0.0 --port=$PORT