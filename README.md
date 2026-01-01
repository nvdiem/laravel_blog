# Laravel Blog – Technical CMS

A lightweight, production-ready technical blog CMS built with Laravel.
Designed for developers who value clean architecture, writing experience,
and long-term maintainability.

---

## 🚀 Overview

**laravel_blog** is not a demo CRUD project.

It is a real-world technical blog system featuring:
- WordPress-like authoring experience
- Clean, developer-focused public reading experience
- Minimal dependencies
- Explicit architecture boundaries

The project is suitable for:
- Personal technical blogs
- Internal engineering blogs
- CMS foundations for small teams

---

## 🧱 Tech Stack

- Backend: Laravel
- Database: MySQL
- Frontend UI: Bootstrap
- Editor: TinyMCE (self-hosted)
- Code Highlighting: Prism.js

No frontend frameworks (React/Vue).
No heavy CMS packages.

---

## 🧩 System Architecture

The system is divided into two clearly separated areas:

### 1. Admin Area (CMS)
- Content creation and editing
- WordPress-like editor layout
- Metadata management (category, tags, SEO)
- Draft / published workflow

### 2. Public Area (Blog)
- Displays published posts only
- Optimized for long technical articles
- SEO-friendly
- Clean reading experience

Admin and Public areas are strictly separated in:
- Layout
- Controllers
- UI logic
- Responsibilities

---

## ✍️ Admin Features

- Two-column editor layout (8 / 4)
- Large title input
- TinyMCE content editor
- WordPress-like tag input (chip-based)
- Category assignment
- Featured image upload
- SEO title & meta description
- Draft / published workflow

---

## 🌐 Public Features

- Home page with paginated post listing
- Post detail page via slug
- Clean typography and spacing
- Code block highlighting (Prism.js)
- Related posts by category

Only published posts are visible publicly.

---

## 🏷️ Tag System

- Tags stored using a many-to-many relationship
- Admin UI uses chip-style tag input
- Backend receives tags as a comma-separated string
- No external tag libraries
- No frontend frameworks

This system is intentionally simple and explicit.

---

## 📂 Documentation

This project uses **AI-first documentation**.

Important documents:

