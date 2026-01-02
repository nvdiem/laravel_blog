# ADMIN_BLUEPRINT.md
Laravel Blog / CMS – Admin Area Blueprint

---

## 1. Purpose
Defines responsibilities, rules, and UX principles of the Admin Area.
Admin Area is a CMS for content management, not public-facing.

---

## 2. Core Principles (NON-NEGOTIABLE)
- Writing & managing content only
- State-mutating
- Authenticated access
- No shared views with Frontend
- Productivity > visual polish

---

## 3. Actors
- Admin
- Editor

---

## 4. Controller Rules
Location:
app/Http/Controllers/Admin/

Rules:
- Admin-only use cases
- Require authentication
- Delegate business logic to services
- Must NOT return frontend views

Example:
Admin\PostController
- index
- create
- store
- edit
- update
- destroy

---

## 5. View Rules
Location:
resources/views/admin/

Rules:
- Used ONLY by Admin controllers
- Use admin layout
- Support complex CMS forms

Example:
resources/views/admin/posts/
- index.blade.php
- create.blade.php
- edit.blade.php

---

## 6. Layout & UX
- Two-column layout (content + sidebar)
- Sidebar: publish, category, tags, thumbnail, SEO
- WordPress-like familiarity

---

## 7. Editor
- TinyMCE (self-hosted)
- No SaaS dependency
- Technical writing optimized

---

## 8. Form Architecture
- Single-form only
- Sidebar fields belong to the same form
- No split forms

---

## 9. Data Rules
- Access draft & published posts
- Create, update, delete allowed

---

## 10. Forbidden
- Sharing admin views with frontend
- Business logic in views
- Frontend frameworks

---

## 11. AI Rules
- Preserve form structure
- Do not refactor editor unless instructed
- Follow this blueprint strictly

---

End of ADMIN_BLUEPRINT.md
