# Database Seeders

Dự án này có các seeder sau để tạo dữ liệu mẫu:

## Các Seeder Có Sẵn

### 1. RolePermissionSeeder
Tạo các roles và permissions cho hệ thống:
- **super-admin**: Có tất cả quyền
- **admin**: Có hầu hết quyền trừ quản trị hệ thống
- **organizer**: Có thể quản lý workshop và participants
- **staff**: Có thể quản lý check-in và xem thông tin cơ bản
- **viewer**: Chỉ có quyền xem

### 2. SampleDataSeeder
Tạo dữ liệu mẫu đầy đủ cho demo:
- 6 users với các roles khác nhau
- 5 workshops với các trạng thái khác nhau
- Ticket types cho mỗi workshop
- 15-25 participants cho mỗi workshop
- Email templates cho tất cả workshops

### 3. DevelopmentSeeder
Tạo dữ liệu tối thiểu cho development:
- 2 users cơ bản (admin, organizer)
- 1 workshop test
- 2 ticket types
- 5 participants
- 1 email template

## Cách Sử dụng

### Chạy tất cả seeders (khuyến nghị cho demo):
```bash
php artisan db:seed
```

### Chỉ chạy roles và permissions:
```bash
php artisan db:seed --class=RolePermissionSeeder
```

### Chỉ chạy dữ liệu mẫu:
```bash
php artisan db:seed --class=SampleDataSeeder
```

### Chỉ chạy dữ liệu development:
```bash
php artisan db:seed --class=DevelopmentSeeder
```

### Reset và seed lại database:
```bash
php artisan migrate:fresh --seed
```

## Thông Tin Đăng Nhập

### Sau khi chạy SampleDataSeeder:
- **Super Admin**: superadmin@example.com / password
- **Admin**: admin@example.com / password
- **Organizer**: organizer1@example.com / password
- **Staff**: staff@example.com / password
- **Viewer**: viewer@example.com / password

### Sau khi chạy DevelopmentSeeder:
- **Admin**: admin@test.com / 123456
- **Organizer**: organizer@test.com / 123456

## Lưu Ý

1. Trước khi chạy seeders, đảm bảo đã chạy migrations:
   ```bash
   php artisan migrate
   ```

2. Nếu gặp lỗi về permissions, hãy clear cache:
   ```bash
   php artisan permission:cache-reset
   ```

3. Để tạo dữ liệu mới mỗi lần, sử dụng:
   ```bash
   php artisan migrate:fresh --seed
   ```