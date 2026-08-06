FROM php:8.3-cli
 
# Install dependency sistem
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*
 
# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
 
WORKDIR /app
 
RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www
USER www
 
EXPOSE 8000
 
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]