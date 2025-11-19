# Deployment Checklist - nhandinhbongda.io.vn

## Pre-Deployment

- [ ] Backup database hiện tại (nếu có)
- [ ] Backup files hiện tại (nếu có)
- [ ] Kiểm tra PHP version (>= 8.1)
- [ ] Kiểm tra Composer đã cài đặt
- [ ] Kiểm tra Git đã cài đặt (nếu cần)

## XAMPP Setup

- [ ] Cài đặt XAMPP
- [ ] Enable mod_rewrite trong httpd.conf
- [ ] Enable mod_ssl trong httpd.conf
- [ ] Enable PHP extensions cần thiết
- [ ] Tạo database `nhandinhbongda` trong phpMyAdmin

## Application Setup

- [ ] Copy project vào `C:\xampp\htdocs\nhandinhbongda`
- [ ] Chạy `composer install --optimize-autoloader --no-dev`
- [ ] Copy `.env.example` thành `.env`
- [ ] Cập nhật `.env` với thông tin production
- [ ] Chạy `php artisan key:generate`
- [ ] Chạy `php artisan migrate --force`
- [ ] Chạy `php artisan db:seed --class=AdminUserSeeder`
- [ ] Chạy `php artisan storage:link`
- [ ] Chạy `php artisan config:cache`
- [ ] Chạy `php artisan route:cache`
- [ ] Chạy `php artisan view:cache`

## Apache Configuration

- [ ] Tạo thư mục `C:\xampp\apache\conf\ssl`
- [ ] Copy nội dung từ `apache-vhost.conf` vào `httpd-vhosts.conf`
- [ ] Update đường dẫn DocumentRoot nếu khác
- [ ] Enable `Include conf/extra/httpd-vhosts.conf` trong httpd.conf
- [ ] Test cấu hình: `httpd.exe -t`

## SSL Configuration

- [ ] Tạo SSL certificate (self-signed hoặc Let's Encrypt)
- [ ] Copy certificate files vào `C:\xampp\apache\conf\ssl\`
- [ ] Update paths trong virtual host nếu cần
- [ ] Kiểm tra SSL certificate paths đúng

## DNS Configuration

- [ ] Thêm A record: `nhandinhbongda.io.vn -> [VPS IP]`
- [ ] Thêm A record: `www.nhandinhbongda.io.vn -> [VPS IP]`
- [ ] Đợi DNS propagate (có thể mất vài phút đến vài giờ)

## Firewall

- [ ] Mở port 80 (HTTP) trong Windows Firewall
- [ ] Mở port 443 (HTTPS) trong Windows Firewall
- [ ] Mở port 3306 (MySQL) nếu cần truy cập từ xa

## Testing

- [ ] Test HTTP: http://nhandinhbongda.io.vn
- [ ] Test HTTPS: https://nhandinhbongda.io.vn
- [ ] Test redirect HTTP -> HTTPS
- [ ] Test các routes chính:
  - [ ] Bảng xếp hạng
  - [ ] Lịch thi đấu
  - [ ] Kết quả
  - [ ] Top ghi bàn
  - [ ] Tin thể thao
  - [ ] Nhận định bóng đá
  - [ ] Admin panel
- [ ] Test upload ảnh trong admin
- [ ] Test TinyMCE editor
- [ ] Kiểm tra storage link hoạt động

## Security

- [ ] Đặt `APP_DEBUG=false` trong `.env`
- [ ] Đổi mật khẩu MySQL root
- [ ] Kiểm tra file permissions (storage, bootstrap/cache)
- [ ] Kiểm tra `.env` không được commit lên Git
- [ ] Cập nhật mật khẩu admin user

## Performance

- [ ] Enable OPcache trong php.ini
- [ ] Cấu hình cache cho production
- [ ] Kiểm tra file upload size limits
- [ ] Cấu hình memory_limit phù hợp

## Monitoring

- [ ] Setup log monitoring
- [ ] Setup error tracking (nếu có)
- [ ] Setup backup schedule
- [ ] Document admin credentials (lưu an toàn)

## Post-Deployment

- [ ] Test tất cả chức năng
- [ ] Kiểm tra logs không có lỗi
- [ ] Kiểm tra performance
- [ ] Thông báo team về deployment

