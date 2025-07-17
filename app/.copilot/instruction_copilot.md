# 📘 GitHub Copilot Instruction for Internal Workshop Management System

## 🔧 Mục tiêu
Hệ thống được xây dựng bằng Laravel 12, MySQL và Blade HTML để phục vụ việc quản lý hội thảo/khoá học **dành cho ban tổ chức nội bộ**, không công khai. Tập trung vào các nghiệp vụ quản trị như phân quyền, quản lý hội thảo, người tham gia, gửi vé, check-in và thống kê.

---

## ⚙️ Công nghệ sử dụng

- Laravel 12
- MySQL
- Queue: database
- Email: Laravel Mailable
- QR Code: `simple-qrcode`
- Phân quyền: `spatie/laravel-permission`
- Import Excel: `maatwebsite/excel` (nếu có)
- Laravel Job & Event Listener

---

## 📦 Chức năng chính

### 1. Quản lý người dùng & phân quyền
- CRUD người dùng
- Gán nhiều vai trò cho mỗi người dùng (`admin`, `organizer`, ...)
- Lọc người dùng theo role
- Kích hoạt/vô hiệu hóa tài khoản
- Sử dụng `spatie/laravel-permission`
- Dùng middleware `role:` để kiểm soát truy cập
- Giao diện Blade có:
  - Danh sách user
  - Dropdown chọn vai trò
  - Nút toggle trạng thái kích hoạt

---

### 2. Quản lý hội thảo (Workshop)
Mỗi hội thảo chứa nhiều thông tin liên quan:

#### a. Thông tin hội thảo
- Tên, mô tả, thời gian, địa điểm,hình anh, trạng thái
- Quan hệ `belongsToMany` với `organizer` (User)

#### b. Loại vé & mức phí
- Model: `TicketType` có `name`, `price`
- Quan hệ `Workshop hasMany TicketType`

#### c. Người tham gia (Participant)
- Thêm từ Excel hoặc thủ công
- Trường: name, phone, email, occupation, address, position, company
- Quan hệ:
  - `belongsTo Workshop`
  - `belongsTo TicketType`
- Có trạng thái:
  - `is_paid`: đã thanh toán
  - `is_checked_in`: đã check-in
- Có `ticket_code` duy nhất

#### d. Mã QR & gửi vé
- Mã QR sinh từ `ticket_code`
- Gửi bằng `Mailable`
- Thực hiện qua hàng đợi (`Queue`)
- Nhấn gửi toàn bộ hoặc chọn từng vé để gửi


#### e. Check-in
- Scan QR để cập nhật `is_checked_in = true`
- Giao diện hỗ trợ xác nhận check-in bằng mã

#### f. Mẫu email theo hội thảo
- Model: `EmailTemplate`
- Gắn `workshop_id`
- Có `type`, `subject`, `content`
- Sử dụng biến động:
  - `{{ name }}`, `{{ ticket_code }}`, `{{ qr_code_url }}`, `{{ workshop_name }}`

---

## ✍️ Quy ước code

### Tổng thể
- Laravel 12 chuẩn: Eloquent, FormRequest, Route Model Binding, Mailable, Job, Event
- Mỗi Controller chỉ xử lý logic điều hướng
- Tách logic nghiệp vụ ra `Service` hoặc `Action`
- Tên model: `Workshop`, `TicketType`, `Participant`, `EmailTemplate`
- Tên job: `SendTicketJob`, `GenerateQrJob`

### Migration
- Luôn có foreign key, index rõ ràng
- Dùng kiểu dữ liệu phù hợp: `string`, `decimal`, `boolean`, `timestamp`
- Default rõ ràng: `->default(false)`, `->nullable()`

### Blade
- Blade đơn giản, sử dụng `@foreach`, `@can`, `@csrf`, `@error`
- Có thể tạo component nếu cần: `<x-ticket-type-select />`

---

## 💡 Gợi ý Copilot mong muốn
- Trả lời bằng **tiếng Việt**, sử dụng thuật ngữ Laravel chính xác.
- Khi có thể, giải thích đoạn mã bằng tiếng Việt đơn giản và dễ hiểu.