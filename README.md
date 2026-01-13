# Laravel Breeze REST API

Stateless Laravel REST API using Breeze for authentication and user management.
This API provides endpoints for user registration, login, logout, profile update, password change, and account deletion.

---

## Features

-   User registration, login, logout (stateless, token-based)
-   User profile update and password change
-   Laravel Breeze starter kit for authentication scaffolding
-   Fully tested with Feature and Unit tests
-   API resources for consistent JSON responses

---

## Installation

1. Clone the repository:

```bash
git clone https://github.com/yourusername/laravel-breeze-rest-api.git
cd laravel-breeze-rest-api
```

2. Install dependencies:

```bash
composer install
```

3. Copy `.env` file:

```bash
cp .env.example .env
```

4. Configure database in `.env`:

```env
DB_CONNECTION=mysql
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Run migrations:

```bash
php artisan migrate
```

---

## Running the API

Start the development server:

```bash
php artisan serve
```

API base URL: `http://localhost:8000`

---

## API Endpoints

| Method    | Endpoint           | Description                 | Request Body Example                                                                                             |
| --------- | ------------------ | --------------------------- | ---------------------------------------------------------------------------------------------------------------- |
| POST      | /api/register      | Register new user           | `{ "name": "John", "email": "john@example.com", "password": "secret123", "password_confirmation": "secret123" }` |
| POST      | /api/login         | Login and receive API token | `{ "email": "john@example.com", "password": "secret123" }`                                                       |
| POST      | /api/logout        | Logout authenticated user   | _Authorization: Bearer {token}_                                                                                  |
| GET       | /api/user          | Get authenticated user info | _Authorization: Bearer {token}_                                                                                  |
| PUT/PATCH | /api/user          | Update user profile         | `{ "name": "New Name", "email": "newemail@example.com" }`                                                        |
| PUT/PATCH | /api/user/password | Update user password        | `{ "current_password": "oldpass", "password": "newpass", "password_confirmation": "newpass" }`                   |
| DELETE    | /api/user          | Delete user account         | `{ "password": "userpass" }`                                                                                     |

---

## Example Responses

**Register / Login Success**

```json
{
    "token": "1|abcd1234efgh5678ijkl",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
    }
}
```

**Get User**

```json
{
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
}
```

**Update Profile Success**

```json
{
    "success": true,
    "user": {
        "id": 1,
        "name": "New Name",
        "email": "newemail@example.com"
    }
}
```

**Update Password / Delete Account Success**

```json
{
    "success": true,
    "message": "Password updated successfully" // or "Account deleted successfully"
}
```

**Logout Success**

Status: 204 No Content

---

## Running Tests

This project includes **Feature** and **Unit** tests.

1. Make sure you have a test database set in `.env.testing`:

```env
DB_CONNECTION=mysql
DB_DATABASE=laravel_test
DB_USERNAME=root
DB_PASSWORD=...
```

2. Run all tests:

```bash
php artisan test --env=testing
```

3. Run only Unit tests:

```bash
php artisan test --testsuite=Unit --env=testing
```

4. Run only Feature tests:

```bash
php artisan test --testsuite=Feature --env=testing
```

Laravel will automatically run migrations for the test database before each test.

---

## License

MIT License
