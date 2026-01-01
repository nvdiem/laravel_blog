# TAG SYSTEM BLUEPRINT – laravel_blog

## 1. Purpose

The Tag System enables flexible content classification and discovery.
It is a critical subsystem shared between Admin and Public areas.

Any change to this system may affect:
- Content editing UX
- Data integrity
- Public filtering and SEO

---

## 2. Data Model

Tables:
- tags
- post_tag (pivot)

Relationship:
- Post ⟷ Tag (Many-to-Many)

Tags are unique by name.

---

## 3. Backend Contract (NON-NEGOTIABLE)

Input Format:
- Backend receives tags as a single string
- Comma-separated values

Example:
"laravel,php,clean architecture"

Backend Responsibilities:
- Parse string
- Normalize if needed
- Sync pivot table

Frontend MUST NOT bypass this contract.

---

## 4. Admin UI Behavior

Tag input behaves like WordPress:

- User types a tag
- Presses Enter or comma (,)
- Tag becomes a visual chip
- Each chip can be removed
- Removing a chip removes it from submission

Implementation:
- Plain JavaScript
- Hidden input stores canonical value
- Hidden input value format:
  tag1,tag2,tag3

---

## 5. Constraints

NON-NEGOTIABLE:
- No external tag libraries
- No JS frameworks
- No async tag syncing
- No auto case normalization on frontend
- Single form architecture only

---

## 6. Public Usage

Tags are used for:
- Displaying tag badges
- Optional filtering (future)

Public area MUST:
- Read tags from relationships
- Never manipulate tag data

---

## 7. Sensitive Areas

Be extremely careful when modifying:
- Tag parsing logic
- Form architecture
- Hidden input synchronization

Tag system bugs may result in:
- Ghost tags
- Data mismatch
- SEO issues

---

## 8. AI Agent Instructions

Before modifying the tag system:
1. Read this document
2. Review ADMIN_BLUEPRINT.md
3. Confirm backend contract is preserved

If unsure → DO NOT MODIFY.
