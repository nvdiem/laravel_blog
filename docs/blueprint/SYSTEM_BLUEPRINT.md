# SYSTEM_BLUEPRINT.md
Laravel Blog / CMS – System Architecture Blueprint (v2)

---

## 1. Purpose & Scope

This document defines the system-level architecture, non-negotiable rules,
and structural conventions of the `laravel_blog` project.

Its goals are:
- Maintain a clean and predictable architecture
- Clearly separate responsibilities and application areas
- Enable safe collaboration between humans and AI
- Prevent architectural drift during refactors and feature growth

This blueprint applies to all contributors, including AI assistants.

---

## 2. System Overview

`laravel_blog` is a content management system (CMS) focused on:
- A WordPress-like admin writing experience
- A clean, performant frontend blog experience
- Long-term maintainability
- AI-assisted development with strict governance

The system follows Laravel MVC, enhanced with explicit architectural boundaries.

---

## 3. Application Areas (NON-NEGOTIABLE)

The application is divided into two primary areas.

### 3.1 Admin Area

Purpose:
- Content creation and management

Actors:
- Admins
- Editors

Responsibilities:
- Create, edit, delete posts
- Manage categories and tags
- Control publish status (draft / published)
- Edit SEO metadata

Access:
- Authenticated users only

Characteristics:
- Write-oriented
- Form-heavy
- CMS-style UX

---

### 3.2 Frontend Area

Purpose:
- Public content delivery

Actors:
- Visitors
- Readers

Responsibilities:
- Display published posts only
- Public browsing and reading
- SEO-friendly rendering

Access:
- Public

Characteristics:
- Read-only
- Performance and readability focused

---

## 4. Controller Architecture (NON-NEGOTIABLE)

Controllers represent application boundaries and MUST be separated by area.

### 4.1 Admin Controllers

Location:
