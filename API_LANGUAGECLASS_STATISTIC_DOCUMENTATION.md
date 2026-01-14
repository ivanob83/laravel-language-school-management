# Language Class Statistics API Documentation

This document describes the statistics API routes for the Language School project.

---

## Statistics Routes

All statistics routes require authentication and admin role.

| Route                       | Method | Role  | Description                                           | Query Parameters | Response                                                                                                                                                   |
| --------------------------- | ------ | ----- | ----------------------------------------------------- | ---------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `/statistics/professors`    | GET    | Admin | Get statistics for all professors                     | —                | `[ { id, name, full_name, email, total_classes, active_classes, completed_classes, total_students } ]`                                                     |
| `/statistics/students`      | GET    | Admin | Get statistics for all students                       | —                | `[ { id, name, full_name, email, enrolled_classes, active_classes, completed_classes } ]`                                                                  |
| `/statistics/daily-classes` | GET    | Admin | Get daily class statistics grouped by date and status | —                | `[ { date, total_classes, active_classes, completed_classes, total_students, classes: [{id, title, schedule_time, status, professor, students_count}] } ]` |

---

## Services

The following services handle the business logic for statistics:

-   **LanguageClassStatisticsService**: Handles professor, student, and daily class statistics
-   **StatisticsService**: Base statistics service
-   **UserStatisticsService**: Handles user-specific statistics
