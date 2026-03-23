# Sử dụng PHP 8.2 với Apache làm môi trường chạy ứng dụng
FROM php:8.2-apache

# Cài đặt các thư viện cần thiết cho PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Kích hoạt Apache mod_rewrite (hữu ích cho các đường dẫn đẹp sau này)
RUN a2enmod rewrite

# Chỉnh sửa cấu hình Apache để cho phép sử dụng .htaccess (nếu có)
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Copy toàn bộ mã nguồn vào thư mục chứa web của Apache
COPY . /var/www/html/

# Cấp quyền cho thư mục web (đảm bảo PHP có thể đọc file)
RUN chown -R www-data:www-data /var/www/html

# Cổng mặc định của Apache là 80
EXPOSE 80

# Chạy Apache ở chế độ foreground
CMD ["apache2-foreground"]
