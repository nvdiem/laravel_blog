# AI RULES – laravel_blog

## 1. Purpose

This file defines strict rules for any AI agent
interacting with the laravel_blog codebase.

These rules override any AI default behavior.

---

## 2. Mandatory Reading Order

Before making changes, AI agents MUST read:
1. docs/blueprint/SYSTEM_BLUEPRINT.md
2. docs/blueprint/ADMIN_BLUEPRINT.md
3. docs/blueprint/PUBLIC_BLUEPRINT.md
4. docs/blueprint/TAG_SYSTEM.md

---

## 3. Absolute Constraints (NON-NEGOTIABLE)

AI agents MUST NOT:
- Merge Admin and Public logic
- Replace TinyMCE
- Introduce JS frameworks
- Introduce CMS packages
- Refactor tag backend contract
- Split admin forms into multiple forms

---

## 4. Allowed Actions

AI agents MAY:
- Improve UI clarity
- Refactor code for readability
- Suggest UX improvements
- Optimize queries (with caution)

---

## 5. Change Policy

For any change:
- Small, incremental changes preferred
- Explicit explanation required
- No silent refactors

If a change risks breaking constraints:
→ STOP and ASK.

---

## 6. Output Expectations

AI outputs must:
- Be explicit
- Be scoped
- Respect existing architecture
- Avoid speculative changes

---

## 7. Violation Handling

If an AI agent violates these rules:
- The change must be reverted
- The agent must re-read blueprints
- A corrected approach must be proposed

This file exists to protect system integrity.
