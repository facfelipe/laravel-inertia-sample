# WebSocket Broadcasting Setup

## Overview

This application supports real-time WebSocket broadcasting with two modern options:
1. **Laravel Reverb** (recommended) - Official Laravel 12 WebSocket server
2. **Pusher.com** - Managed WebSocket service

> **⚠️ Note**: Laravel WebSockets is not compatible with Laravel 12. Use Laravel Reverb instead for self-hosted WebSocket functionality.

## Option 1: Laravel Reverb (Recommended) 🚀

Laravel Reverb is the official, first-party WebSocket server for Laravel 12.

### Environment Configuration
Add the following to your `.env` file:

```env
# Broadcasting Configuration
BROADCAST_DRIVER=reverb

# Laravel Reverb Configuration
REVERB_APP_ID=local
REVERB_APP_KEY=local
REVERB_APP_SECRET=local
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# Frontend Configuration (for Vite)
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### Setup Steps
```bash
# Reverb is already installed, just start the server
php artisan reverb:start

# In another terminal, start the queue worker
php artisan queue:work
```

### Advantages
- ✅ Official Laravel package
- ✅ Self-hosted (no external dependencies)
- ✅ High performance
- ✅ Built-in monitoring with Laravel Pulse
- ✅ Free to use

## Option 2: Pusher.com Service 📡

Pusher is a managed WebSocket service.

### Environment Configuration
Add the following to your `.env` file:

```env
# Broadcasting Configuration
BROADCAST_DRIVER=pusher

# Pusher Configuration
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

# Frontend Configuration (for Vite)
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### Setup Steps
1. Create a free account at [Pusher.com](https://pusher.com)
2. Create a new app in your Pusher dashboard
3. Copy the app credentials to your `.env` file
4. Start the queue worker: `php artisan queue:work`

### Advantages
- ✅ Managed service (no server maintenance)
- ✅ Global CDN
- ✅ Built-in analytics
- ✅ Automatic scaling

## Queue Configuration

For production, make sure to set up a queue driver and run queue workers:

```bash
# For development
QUEUE_CONNECTION=database

# For production (recommended)
QUEUE_CONNECTION=redis

# Start queue worker
php artisan queue:work
```

## Testing the Implementation

### Test Broadcasting Command
```bash
# Test with the latest medical record
php artisan test:websocket

# Test with a specific record ID
php artisan test:websocket --record-id=1
```

### Manual Testing
1. **Start your chosen WebSocket service** (Reverb or Pusher)
2. **Open multiple browser tabs** with your application
3. **Navigate to the Home page** or Medical Records page
4. **Create a new medical record** in one tab
5. **Watch real-time updates** appear in other tabs automatically! ✨

## Features Implemented

- ✅ Real-time medical records table updates
- ✅ Live statistics updates (total records, monthly count, status counts)
- ✅ Connection status indicator with visual feedback
- ✅ Automatic reconnection handling
- ✅ Graceful fallback when WebSocket is not configured
- ✅ Broadcasting on create, update, and status changes

## Troubleshooting

### No Real-time Updates?
- Check browser console for WebSocket connection logs
- Ensure queue worker is running: `php artisan queue:work`
- For Reverb: Verify server is running: `php artisan reverb:start`
- Test broadcasting: `php artisan test:websocket`

### Connection Issues?
- **Reverb**: Check if port 8080 is available and not blocked
- **Pusher**: Verify credentials and network connectivity
- Check environment variables are properly set

### Frontend Not Connecting?
- Rebuild frontend assets: `npm run build` or `npm run dev`
- Check browser console for JavaScript errors
- Verify VITE_* environment variables are set

## Production Considerations

### For Reverb
- Use a process manager (Supervisor) to keep Reverb running
- Configure reverse proxy (Nginx) for WebSocket traffic
- Consider Redis for horizontal scaling

### For Pusher
- Monitor usage and costs
- Configure proper authentication for channels
- Set up error tracking and monitoring

For detailed setup instructions, see `docs/ENVIRONMENT_SETUP.md`. 