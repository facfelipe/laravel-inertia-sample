# Environment Setup Guide

This guide provides comprehensive instructions for setting up your environment variables for the Laravel Inertia Medical Records application.

## Quick Setup

1. Copy the environment template below to your `.env` file
2. Update the database credentials
3. Choose your WebSocket broadcasting option (Reverb or Pusher)
4. Generate your application key: `php artisan key:generate`

## Environment Variables Template

Create a `.env` file in your project root with the following content:

```env
APP_NAME="Laravel Medical Records"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost:8000

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# Database Configuration
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=medical_records
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Session Configuration
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

# Broadcasting Configuration
# Options: reverb, pusher, redis, log, null
BROADCAST_DRIVER=reverb
QUEUE_CONNECTION=database

# Cache Configuration
CACHE_STORE=database
CACHE_PREFIX=

# Mail Configuration
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# Laravel Reverb Configuration (Option 1: Self-hosted WebSocket server)
# This is the recommended option for Laravel 12
REVERB_APP_ID=local
REVERB_APP_KEY=local
REVERB_APP_SECRET=local
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# Reverb Server Configuration
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080

# Frontend Configuration for Reverb
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

# Pusher Configuration (Option 2: Use Pusher.com service)
# Uncomment these lines if you prefer to use Pusher instead of Reverb
# Sign up at https://pusher.com and create a new app
# BROADCAST_DRIVER=pusher
# PUSHER_APP_ID=your_app_id
# PUSHER_APP_KEY=your_app_key
# PUSHER_APP_SECRET=your_app_secret
# PUSHER_HOST=
# PUSHER_PORT=443
# PUSHER_SCHEME=https
# PUSHER_APP_CLUSTER=mt1
# 
# VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
# VITE_PUSHER_HOST="${PUSHER_HOST}"
# VITE_PUSHER_PORT="${PUSHER_PORT}"
# VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
# VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

# Redis Configuration (Optional - for better performance and scaling)
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1

# AWS Configuration (if using AWS services)
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

# Vite Configuration
VITE_APP_NAME="${APP_NAME}"
VITE_APP_ENV="${APP_ENV}"
```

## WebSocket Broadcasting Options

### Option 1: Laravel Reverb (Recommended)

Laravel Reverb is the official, first-party WebSocket server for Laravel 12. It's self-hosted, fast, and integrates seamlessly with Laravel.

**Advantages:**
- ✅ Official Laravel package
- ✅ Self-hosted (no external dependencies)
- ✅ High performance
- ✅ Built-in monitoring with Laravel Pulse
- ✅ Free to use
- ✅ Easy to scale horizontally

**Setup:**
1. Use the Reverb configuration in your `.env` file (already included above)
2. Start the Reverb server: `php artisan reverb:start`
3. Your WebSocket server will be available at `http://localhost:8080`

**For Production:**
- Use a process manager like Supervisor to keep Reverb running
- Configure a reverse proxy (Nginx) to route WebSocket traffic
- Consider using Redis for horizontal scaling

### Option 2: Pusher.com Service

Pusher is a managed WebSocket service that handles all the infrastructure for you.

**Advantages:**
- ✅ Managed service (no server maintenance)
- ✅ Global CDN
- ✅ Built-in analytics
- ✅ Automatic scaling

**Disadvantages:**
- ❌ Costs money (after free tier)
- ❌ External dependency
- ❌ Data goes through third-party servers

**Setup:**
1. Sign up at [pusher.com](https://pusher.com)
2. Create a new app in your Pusher dashboard
3. Uncomment the Pusher configuration in your `.env` file
4. Replace the placeholder values with your actual Pusher credentials
5. Change `BROADCAST_DRIVER=pusher`

## Database Setup

### PostgreSQL (Recommended)

1. Install PostgreSQL on your system
2. Create a database: `createdb medical_records`
3. Create a user with appropriate permissions
4. Update the database credentials in your `.env` file

### Alternative: SQLite (for development)

If you prefer SQLite for local development:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

## Queue Configuration

The application uses queues for broadcasting events. For development, the database queue driver is sufficient:

```env
QUEUE_CONNECTION=database
```

For production, consider using Redis:

```env
QUEUE_CONNECTION=redis
```

## Testing the Setup

### 1. Verify Database Connection
```bash
php artisan migrate
```

### 2. Test WebSocket Broadcasting

**With Reverb:**
```bash
# Terminal 1: Start Reverb server
php artisan reverb:start

# Terminal 2: Test broadcasting
php artisan test:broadcast
```

**With Pusher:**
```bash
# Test broadcasting (Pusher handles the server)
php artisan test:broadcast
```

### 3. Frontend Testing
1. Open your application in multiple browser tabs
2. Navigate to the Home page or Medical Records page
3. Create a new medical record in one tab
4. Watch for real-time updates in other tabs
5. Check browser console for WebSocket connection logs

## Troubleshooting

### WebSocket Connection Issues

**Problem:** "WebSocket Broadcasting Disabled" message in console
**Solution:** Ensure your environment variables are properly set and the frontend is rebuilt:
```bash
npm run build
# or for development
npm run dev
```

**Problem:** Reverb server won't start
**Solution:** Check if port 8080 is already in use:
```bash
lsof -i :8080
# Kill any processes using the port, then restart Reverb
```

**Problem:** No real-time updates
**Solution:** 
1. Check WebSocket connection in browser console
2. Verify queue worker is running: `php artisan queue:work`
3. Check Reverb/Pusher logs for errors

### Database Issues

**Problem:** Migration fails
**Solution:** 
1. Verify database credentials in `.env`
2. Ensure database exists and user has proper permissions
3. Check database server is running

### Environment Variables Not Loading

**Problem:** Environment variables showing as `null`
**Solution:**
1. Ensure `.env` file is in project root
2. Clear configuration cache: `php artisan config:clear`
3. Restart development server

## Production Considerations

### Security
- Use strong, unique values for all keys and secrets
- Enable HTTPS for WebSocket connections
- Implement proper authentication for WebSocket channels
- Use environment-specific configuration files

### Performance
- Use Redis for caching and queues
- Configure proper queue workers
- Set up horizontal scaling for Reverb if needed
- Monitor WebSocket connections and memory usage

### Monitoring
- Enable Laravel Pulse for Reverb monitoring
- Set up proper logging and error tracking
- Monitor queue job processing
- Track WebSocket connection metrics

## Getting Help

If you encounter issues:

1. Check the browser console for JavaScript errors
2. Review Laravel logs: `php artisan pail` or `tail -f storage/logs/laravel.log`
3. Verify environment variables: `php artisan config:show`
4. Test WebSocket connection manually using browser developer tools
5. Consult the [Laravel Reverb documentation](https://laravel.com/docs/12.x/reverb)

## Quick Commands Reference

```bash
# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed

# Start Reverb server
php artisan reverb:start

# Start queue worker
php artisan queue:work

# Test broadcasting
php artisan test:broadcast

# Clear caches
php artisan config:clear
php artisan cache:clear

# Build frontend assets
npm run build

# Start development server
php artisan serve
``` 