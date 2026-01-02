# TAG_SYSTEM.md
Tag System Blueprint – laravel_blog

---

## 1. Purpose
Defines the tag system contract.
Simple, stable, WordPress-inspired.

---

## 2. Core Principles (NON-NEGOTIABLE)
- Plain text tags
- Shared across posts
- Managed during post create/update
- No separate tag management UI

---

## 3. Data Model
- Tag: id, name (unique)
- Relationship: Post ↔ Tag (many-to-many)

---

## 4. Input Contract (STRICT)
Format:
tag1,tag2,tag3

- Comma-separated
- Case-insensitive
- Trimmed
- Empty ignored

---

## 5. Backend Rules
- Normalize tags
- Create if missing
- Sync replaces existing tags
- Remove deleted tags on update

---

## 6. Service Layer
- All tag logic in:
app/Services/TagService.php
- Controllers must NOT handle tag logic

---

## 7. Frontend Behavior
- Display-only
- Optional future filtering

---

## 8. Admin UI Rules
- Chip-style input
- Single field
- No external JS libraries
- Removal fully replaces tag set

---

## 9. Forbidden
- JSON tags in posts
- Duplicate tag names
- Append-only updates
- Tag logic in controllers/views

---

## 10. AI Rules
- Preserve input contract
- Preserve replacement behavior
- No new formats or async behavior

---

End of TAG_SYSTEM.md
