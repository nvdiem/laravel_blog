# PUBLIC BLUEPRINT – laravel_blog (Tiếng Việt)

## 1. Mục đích khu vực Public

Public dùng để hiển thị bài viết cho người đọc và công cụ tìm kiếm.
Ưu tiên trải nghiệm đọc bài kỹ thuật dài.

---

## 2. Trách nhiệm Public

- Hiển thị bài viết đã publish
- Điều hướng đơn giản
- Hỗ trợ SEO
- Tối ưu đọc

Public KHÔNG:
- Hiển thị bài draft
- Phụ thuộc logic admin

---

## 3. Các trang Public

### Trang chủ
- Danh sách bài viết
- Layout dạng card
- Có phân trang

### Trang chi tiết bài viết
- Truy cập qua slug
- Hiển thị toàn bộ nội dung

---

## 4. Quy tắc hiển thị

- Chỉ hiển thị bài published
- Draft tuyệt đối không xuất hiện
- Query public phải lọc status

---

## 5. Trải nghiệm đọc

Nguyên tắc:
- Nội dung không quá rộng
- Line-height lớn
- Không sidebar
- Ít nhiễu

---

## 6. Hiển thị nội dung

- Nội dung HTML từ TinyMCE
- Highlight code bằng Prism.js

---

## 7. SEO

- Dùng SEO title nếu có
- Dùng meta description nếu có
- URL theo slug

---

## 8. Hướng mở rộng

Có thể:
- Tìm kiếm
- Lọc category / tag
- Bài viết liên quan

KHÔNG:
- Hiển thị bài draft
- Trộn logic admin
