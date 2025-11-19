# Hướng Dẫn Deploy Laravel lên VPS Windows với XAMPP và Apache

## Yêu Cầu
- Windows Server với XAMPP đã cài đặt
- Domain: nhandinhbongda.io.vn
- PHP 8.1+ (đã có trong XAMPP)
- Composer
- Git (nếu cần)

## Bước 1: Cài Đặt và Cấu Hình XAMPP

### 1.1. Cài đặt XAMPP
- Tải và cài đặt XAMPP từ https://www.apachefriends.org/
- Đảm bảo Apache và MySQL đã được cài đặt

### 1.2. Cấu hình PHP
- Mở file `C:\xampp\php\php.ini`
- Tìm và bật các extension sau:
```ini
extension=openssl
extension=pdo_mysql
extension=mbstring
extension=tokenizer
extension=xml
extension=fileinfo
extension=gd
```

### 1.3. Cấu hình Apache
- Mở file `C:\xampp\apache\conf\httpd.conf`
- Tìm và uncomment dòng:
```apache
LoadModule rewrite_module modules/mod_rewrite.so
```

## Bước 2: Deploy Laravel Application

### 2.1. Copy Files
- Copy toàn bộ project vào thư mục: `C:\xampp\htdocs\nhandinhbongda`
- Hoặc tạo symbolic link nếu muốn giữ project ở nơi khác

### 2.2. Cài Đặt Dependencies
Mở Command Prompt (Run as Administrator) và chạy:
```bash
cd C:\xampp\htdocs\nhandinhbongda
composer install --optimize-autoloader --no-dev
```

### 2.3. Cấu Hình Environment
- Copy file `.env.example` thành `.env` (nếu chưa có)
- Cập nhật các thông tin trong `.env`:
```env
APP_NAME="XOILAC TV"
APP_ENV=production
APP_KEY=base64:... (chạy php artisan key:generate nếu chưa có)
APP_DEBUG=false
APP_URL=https://nhandinhbongda.io.vn
APP_TIMEZONE=Asia/Ho_Chi_Minh

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nhandinhbongda
DB_USERNAME=root
DB_PASSWORD=

SOCCERSAPI_USER=Zr1NN
SOCCERSAPI_TOKEN=DqCDvCP0ye
```

### 2.4. Tạo Database
- Mở phpMyAdmin: http://localhost/phpmyadmin
- Tạo database mới: `nhandinhbongda`
- Chạy migrations:
```bash
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder
```

### 2.5. Tạo Storage Link
```bash
php artisan storage:link
```

### 2.6. Optimize Application
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Bước 3: Cấu Hình Apache Virtual Host

### 3.1. Tạo Virtual Host File
Tạo file `C:\xampp\apache\conf\extra\httpd-vhosts.conf` (hoặc mở file có sẵn) và thêm:

```apache
<VirtualHost *:80>
    ServerName nhandinhbongda.io.vn
    ServerAlias www.nhandinhbongda.io.vn
    DocumentRoot "C:/xampp/htdocs/nhandinhbongda/public"
    
    <Directory "C:/xampp/htdocs/nhandinhbongda/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "C:/xampp/apache/logs/nhandinhbongda_error.log"
    CustomLog "C:/xampp/apache/logs/nhandinhbongda_access.log" common
</VirtualHost>

<VirtualHost *:443>
    ServerName nhandinhbongda.io.vn
    ServerAlias www.nhandinhbongda.io.vn
    DocumentRoot "C:/xampp/htdocs/nhandinhbongda/public"
    
    SSLEngine on
    SSLCertificateFile "C:/xampp/apache/conf/ssl/nhandinhbongda.io.vn.crt"
    SSLCertificateKeyFile "C:/xampp/apache/conf/ssl/nhandinhbongda.io.vn.key"
    SSLCertificateChainFile "C:/xampp/apache/conf/ssl/nhandinhbongda.io.vn.chain.crt"
    
    <Directory "C:/xampp/htdocs/nhandinhbongda/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "C:/xampp/apache/logs/nhandinhbongda_ssl_error.log"
    CustomLog "C:/xampp/apache/logs/nhandinhbongda_ssl_access.log" common
</VirtualHost>
```

### 3.2. Enable Virtual Hosts
Mở `C:\xampp\apache\conf\httpd.conf` và uncomment:
```apache
Include conf/extra/httpd-vhosts.conf
```

## Bước 4: Cấu Hình SSL

### 4.1. Tạo Thư Mục SSL
```bash
mkdir C:\xampp\apache\conf\ssl
```

### 4.2. Tạo SSL Certificate (Let's Encrypt hoặc tự ký)

#### Option A: Sử dụng Let's Encrypt (Khuyến nghị)
1. Cài đặt Certbot for Windows hoặc sử dụng Win-ACME
2. Chạy certbot để tạo certificate:
```bash
certbot certonly --standalone -d nhandinhbongda.io.vn -d www.nhandinhbongda.io.vn
```
3. Copy certificate files vào `C:\xampp\apache\conf\ssl\`

#### Option B: Tạo Self-Signed Certificate (cho testing)
```bash
# Tạo private key
openssl genrsa -out C:\xampp\apache\conf\ssl\nhandinhbongda.io.vn.key 2048

# Tạo certificate signing request
openssl req -new -key C:\xampp\apache\conf\ssl\nhandinhbongda.io.vn.key -out C:\xampp\apache\conf\ssl\nhandinhbongda.io.vn.csr

# Tạo self-signed certificate
openssl x509 -req -days 365 -in C:\xampp\apache\conf\ssl\nhandinhbongda.io.vn.csr -signkey C:\xampp\apache\conf\ssl\nhandinhbongda.io.vn.key -out C:\xampp\apache\conf\ssl\nhandinhbongda.io.vn.crt
```

### 4.3. Enable SSL Module
Mở `C:\xampp\apache\conf\httpd.conf` và uncomment:
```apache
LoadModule ssl_module modules/mod_ssl.so
Include conf/extra/httpd-ssl.conf
```

## Bước 5: Cấu Hình DNS

### 5.1. Cấu Hình DNS Records
Thêm các records sau vào DNS của domain:
```
A Record: nhandinhbongda.io.vn -> [IP của VPS]
A Record: www.nhandinhbongda.io.vn -> [IP của VPS]
```

## Bước 6: Cấu Hình Firewall

### 6.1. Mở Ports
- Mở Windows Firewall
- Cho phép port 80 (HTTP) và 443 (HTTPS)
- Cho phép port 3306 (MySQL) nếu cần truy cập từ xa

## Bước 7: Kiểm Tra và Khởi Động

### 7.1. Kiểm Tra Cấu Hình Apache
```bash
C:\xampp\apache\bin\httpd.exe -t
```

### 7.2. Khởi Động Apache
- Mở XAMPP Control Panel
- Start Apache và MySQL
- Hoặc chạy: `net start Apache2.4`

### 7.3. Kiểm Tra
- Truy cập: http://nhandinhbongda.io.vn
- Truy cập: https://nhandinhbongda.io.vn

## Bước 8: Cấu Hình Redirect HTTP -> HTTPS

Thêm vào file `.htaccess` trong thư mục `public`:
```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

## Lưu Ý Quan Trọng

1. **Permissions**: Đảm bảo thư mục `storage` và `bootstrap/cache` có quyền ghi
2. **Scheduled Tasks**: Nếu cần chạy cron jobs, tạo Windows Scheduled Task
3. **Backup**: Thiết lập backup định kỳ cho database và files
4. **Security**: 
   - Đặt `APP_DEBUG=false` trong production
   - Đổi mật khẩu MySQL root
   - Cập nhật `.env` với thông tin bảo mật

## Troubleshooting

- **403 Forbidden**: Kiểm tra quyền thư mục và cấu hình Directory trong Apache
- **500 Error**: Kiểm tra `storage/logs/laravel.log` để xem lỗi chi tiết
- **SSL không hoạt động**: Kiểm tra certificate paths và SSL module đã được enable

