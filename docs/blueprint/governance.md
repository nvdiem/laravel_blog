# Governance Model (Phase 2)

This CMS follows a strict governance model to ensure security, scalability,
and enterprise-grade access control.

---

## 1. Roles

Roles define **who a user is** in the system.

### Available Roles
- super_admin
- editor
- author

### Rules
- Each user has **exactly one role**
- Roles are **defined in code & database seeders**
- Roles CANNOT be created, edited, or deleted via UI

---

## 2. Permissions

Permissions define **what a user can do**.

### Characteristics
- Capability-based (e.g. post.publish, user.manage)
- Assigned to roles, not directly to users
- Enforced via Laravel Policies

### Rules
- Permissions are **code-defined**
- No permission editing UI exists
- All authorization must go through `canDo()`

---

## 3. Authorization Flow

Authorization is enforced in the following order:

Request  
→ Controller (`authorize()`)  
→ Policy  
→ User::canDo(permission)  
→ Role → Permission mapping  

### Important
- No role hardcoding is allowed in controllers or policies
- `isAdmin()` exists for backward compatibility only

---

## 4. Super Admin

Super Admin is identified by role slug `super_admin`.

### Rules
- There must always be **at least one super admin**
- Super admin can:
  - Assign roles to users
  - Perform all CMS operations
- Super admin CANNOT be removed if it is the last one

---

## 5. User Management (Phase 2.3)

### Scope
- List users
- Assign role (one role per user)

### Restrictions
- Only super admin can access `/admin/users`
- No role CRUD
- No permission management
- Users cannot change their own role

---

## 6. Design Principles

- Security > Convenience
- Backend enforcement > UI hiding
- Capability-based access > role checks
- Minimal UI for governance

---

## 7. Phase Status

- Phase 2.2: Policies & Permissions ✅
- Phase 2.3: User Role Assignment UI ✅
- Phase 2.4: Audit Log ⏸ (Deferred)
- Phase 3: Editorial Workflow ⏸ (Deferred)
