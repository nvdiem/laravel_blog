# PUBLIC BLUEPRINT – laravel_blog

## 1. Purpose of Public Area

The Public Area displays published content to readers and search engines.
It is optimized for reading long technical articles.

---

## 2. Public Responsibilities

- Display published posts only
- Provide clean navigation
- Support SEO
- Optimize readability

Public area must NOT:
- Expose draft content
- Depend on admin logic

---

## 3. Public Pages

### Home Page
- Lists published posts
- Card-based layout
- Pagination enabled

### Post Detail Page
- Accessed via slug
- Displays full article content

---

## 4. Visibility Rules

- Only published posts are visible
- Draft posts must never appear
- Public queries must explicitly filter status

---

## 5. Reading Experience

Design principles:
- Max-width content
- Large line-height
- No sidebar
- Minimal distractions

---

## 6. Content Rendering

- Content is rendered as HTML
- Generated from TinyMCE
- Code blocks highlighted via Prism.js

---

## 7. SEO

- Use SEO title if available
- Use meta description if available
- Slug-based URLs

---

## 8. Extension Guidelines

Safe to extend:
- Search
- Category filter
- Tag filter
- Related posts

Never change:
- Draft visibility rules
- Admin/Public separation
