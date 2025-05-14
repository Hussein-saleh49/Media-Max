#!/bin/bash

# توقف الحاويات إذا كانت تعمل
echo "Stopping any running containers..."
docker-compose down

# تغيير صلاحيات المجلدات
echo "Setting up permissions..."
sudo chmod -R 777 storage bootstrap/cache

# تشغيل الحاويات
echo "Starting containers..."
docker-compose up -d

# انتظار حتى يبدأ MySQL
echo "Waiting for MySQL to start..."
sleep 15

# تنفيذ الأوامر داخل حاوية التطبيق
echo "Installing dependencies and setting up Laravel..."
docker-compose exec app composer install --optimize-autoloader --no-dev
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --force

# إعداد Redis
echo "Testing Redis connection..."
docker-compose exec redis redis-cli ping

# عرض رسالة النجاح
echo -e "\n\n✅ Setup completed successfully!"
echo "Your application is now running at: http://localhost:8080"
echo "MySQL is running on port 3306"
echo "Redis is running on port 6379"