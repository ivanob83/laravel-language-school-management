# Laravel REST API – Stateless School Management

## Overview

This is a **Laravel REST API project** built with a **stateless authentication system**. The project is designed for **school/class management** and is fully **access-controlled**, meaning all endpoints require authentication. The API handles users, roles, classes, and attendance confirmation in a structured and secure way.

## Roles

The system defines three main roles:

### Admin

-   Full access to all resources.
-   Can **create classes**, **assign students to classes**, and **set class schedules**.
-   Manages users (view, create, update roles).

### Professor

-   Can view their assigned classes.
-   Confirms when a class has been **conducted**.

### Student

-   Can view classes they are enrolled in.
-   Receives notifications for scheduled classes (optional future feature).

## Entities & Relationships

### User

-   **Attributes:** `id`, `name`, `email`, `password`, `role` (`admin | professor | student`)
-   **Relationships:**
    -   `hasMany(ClassAssignment)` (for professors: LanguageClasses they teach)
    -   `belongsToMany(LanguageClass)` (for students: classes they attend)

### LanguageClass

-   **Attributes:** `id`, `title`, `description`, `schedule_time`, `status` (`scheduled | completed`)
-   **Relationships:**
    -   `belongsToMany(User)` (students)
    -   `belongsTo(User)` (professor)

### LanguageClassAssignment (pivot table for students)

-   **Attributes:** `class_id`, `student_id`, `status` (`assigned | completed`)

## Workflow

### Admin Flow

1. Logs in.
2. Creates a new LanguageClass with a title, description, and schedule.
3. Assigns students to the class.
4. Assigns a professor to the class.

### Professor Flow

1. Logs in.
2. Views classes assigned to them.
3. Marks a class as **conducted** once finished.

### Student Flow

1. Logs in.
2. Views their upcoming or completed classes.

## API Endpoints (Example)

### Auth

-   `POST /login` – login
-   `POST /register` – register user (admin only)
-   `POST /logout` – logout

### LanguageClasses

-   `GET /classes` – list all classes (role-based access)
-   `POST /classes` – create a class (admin only)
-   `PUT /classes/{id}` – update class (admin only)
-   `POST /classes/{id}/confirm` – confirm class (professor only)

### Users

-   `GET /users` – list users (admin only)
-   `POST /users` – create user (admin only)
-   `PUT /users/{id}` – update user roles (admin only)

## Technical Highlights

-   **Laravel 10** with **Sanctum / Passport** for stateless API authentication.
-   **Role-based access control** via middleware.
-   **JSON responses only**, ready for frontend integration (React, Vue, mobile apps).
-   Fully **closed system**: all data access requires login.
-   Designed for easy **scaling**, adding new features like notifications, attendance tracking, or reports.
