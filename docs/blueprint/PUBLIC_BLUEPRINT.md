# PUBLIC_BLUEPRINT.md
Laravel Blog – Frontend Area Blueprint

---

## 1. Purpose
Defines rules and UX principles of the public-facing Frontend.
Frontend is read-only and visitor-focused.

---

## 2. Core Principles (NON-NEGOTIABLE)
- Read-only
- Published content only
- No state mutation
- No shared views with Admin
- Readability & performance first

---

## 3. Actors
- Visitors
- Readers

---

## 4. Controller Rules
Location:
app/Http/Controllers/Frontend/

Rules:
- Read-only
- Published data only
- No admin views

Example:
Frontend\PostController
- index
- show

---

## 5. View Rules
Location:
resources/views/frontend/

Rules:
- Used ONLY by Frontend controllers
- Use frontend layout
- Optimized for reading

Example:
resources/views/frontend/posts/
- index.blade.php
- show.blade.php

---

## 6. UX Guidelines
- Single-column content focus
- Clear typography
- Minimal distractions

---

## 7. Content Rules
- Only status = published
- Slug-based URLs
- Drafts never accessible

---

## 8. SEO
- Meta title & description
- Semantic HTML
- Human-readable URLs

---

## 9. Forbidden
- Admin layout usage
- CMS interactions
- Frontend frameworks

---

## 10. AI Rules
- Preserve readability
- Respect read-only constraints
- No admin logic

---

End of PUBLIC_BLUEPRINT.md
