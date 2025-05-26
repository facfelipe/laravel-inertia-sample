import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Make Pusher available globally
window.Pusher = Pusher;

// Check environment variables for both Reverb and Pusher
const reverbKey = import.meta.env.VITE_REVERB_APP_KEY;
const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;

// Determine which broadcasting service to use
const useReverb = reverbKey && reverbKey !== 'undefined';
const usePusher = pusherKey && pusherKey !== 'undefined';

// Create a mock Echo instance that doesn't break the app
const createMockEcho = () => ({
    channel: () => ({
        listen: () => {},
        subscribed: () => {},
        error: () => {},
    }),
    private: () => ({
        listen: () => {},
        subscribed: () => {},
        error: () => {},
    }),
    join: () => ({
        listen: () => {},
        subscribed: () => {},
        error: () => {},
    }),
});

if (!useReverb && !usePusher) {
    console.warn('⚠️ WebSocket Broadcasting Disabled: No broadcasting service configured');
    console.info('📖 To enable real-time features, configure either:');
    console.info('   • Laravel Reverb (recommended): Set VITE_REVERB_APP_KEY');
    console.info('   • Pusher: Set VITE_PUSHER_APP_KEY');
    console.info('📚 See docs/ENVIRONMENT_SETUP.md for detailed setup instructions');
    
    window.Echo = createMockEcho();
} else {
    try {
        if (useReverb) {
            // Configure Laravel Reverb
            console.log('🚀 Initializing Laravel Reverb WebSocket connection...');
            
            window.Echo = new Echo({
                broadcaster: 'reverb',
                key: reverbKey,
                wsHost: import.meta.env.VITE_REVERB_HOST || 'localhost',
                wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
                wssPort: import.meta.env.VITE_REVERB_PORT || 8080,
                forceTLS: (import.meta.env.VITE_REVERB_SCHEME || 'http') === 'https',
                enabledTransports: ['ws', 'wss'],
                disableStats: true,
            });
            
            console.log('✅ Laravel Reverb WebSocket Broadcasting Enabled');
            console.info(`🔗 WebSocket server: ${import.meta.env.VITE_REVERB_SCHEME || 'http'}://${import.meta.env.VITE_REVERB_HOST || 'localhost'}:${import.meta.env.VITE_REVERB_PORT || 8080}`);
            
        } else if (usePusher) {
            // Configure Pusher
            console.log('🚀 Initializing Pusher WebSocket connection...');
            
            const pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1';
            
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: pusherKey,
                cluster: pusherCluster,
                wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${pusherCluster}.pusherapp.com`,
                wsPort: import.meta.env.VITE_PUSHER_PORT || 80,
                wssPort: import.meta.env.VITE_PUSHER_PORT || 443,
                forceTLS: (import.meta.env.VITE_PUSHER_SCHEME || 'https') === 'https',
                enabledTransports: ['ws', 'wss'],
            });
            
            console.log('✅ Pusher WebSocket Broadcasting Enabled');
            console.info(`🔗 Pusher cluster: ${pusherCluster}`);
        }
        
    } catch (error) {
        console.error('❌ Failed to initialize WebSocket connection:', error);
        
        if (useReverb) {
            console.error('💡 Reverb connection failed. Make sure:');
            console.error('   • Reverb server is running: php artisan reverb:start');
            console.error('   • Environment variables are correct in .env file');
            console.error('   • Port 8080 is not blocked by firewall');
        } else if (usePusher) {
            console.error('💡 Pusher connection failed. Make sure:');
            console.error('   • Pusher credentials are correct in .env file');
            console.error('   • Your Pusher app is active');
            console.error('   • Network allows WebSocket connections');
        }
        
        console.info('📚 Check docs/ENVIRONMENT_SETUP.md for troubleshooting guide');
        
        // Fallback to mock Echo instance
        window.Echo = createMockEcho();
    }
}

export default window.Echo; 