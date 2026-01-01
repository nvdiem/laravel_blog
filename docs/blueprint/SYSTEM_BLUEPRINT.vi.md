# SYSTEM BLUEPRINT – laravel_blog (Tiếng Việt)

## 1. Tổng quan dự án

Tên dự án: laravel_blog  
Loại: Hệ thống blog công nghệ / CMS nhẹ  
Công nghệ chính: Laravel, MySQL, Bootstrap  
Đối tượng sử dụng:
- Developer
- Team kỹ thuật
- Blog cá nhân / blog nội bộ

Mục tiêu:
Xây dựng một hệ thống blog công nghệ có:
- Trải nghiệm viết bài giống WordPress
- Trải nghiệm đọc bài tối ưu cho nội dung kỹ thuật
- Không phụ thuộc package CMS nặng
- Dễ mở rộng, dễ bảo trì

Dự án này KHÔNG phải CRUD demo.

---

## 2. Nguyên tắc cốt lõi (NON-NEGOTIABLE)

Các nguyên tắc sau **KHÔNG ĐƯỢC VI PHẠM**:

- Tách biệt hoàn toàn Admin và Public
- Ưu tiên trải nghiệm viết bài (editor)
- Ưu tiên trải nghiệm đọc bài dài
- Phụ thuộc thư viện ở mức tối thiểu
- Có ranh giới rõ ràng cho AI và dev

---

## 3. Kiến trúc tổng thể

Hệ thống chia làm 2 khu vực độc lập:

### 3.1 Khu vực Admin (CMS)
Mục đích:
- Tạo bài viết
- Chỉnh sửa bài viết
- Quản lý trạng thái (draft / published)
- Quản lý SEO, tag, category

Đặc điểm:
- Giao diện giống WordPress
- Editor là trung tâm
- Sidebar quản lý metadata

---

### 3.2 Khu vực Public (Blog)
Mục đích:
- Hiển thị bài viết đã publish
- Phục vụ người đọc và SEO

Đặc điểm:
- Giao diện gọn, dễ đọc
- Không hiển thị bài draft
- Tối ưu nội dung kỹ thuật

---

## 4. Editor Admin (WordPress-like)

Bố cục:
- Cột trái (8):
  - Title
  - Slug preview
  - Nội dung bài viết (TinyMCE)

- Cột phải (4):
  - Publish / Draft
  - Category
  - Tag
  - Thumbnail
  - SEO

TinyMCE:
- Self-hosted
- Không thay thế bằng editor khác

---

## 5. Hệ thống Tag (RẤT QUAN TRỌNG)

Backend:
- Tag lưu trong bảng tags
- Quan hệ nhiều-nhiều với posts

Frontend:
- Nhập tag dạng chip (giống WordPress)
- Enter hoặc dấu phẩy để tạo tag
- Có thể xóa từng tag
- Dữ liệu gửi lên backend là chuỗi:
  tag1,tag2,tag3

KHÔNG:
- Dùng thư viện tag ngoài
- Thay đổi contract backend

---

## 6. Public Blog

Trang public chỉ hiển thị:
- Bài viết có status = published

Trang chính:
- Danh sách bài viết
- Phân trang

Trang chi tiết:
- Hiển thị nội dung HTML
- Highlight code (Prism.js)
- Bài viết liên quan

---

## 7. Trải nghiệm đọc bài

Nguyên tắc:
- Nội dung có độ rộng tối đa
- Line-height lớn
- Không sidebar
- Không làm người đọc mệt

---

## 8. Ràng buộc kỹ thuật

NON-NEGOTIABLE:
- KHÔNG thêm CMS package nặng
- KHÔNG thay TinyMCE
- KHÔNG dùng React/Vue
- Chỉ dùng Bootstrap
- KHÔNG trộn logic Admin và Public

---

## 9. Hướng mở rộng

Có thể mở rộng:
- UI admin
- SEO public
- Trải nghiệm đọc

Cẩn trọng khi sửa:
- Tag system
- Editor form
- Publish flow

Tài liệu này dùng để:
- Onboard dev
- Giải thích hệ thống
- Kiểm soát refactor
