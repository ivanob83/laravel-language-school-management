# Laravel API - Stateless Authentication

This Laravel application is configured for **stateless API authentication only** using Laravel Sanctum. All web routes have been removed.

## API Endpoints

All endpoints are prefixed with `/api`

### Public Endpoints (No Authentication Required)

#### Register User
```http
POST /api/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response:** 204 No Content

#### Login
```http
POST /api/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "created_at": "2026-01-12T...",
        "updated_at": "2026-01-12T..."
    }
}
```

#### Status Check
```http
GET /api/status
```

**Response:**
```json
{
    "status": "API is running"
}
```

#### Forgot Password
```http
POST /api/forgot-password
Content-Type: application/json

{
    "email": "john@example.com"
}
```

**Response:**
```json
{
    "status": "We have emailed your password reset link."
}
```

**Note:** In production, this sends an email with a reset link. For testing, check the `password_reset_tokens` table for the token.

#### Reset Password
```http
POST /api/reset-password
Content-Type: application/json

{
    "token": "reset-token-from-email",
    "email": "john@example.com",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

**Response:**
```json
{
    "status": "Your password has been reset."
}
```

### Protected Endpoints (Authentication Required)

All protected endpoints require the `Authorization` header with a Bearer token:

```http
Authorization: Bearer {your-api-token}
```

#### Get Current User
```http
GET /api/user
Authorization: Bearer {token}
```

**Response:**
```json
{
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "email_verified_at": null,
    "created_at": "2026-01-12T...",
    "updated_at": "2026-01-12T..."
}
```

#### Logout (Revoke Token)
```http
POST /api/logout
Authorization: Bearer {token}
```

**Response:** 204 No Content

#### Posts Resource
All posts endpoints require authentication:

- `GET /api/posts` - List all posts
- `POST /api/posts` - Create a new post
- `GET /api/posts/{id}` - Get a specific post
- `PUT/PATCH /api/posts/{id}` - Update a post
- `DELETE /api/posts/{id}` - Delete a post

## Configuration Changes

### Stateless Authentication
- **Sanctum configured for token-based authentication only**
- No session cookies or CSRF protection for API routes
- Stateful domains set to empty array in `config/sanctum.php`

### Removed Web Components
- Web routes cleared in `routes/web.php`
- Web middleware group emptied in `app/Http/Kernel.php`
- RouteServiceProvider no longer loads web routes

### API Middleware
The API middleware group includes:
- Rate limiting (60 requests per minute per IP/user)
- Route model binding

## Testing the API

### Using cURL

**Register:**
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Login:**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

**Get User (with token):**
```bash
curl -X GET http://localhost:8000/api/user \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Forgot Password:**
```bash
curl -X POST http://localhost:8000/api/forgot-password \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "test@example.com"
  }'
```

**Reset Password:**
```bash
curl -X POST http://localhost:8000/api/reset-password \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "token": "RESET_TOKEN_FROM_EMAIL",
    "email": "test@example.com",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
  }'
```

### Using Postman

1. Create a new request
2. Set the method and URL
3. For protected routes, add header: `Authorization: Bearer {your-token}`
4. Set `Accept: application/json` header
5. For POST/PUT requests, set `Content-Type: application/json` and add JSON body

## Security Features

- **Password hashing** using Laravel's Hash facade
- **Rate limiting** on authentication endpoints
- **Token-based authentication** using Sanctum
- **Input validation** on all endpoints
- **Mass assignment protection** via fillable properties

## Running the Application

```bash
# Start the development server
php artisan serve

# The API will be available at:
# http://localhost:8000/api
```

## Database Migrations

All necessary tables are already migrated:
- users
- password_reset_tokens
- failed_jobs
- personal_access_tokens (for Sanctum)
- posts

## Environment Configuration

Make sure your `.env` file has the database configured:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## Notes

- This application is **API-only** - no web views or sessions
- All authentication is **stateless** using API tokens
- Tokens do not expire by default (set in `config/sanctum.php`)
- Each login creates a new token
- Logout revokes only the current token
