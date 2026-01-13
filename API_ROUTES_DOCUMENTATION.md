# API Routes Documentation

This document describes all API routes for the Language School project, including methods, roles, request parameters, and response structures.

---

## Authentication

| Route       | Method | Role  | Description              | Request Body / Query              | Response                                                                |
| ----------- | ------ | ----- | ------------------------ | --------------------------------- | ----------------------------------------------------------------------- |
| `/login`    | POST   | Guest | Login user               | `{ email, password }`             | `{ user: {id, name, email, role}, token }`                              |
| `/register` | POST   | Guest | Register user            | `{ name, email, password, role }` | `{ user, token }`                                                       |
| `/user`     | GET    | Auth  | Get current user profile | —                                 | `{ id, name, full_name, email, address, role, created_at, updated_at }` |

---

## Students

| Route            | Method | Role | Description                                    | Query / Body   | Response                                                                                                                         |
| ---------------- | ------ | ---- | ---------------------------------------------- | -------------- | -------------------------------------------------------------------------------------------------------------------------------- |
| `/students`      | GET    | Auth | Paginated list of students                     | `?per_page=10` | `[ { id, name, full_name, email, enrolled_classes_count } ]`                                                                     |
| `/students/{id}` | GET    | Auth | Detail of single student with enrolled classes | —              | `{ id, name, full_name, email, enrolled_classes: [{id, title, schedule_time, status, professor:{id, name, full_name, email}}] }` |

---

## Professors

| Route              | Method | Role | Description                                    | Query / Body   | Response                                                                                                        |
| ------------------ | ------ | ---- | ---------------------------------------------- | -------------- | --------------------------------------------------------------------------------------------------------------- |
| `/professors`      | GET    | Auth | Paginated list of professors                   | `?per_page=10` | `[ { id, name, full_name, email, taught_classes_count } ]`                                                      |
| `/professors/{id}` | GET    | Auth | Detail of single professor with taught classes | —              | `{ id, name, full_name, email, taught_classes: [{id, title, schedule_time, status, enrolled_students_count}] }` |

---

## Language Classes

| Route                             | Method | Role             | Description               | Request Body / Query                                                             | Response                                                                                                                                     |
| --------------------------------- | ------ | ---------------- | ------------------------- | -------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------- |
| `/language-classes`               | GET    | Auth             | Paginated list of classes | `?per_page=10`                                                                   | `[ { id, title, schedule_time, status, professor:{id, name, full_name, email}, students_count } ]`                                           |
| `/language-classes/{id}`          | GET    | Auth             | Detail of single class    | —                                                                                | `{ id, title, description, schedule_time, status, professor:{id, name, full_name, email}, students:[{id, name, full_name, email, status}] }` |
| `/language-classes`               | POST   | Admin            | Create new class          | `{ title, description?, professor_id, schedule_time, student_ids? }`             | `{ id, title, description, schedule_time, status, professor:{...}, students:[...] }`                                                         |
| `/language-classes/{id}`          | PUT    | Admin            | Update class              | `{ title?, description?, professor_id?, schedule_time?, student_ids?, status? }` | `{ id, title, description, schedule_time, status, professor:{...}, students:[...] }`                                                         |
| `/language-classes/{id}`          | DELETE | Admin            | Delete class              | —                                                                                | `{ message: 'Language class deleted' }`                                                                                                      |
| `/language-classes/{id}/complete` | POST   | Admin, Professor | Confirm class completion  | —                                                                                | `{ id, title, description, schedule_time, status:'completed', professor:{...}, students:[...] }`                                             |

---

## Language Class Assignments

| Route               | Method | Role             | Description                   | Request Body / Query  | Response                                                                                                                                              |             |                                                       |
| ------------------- | ------ | ---------------- | ----------------------------- | --------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------- | ----------- | ----------------------------------------------------- |
| `/assignments`      | GET    | Admin, Professor | Paginated list of assignments | `?per_page=10`        | `[ { id, student:{id, name, full_name, email}, language_class:{id, title, schedule_time, status, professor:{id, name, full_name, email}}, status } ]` |             |                                                       |
| `/assignments/{id}` | GET    | Admin, Professor | Detail of a single assignment | —                     | `{ id, student:{...}, language_class:{...}, status }`                                                                                                 |             |                                                       |
| `/assignments/{id}` | PUT    | Admin, Professor | Update assignment status      | `{ status: 'assigned' | 'passed'                                                                                                                                              | 'failed' }` | `{ id, student:{...}, language_class:{...}, status }` |

---

## Notes

-   All routes are protected with `auth:sanctum` and `throttle:api`.
-   Role-based access is enforced via `RoleMiddleware`.
-   `student_ids` in class creation/update should be an array of existing student IDs.
-   Date fields use format `Y-m-d H:i:s`.
-   Nested objects include only relevant fields for FE consumption.
-   Pagination supports `?per_page=` query parameter.
