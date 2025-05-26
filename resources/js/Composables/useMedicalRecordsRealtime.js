import { ref, onMounted, onUnmounted } from 'vue';

export function useMedicalRecordsRealtime() {
    const medicalRecords = ref([]);
    const stats = ref({
        total_records: 0,
        records_this_month: 0,
        status_counts: {}
    });
    const isConnected = ref(false);
    const connectionStatus = ref('disconnected'); // 'connecting', 'connected', 'disconnected', 'error'
    const lastUpdate = ref(null);
    const broadcastingEnabled = ref(false);

    let channel = null;

    const initializeRealtime = (initialRecords = [], initialStats = {}) => {
        // Set initial data
        medicalRecords.value = initialRecords;
        stats.value = { ...stats.value, ...initialStats };

        // Check if Echo is available and properly configured
        if (!window.Echo) {
            console.warn('⚠️ Laravel Echo not available - real-time features disabled');
            connectionStatus.value = 'disabled';
            broadcastingEnabled.value = false;
            return;
        }

        // Check if Echo is a mock instance (no broadcasting configured)
        if (typeof window.Echo.channel !== 'function') {
            console.warn('⚠️ WebSocket broadcasting not configured - real-time features disabled');
            connectionStatus.value = 'disabled';
            broadcastingEnabled.value = false;
            return;
        }

        broadcastingEnabled.value = true;
        connectionStatus.value = 'connecting';

        try {
            // Connect to WebSocket channel
            channel = window.Echo.channel('medical-records');
            
            // Listen for medical record updates
            channel.listen('.medical.record.updated', (event) => {
                console.log('📡 Medical record update received:', event.action);
                handleMedicalRecordUpdate(event);
                lastUpdate.value = new Date();
            });

            // Listen for connection events
            channel.subscribed(() => {
                console.log('✅ Connected to medical-records channel');
                isConnected.value = true;
                connectionStatus.value = 'connected';
            });

            channel.error((error) => {
                console.error('❌ WebSocket error:', error);
                isConnected.value = false;
                connectionStatus.value = 'error';
            });

            // Set a timeout to detect connection issues
            setTimeout(() => {
                if (connectionStatus.value === 'connecting') {
                    console.warn('⚠️ WebSocket connection timeout - check server status');
                    connectionStatus.value = 'error';
                }
            }, 10000); // 10 second timeout

        } catch (error) {
            console.error('❌ Failed to initialize WebSocket connection:', error);
            connectionStatus.value = 'error';
            broadcastingEnabled.value = false;
        }
    };

    const handleMedicalRecordUpdate = (event) => {
        const { medical_record, action } = event;
        
        switch (action) {
            case 'created':
                // Add new record to the beginning of the list
                medicalRecords.value.unshift(medical_record);
                updateStats('increment');
                break;
                
            case 'updated':
            case 'status_changed':
            case 'test_broadcast':
                // Find and update existing record
                const index = medicalRecords.value.findIndex(record => record.id === medical_record.id);
                if (index !== -1) {
                    medicalRecords.value[index] = medical_record;
                } else {
                    // If record not found, add it (might be a new record that matches current filters)
                    medicalRecords.value.unshift(medical_record);
                    updateStats('increment');
                }
                break;
                
            case 'deleted':
                // Remove record from list
                const deleteIndex = medicalRecords.value.findIndex(record => record.id === medical_record.id);
                if (deleteIndex !== -1) {
                    medicalRecords.value.splice(deleteIndex, 1);
                    updateStats('decrement');
                }
                break;
        }
        
        // Update status counts if it's a status change
        if (action === 'status_changed' || action === 'created') {
            updateStatusCounts();
        }
    };

    const updateStats = (operation) => {
        if (operation === 'increment') {
            stats.value.total_records++;
            // Check if it's from this month
            const now = new Date();
            const recordDate = new Date();
            if (recordDate.getMonth() === now.getMonth() && recordDate.getFullYear() === now.getFullYear()) {
                stats.value.records_this_month++;
            }
        } else if (operation === 'decrement') {
            stats.value.total_records = Math.max(0, stats.value.total_records - 1);
        }
    };

    const updateStatusCounts = () => {
        const statusCounts = {};
        medicalRecords.value.forEach(record => {
            const status = record.current_status || 'Unknown';
            statusCounts[status] = (statusCounts[status] || 0) + 1;
        });
        stats.value.status_counts = statusCounts;
    };

    const getConnectionStatusText = () => {
        switch (connectionStatus.value) {
            case 'connecting':
                return 'Connecting...';
            case 'connected':
                return 'Connected';
            case 'disconnected':
                return 'Disconnected';
            case 'error':
                return 'Connection Error';
            case 'disabled':
                return 'Disabled';
            default:
                return 'Unknown';
        }
    };

    const getConnectionStatusColor = () => {
        switch (connectionStatus.value) {
            case 'connecting':
                return 'text-yellow-500';
            case 'connected':
                return 'text-green-500';
            case 'disconnected':
                return 'text-gray-500';
            case 'error':
                return 'text-red-500';
            case 'disabled':
                return 'text-gray-400';
            default:
                return 'text-gray-500';
        }
    };

    const disconnect = () => {
        if (channel) {
            try {
                window.Echo.leaveChannel('medical-records');
            } catch (error) {
                console.warn('Error leaving channel:', error);
            }
            channel = null;
            isConnected.value = false;
            connectionStatus.value = 'disconnected';
        }
    };

    // Cleanup on unmount
    onUnmounted(() => {
        disconnect();
    });

    return {
        medicalRecords,
        stats,
        isConnected,
        connectionStatus,
        lastUpdate,
        broadcastingEnabled,
        initializeRealtime,
        disconnect,
        handleMedicalRecordUpdate,
        getConnectionStatusText,
        getConnectionStatusColor
    };
} 