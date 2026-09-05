FROM php:8.4-cli

# Install system dependencies, Node.js (for Tailwind), and PostgreSQL drivers (for Supabase)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions required by Laravel and Supabase
RUN docker-php-ext-install pdo pdo_pgsql mbstring pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory
WORKDIR /app

# Copy all project files
COPY . .

# Install PHP and Node dependencies, then build Tailwind
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm install
RUN npm run build

# Start the Laravel server
CMD php artisan serve --host=0.0.0.0 --port=$PORT