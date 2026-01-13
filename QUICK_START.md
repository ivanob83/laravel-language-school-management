# Quick Start Guide - Laravel Stateless API

## What's Ready

✅ Stateless API authentication using Laravel Sanctum  
✅ Token-based authentication (no sessions/cookies)  
✅ All web routes removed  
✅ Authentication endpoints: register, login, logout  
✅ Password reset endpoints: forgot-password, reset-password  
✅ Protected user and posts endpoints  

## API Endpoints

### Public (No Token Required)
- `POST /api/register` - Create new user account
- `POST /api/login` - Get authentication token
- `POST /api/forgot-password` - Request password reset
- `POST /api/reset-password` - Reset password with token
- `GET /api/status` - Health check

### Protected (Bearer Token Required)
- `GET /api/user` - Get authenticated user details
- `POST /api/logout` - Revoke current token
- `GET /api/posts` - List all posts
- `POST /api/posts` - Create post
- `GET /api/posts/{id}` - Get specific post
- `PUT /api/posts/{id}` - Update post
- `DELETE /api/posts/{id}` - Delete post

## Quick Test

1. **Start server:**
   ```bash
   php artisan serve
   ```

2. **Register a user:**
   ```bash
   curl -X POST http://localhost:8000/api/register \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{"name":"Test User","email":"test@example.com","password":"password123","password_confirmation":"password123"}'
   ```

3. **Login to get token:**
   ```bash
   curl -X POST http://localhost:8000/api/login \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{"email":"test@example.com","password":"password123"}'
   ```
   
   **Save the token from response!**

4. **Use token to access protected endpoint:**
   ```bash
   curl -X GET http://localhost:8000/api/user \
     -H "Accept: application/json" \
     -H "Authorization: Bearer YOUR_TOKEN_HERE"
   ```

## Testing with PowerShell

```powershell
# Run the automated test script
.\test-api.ps1
```

## Important Headers

All API requests should include:
```
Accept: application/json
Content-Type: application/json  # For POST/PUT requests
Authorization: Bearer {token}   # For protected endpoints
```

## Configuration Summary

- **Authentication:** Stateless (token-based only)
- **Tokens:** Never expire (by default)
- **Rate Limit:** 60 requests/minute per IP
- **Login Rate Limit:** 5 attempts then throttled
- **Sessions:** Disabled
- **CSRF:** Not required (stateless API)
- **Web Routes:** Removed

## Files to Review

- `routes/api.php` - All API endpoints
- `app/Http/Controllers/Auth/` - Authentication logic
- `API_DOCUMENTATION.md` - Detailed API documentation
- `SETUP_SUMMARY.md` - Complete list of changes

## What Changed

**Removed:**
- All web routes
- Session middleware
- Cookie middleware
- CSRF protection
- Stateful Sanctum authentication

**Added:**
- Registration endpoint
- Login endpoint (returns token)
- Logout endpoint (revokes token)
- Stateless Sanctum configuration

**Configured:**
- Sanctum for token-only authentication
- API routes with auth:sanctum middleware
- Rate limiting on all endpoints

## Next Steps

1. Test the API endpoints
2. Configure CORS if needed for frontend
3. Set up database properly in `.env`
4. Deploy to production when ready

## Need Help?

Check these files:
- `API_DOCUMENTATION.md` - Full API reference
- `SETUP_SUMMARY.md` - Technical details of the setup
- `test-api.ps1` - Working code examples

## Production Checklist

Before deploying:
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Configure database properly
- [ ] Set up HTTPS (required for tokens in production)
- [ ] Configure CORS if needed
- [ ] Consider token expiration settings
- [ ] Set up proper logging
- [ ] Configure queue driver if using background jobs

---

**Your Laravel stateless API is ready to use! 🚀**
