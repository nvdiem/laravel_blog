# SYSTEM BLUEPRINT – laravel_blog

## 1. PROJECT OVERVIEW

Project Name: laravel_blog  
Type: Technical Blog CMS  
Primary Stack: Laravel + MySQL + Bootstrap  
Audience: Developers / Tech teams  
Purpose:  
Build a lightweight, real-world technical blog system with a WordPress-like
authoring experience and a clean, developer-focused public reading experience.

This project is NOT a demo CRUD application.
It is designed as a production-ready foundation for a technical blog or internal CMS.

---

## 2. CORE PRINCIPLES

The system is built around the following non-negotiable principles:

1. Clear separation between Admin and Public areas
2. Authoring experience prioritized (WordPress-like UX)
3. Reading experience prioritized for long technical articles
4. Minimal dependencies (no heavy CMS packages)
5. Explicit constraints to guide AI agents and developers

Any future change must respect these principles.

---

## 3. HIGH-LEVEL ARCHITECTURE

The system is divided into two independent domains:

### 3.1 Admin Area (CMS)
Purpose:
- Content creation
- Content management
- SEO metadata management

Audience:
- Authenticated users (admins/editors)

Characteristics:
- WordPress-like editor layout
- Focus on productivity and clarity
- UI optimized for writing long technical content

### 3.2 Public Area (Blog)
Purpose:
- Display published content
- SEO-friendly
- Optimized reading experience

Audience:
- Public users
- Search engines

Characteristics:
- Clean typography
- Max-width content
- Code-friendly reading (syntax highlighting)

The Admin Area and Public Area MUST remain separated in:
- Layout
- Controllers
- UI logic
- Responsibilities

---

## 4. ADMIN AREA BLUEPRINT (CMS)

### 4.1 Responsibilities
- Create new posts
- Edit existing posts
- Manage post status (draft / published)
- Assign category, tags, thumbnail
- Manage SEO metadata

### 4.2 Layout Philosophy
- Dark header, light content
- Two-column editor layout (8 / 4 grid)
- Editor is the primary focus
- Sidebar contains metadata (similar to WordPress)

### 4.3 Editor Architecture
- Left column (8):
  - Title input (large, prominent)
  - Slug preview (read-only)
  - Content editor (TinyMCE self-hosted)

- Right column (4):
  - Publish box (status + action)
  - Category selector
  - Tag input (WordPress-like behavior)
  - Featured image upload
  - SEO fields (title + description)

---

## 5. TAG SYSTEM DESIGN (CRITICAL)

### 5.1 Storage Model
- Tags stored in `tags` table
- Many-to-many relationship via `post_tag` pivot table

### 5.2 Backend Interface
- Backend receives tags as a single comma-separated string
  Example:
  "laravel,php,clean-architecture"

- Backend logic must remain simple and unchanged

### 5.3 Frontend Behavior
- Tag input uses chip-style UI (WordPress-like)
- User adds tag via:
  - Enter key
  - Comma (,)
- Each tag displayed as a removable chip
- Removing a chip removes the tag from submission
- A hidden input stores the canonical comma-separated value

### 5.4 Constraints
- No external JS libraries for tag input
- No JS frameworks
- Do not normalize case automatically (preserve user input)
- Tag UI must not bypass backend logic

This tag system is sensitive.
Any refactor must preserve this contract.

---

## 6. PUBLIC AREA BLUEPRINT (BLOG)

### 6.1 Responsibilities
- Display published posts only
- Provide clean navigation
- Optimize reading experience
- Support SEO and discoverability

### 6.2 Public Pages
- Home page:
  - Lists published posts
  - Card-based layout
  - Pagination enabled

- Post detail page:
  - Accessible via slug (/posts/{slug})
  - Displays full article content

### 6.3 Visibility Rules
- ONLY posts with status = published are visible publicly
- Draft posts must never appear in public queries
- Admin logic must not leak into public controllers

---

## 7. POST DETAIL PAGE (READING EXPERIENCE)

The post detail page is the most important public page.

### Design Principles:
- Max-width content (~760px)
- Large line-height for long reading sessions
- No sidebar
- No distractions

### Features:
- SEO title & meta description
- Category and tag display
- Syntax highlighting for code blocks
- Related articles section (same category)

---

## 8. CODE CONTENT HANDLING

### 8.1 Content Source
- Post content is stored as HTML
- Generated via TinyMCE editor
- Rendered directly in public views

### 8.2 Syntax Highlighting
- Code blocks follow Prism.js format:
  <pre><code class="language-php">...</code></pre>

- Prism.js is loaded only on post detail pages
- No global JS loading for code highlight

---

## 9. DATA FLOW OVERVIEW

Admin creates or edits a post
→ Post saved as draft or published
→ Public controllers query only published posts
→ Post content rendered as HTML
→ Code blocks enhanced by Prism.js
→ Related articles fetched by category

---

## 10. TECHNICAL CONSTRAINTS (NON-NEGOTIABLE)

The following constraints MUST be respected by all contributors and AI agents:

- Do NOT introduce heavy CMS packages
- Do NOT replace TinyMCE
- Do NOT introduce JS frameworks (React, Vue, etc.)
- Bootstrap is the only UI framework
- Do NOT merge admin and public logic
- Prefer clarity over abstraction
- Avoid premature optimization

---

## 11. EXTENSION GUIDELINES

Safe to extend:
- Admin UI/UX improvements
- Reading experience enhancements
- SEO improvements
- Public navigation features

Be careful when modifying:
- Tag system
- Editor form structure
- Post publishing workflow

Never change without redesign:
- Admin/Public separation
- Core content lifecycle (draft → published)
- Tag backend contract

---

## 12. AI AGENT INSTRUCTIONS

Any AI agent working on this project must:

1. Read this blueprint before suggesting changes
2. Respect all constraints listed above
3. Avoid refactors that violate system boundaries
4. Prefer incremental, explicit changes
5. Ask for clarification before breaking constraints

This document is the single source of truth for system behavior.
