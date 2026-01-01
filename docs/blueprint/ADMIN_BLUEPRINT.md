# ADMIN BLUEPRINT – laravel_blog

## 1. Purpose of Admin Area

The Admin Area acts as a lightweight CMS focused on:
- Writing technical articles
- Managing post lifecycle
- Managing metadata (category, tags, SEO)

The Admin Area prioritizes author productivity over visual complexity.

---

## 2. Target Users

- Developers
- Content editors
- Technical writers

Users are assumed to be authenticated and familiar with CMS workflows.

---

## 3. Admin Responsibilities

The Admin Area is responsible for:
- Creating posts
- Editing posts
- Managing post status (draft / published)
- Assigning categories
- Managing tags
- Uploading featured images
- Managing SEO metadata

The Admin Area must NOT:
- Render public-facing content
- Handle public navigation logic

---

## 4. Editor Layout (WordPress-like)

The editor uses a two-column layout:

- Left column (8/12):
  - Title input (large, prominent)
  - Slug preview (read-only)
  - Content editor (TinyMCE)

- Right column (4/12):
  - Publish box
  - Category selector
  - Tag management
  - Featured image
  - SEO metadata

This layout is non-negotiable.

---

## 5. Content Editor

Editor requirements:
- TinyMCE self-hosted
- Supports:
  - Headings
  - Lists
  - Links
  - Tables
  - Code blocks
- Generates clean HTML

Editor must NOT be replaced without architectural review.

---

## 6. Tag Management System

- Tag input behaves like WordPress
- Tags are entered via Enter or comma
- Tags are displayed as removable chips
- Backend receives tags as a comma-separated string

Constraints:
- No external JS tag libraries
- No JS frameworks
- UI logic must not bypass backend contract

---

## 7. Form Architecture (CRITICAL)

- Admin editor uses a SINGLE HTML form
- All inputs must belong to the same form
- Hidden inputs must be submitted correctly

Form splitting is strictly forbidden.

---

## 8. Post Status Workflow

Post statuses:
- draft
- published

Rules:
- Draft posts are visible ONLY in admin
- Published posts are visible publicly

Status handling must remain explicit and simple.

---

## 9. Admin UX Principles

- Minimal distractions
- Clear hierarchy
- Fast writing workflow
- No unnecessary animations

The admin UI favors clarity over aesthetics.

---

## 10. Constraints (NON-NEGOTIABLE)

- Do NOT merge admin and public logic
- Do NOT introduce frontend frameworks
- Do NOT replace TinyMCE
- Bootstrap is the only UI framework

---

## 11. Extension Guidelines

Safe to extend:
- Admin UX improvements
- Editor enhancements
- Validation feedback

Be careful when modifying:
- Tag system
- Form structure
- Publish workflow
