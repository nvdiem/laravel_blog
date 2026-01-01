# ADMIN BLUEPRINT – laravel_blog (Tiếng Việt)

## 1. Mục đích khu vực Admin

Admin đóng vai trò là CMS nhẹ, tập trung vào:
- Viết bài kỹ thuật
- Quản lý vòng đời bài viết
- Quản lý metadata (category, tag, SEO)

Ưu tiên trải nghiệm viết bài hơn giao diện màu mè.

---

## 2. Đối tượng sử dụng

- Developer
- Người viết nội dung kỹ thuật
- Editor nội bộ

Người dùng đã đăng nhập và quen với CMS.

---

## 3. Trách nhiệm Admin

Admin chịu trách nhiệm:
- Tạo / sửa bài viết
- Quản lý trạng thái bài viết
- Gán category, tag
- Upload ảnh đại diện
- Quản lý SEO

Admin KHÔNG:
- Hiển thị nội dung public
- Xử lý logic public

---

## 4. Bố cục Editor (Giống WordPress)

- Cột trái (8):
  - Title lớn
  - Slug preview
  - Nội dung bài viết (TinyMCE)

- Cột phải (4):
  - Publish
  - Category
  - Tag
  - Thumbnail
  - SEO

Bố cục này KHÔNG ĐƯỢC thay đổi tùy tiện.

---

## 5. Trình soạn thảo nội dung

- Dùng TinyMCE self-hosted
- Hỗ trợ heading, list, table, code
- Sinh HTML sạch

KHÔNG thay editor nếu chưa thiết kế lại toàn bộ.

---

## 6. Quản lý Tag

- Nhập tag bằng Enter hoặc dấu phẩy
- Tag hiển thị dạng chip
- Có thể xóa từng tag
- Backend nhận chuỗi: tag1,tag2

KHÔNG dùng thư viện tag ngoài.

---

## 7. Kiến trúc Form (RẤT QUAN TRỌNG)

- Chỉ dùng MỘT form duy nhất
- Mọi input phải nằm trong form
- Không tách form ẩn

Sai kiến trúc form sẽ gây lỗi dữ liệu.

---

## 8. Trạng thái bài viết

- draft: chỉ admin thấy
- published: public thấy

Luồng này KHÔNG được thay đổi.

---

## 9. Nguyên tắc UX Admin

- Tập trung viết bài
- Ít nhiễu
- Rõ ràng, nhanh

---

## 10. Ràng buộc kỹ thuật

KHÔNG ĐƯỢC:
- Gộp admin và public
- Dùng React/Vue
- Thay TinyMCE

---

## 11. Hướng mở rộng

Có thể:
- Cải thiện UX
- Thêm hỗ trợ viết bài

Cẩn trọng:
- Tag system
- Form submit
