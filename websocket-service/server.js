/**
 * WebSocket Server for Real-time Presentation Collaboration
 * Port: 3002
 * Features: Room-based broadcasting, automatic reconnection, presence tracking
 */

const WebSocket = require('ws');

const PORT = process.env.PORT || 3002;
const wss = new WebSocket.Server({ port: PORT });

// Data structures
const rooms = new Map(); // presentationId -> Set of clients
const clientInfo = new Map(); // ws -> { userId, username, presentationId }

// Statistics
let totalConnections = 0;
let totalMessages = 0;
const startTime = Date.now();

console.log('='.repeat(60));
console.log('WebSocket Server for Presentation Collaboration');
console.log('='.repeat(60));
console.log(`Server started on port ${PORT}`);
console.log(`Waiting for connections...`);
console.log('='.repeat(60));

wss.on('connection', (ws, req) => {
    totalConnections++;
    const clientIp = req.socket.remoteAddress;
    
    console.log(`\n[CONNECTION] New client connected from ${clientIp}`);
    console.log(`[STATS] Total connections: ${totalConnections}, Active: ${wss.clients.size}`);
    
    // Send welcome message
    ws.send(JSON.stringify({
        type: 'connected',
        message: 'Connected to WebSocket server',
        serverId: process.pid,
        timestamp: Date.now()
    }));
    
    ws.on('message', (message) => {
        totalMessages++;
        
        try {
            const data = JSON.parse(message);
            console.log(`[MESSAGE] Type: ${data.type}, Presentation: ${data.presentationId || 'N/A'}`);
            
            switch(data.type) {
                case 'join':
                    handleJoin(ws, data);
                    break;
                    
                case 'leave':
                    handleLeave(ws, data);
                    break;
                    
                case 'slide-update':
                    handleSlideUpdate(ws, data);
                    break;
                    
                case 'slide-created':
                    handleSlideCreated(ws, data);
                    break;
                    
                case 'slide-deleted':
                    handleSlideDeleted(ws, data);
                    break;
                    
                case 'presentation-update':
                    handlePresentationUpdate(ws, data);
                    break;
                    
                case 'cursor-move':
                    handleCursorMove(ws, data);
                    break;
                    
                case 'ping':
                    ws.send(JSON.stringify({
                        type: 'pong',
                        timestamp: Date.now()
                    }));
                    break;
                    
                case 'get-stats':
                    sendStats(ws);
                    break;
                    
                default:
                    console.log(`[WARNING] Unknown message type: ${data.type}`);
                    ws.send(JSON.stringify({
                        type: 'error',
                        message: 'Unknown message type',
                        receivedType: data.type
                    }));
            }
        } catch (error) {
            console.error('[ERROR] Failed to parse message:', error.message);
            ws.send(JSON.stringify({
                type: 'error',
                message: 'Invalid JSON format'
            }));
        }
    });
    
    ws.on('close', () => {
        const info = clientInfo.get(ws);
        console.log(`[DISCONNECT] Client disconnected`);
        
        if (info) {
            console.log(`  User: ${info.username || 'Unknown'}, Presentation: ${info.presentationId}`);
            handleLeave(ws, { presentationId: info.presentationId });
            clientInfo.delete(ws);
        }
        
        console.log(`[STATS] Active connections: ${wss.clients.size}`);
    });
    
    ws.on('error', (error) => {
        console.error('[ERROR] WebSocket error:', error.message);
    });
});

/**
 * Handle client joining a presentation room
 */
function handleJoin(ws, data) {
    const { presentationId, userId, username } = data;
    
    if (!presentationId) {
        ws.send(JSON.stringify({
            type: 'error',
            message: 'presentationId is required'
        }));
        return;
    }
    
    // Create room if doesn't exist
    if (!rooms.has(presentationId)) {
        rooms.set(presentationId, new Set());
        console.log(`[ROOM] Created new room: ${presentationId}`);
    }
    
    // Add client to room
    rooms.get(presentationId).add(ws);
    
    // Store client info
    clientInfo.set(ws, {
        userId: userId || 'anonymous',
        username: username || 'Anonymous User',
        presentationId: presentationId,
        joinedAt: Date.now()
    });
    
    ws.presentationId = presentationId;
    
    const roomSize = rooms.get(presentationId).size;
    
    console.log(`[JOIN] User "${username || 'Anonymous'}" joined presentation ${presentationId}`);
    console.log(`[ROOM] Presentation ${presentationId} now has ${roomSize} viewer(s)`);
    
    // Notify client
    ws.send(JSON.stringify({
        type: 'joined',
        presentationId: presentationId,
        roomSize: roomSize,
        message: `Successfully joined presentation ${presentationId}`
    }));
    
    // Notify others in room
    broadcastToRoom(presentationId, {
        type: 'user-joined',
        userId: userId || 'anonymous',
        username: username || 'Anonymous User',
        roomSize: roomSize,
        timestamp: Date.now()
    }, ws);
}

/**
 * Handle client leaving a presentation room
 */
function handleLeave(ws, data) {
    const info = clientInfo.get(ws);
    const presentationId = data.presentationId || (info && info.presentationId);
    
    if (!presentationId || !rooms.has(presentationId)) {
        return;
    }
    
    const room = rooms.get(presentationId);
    room.delete(ws);
    
    const roomSize = room.size;
    
    console.log(`[LEAVE] User left presentation ${presentationId}`);
    console.log(`[ROOM] Presentation ${presentationId} now has ${roomSize} viewer(s)`);
    
    // Delete empty rooms
    if (roomSize === 0) {
        rooms.delete(presentationId);
        console.log(`[ROOM] Deleted empty room: ${presentationId}`);
    } else {
        // Notify others
        broadcastToRoom(presentationId, {
            type: 'user-left',
            userId: info ? info.userId : 'unknown',
            username: info ? info.username : 'Unknown User',
            roomSize: roomSize,
            timestamp: Date.now()
        });
    }
}

/**
 * Handle slide content update
 */
function handleSlideUpdate(ws, data) {
    const { presentationId, slideId, content, title, layout } = data;
    
    if (!presentationId || !slideId) {
        ws.send(JSON.stringify({
            type: 'error',
            message: 'presentationId and slideId are required'
        }));
        return;
    }
    
    const info = clientInfo.get(ws);
    
    console.log(`[UPDATE] Slide ${slideId} updated in presentation ${presentationId}`);
    
    // Broadcast to all in room except sender
    broadcastToRoom(presentationId, {
        type: 'slide-updated',
        slideId: slideId,
        content: content,
        title: title,
        layout: layout,
        updatedBy: info ? info.username : 'Unknown User',
        timestamp: Date.now()
    }, ws);
    
    // Confirm to sender
    ws.send(JSON.stringify({
        type: 'update-confirmed',
        slideId: slideId,
        timestamp: Date.now()
    }));
}

/**
 * Handle new slide creation
 */
function handleSlideCreated(ws, data) {
    const { presentationId, slideId, title, layout } = data;
    
    if (!presentationId || !slideId) {
        return;
    }
    
    const info = clientInfo.get(ws);
    
    console.log(`[CREATE] New slide ${slideId} created in presentation ${presentationId}`);
    
    broadcastToRoom(presentationId, {
        type: 'slide-created',
        slideId: slideId,
        title: title || 'New Slide',
        layout: layout || 'blank',
        createdBy: info ? info.username : 'Unknown User',
        timestamp: Date.now()
    }, ws);
}

/**
 * Handle slide deletion
 */
function handleSlideDeleted(ws, data) {
    const { presentationId, slideId } = data;
    
    if (!presentationId || !slideId) {
        return;
    }
    
    const info = clientInfo.get(ws);
    
    console.log(`[DELETE] Slide ${slideId} deleted from presentation ${presentationId}`);
    
    broadcastToRoom(presentationId, {
        type: 'slide-deleted',
        slideId: slideId,
        deletedBy: info ? info.username : 'Unknown User',
        timestamp: Date.now()
    }, ws);
}

/**
 * Handle presentation metadata update
 */
function handlePresentationUpdate(ws, data) {
    const { presentationId, title, theme, language } = data;
    
    if (!presentationId) {
        return;
    }
    
    const info = clientInfo.get(ws);
    
    console.log(`[UPDATE] Presentation ${presentationId} metadata updated`);
    
    broadcastToRoom(presentationId, {
        type: 'presentation-updated',
        title: title,
        theme: theme,
        language: language,
        updatedBy: info ? info.username : 'Unknown User',
        timestamp: Date.now()
    }, ws);
}

/**
 * Handle cursor movement (for collaborative editing)
 */
function handleCursorMove(ws, data) {
    const { presentationId, slideId, x, y } = data;
    
    if (!presentationId) {
        return;
    }
    
    const info = clientInfo.get(ws);
    
    // Broadcast cursor position (throttled on client side)
    broadcastToRoom(presentationId, {
        type: 'cursor-moved',
        slideId: slideId,
        x: x,
        y: y,
        userId: info ? info.userId : 'unknown',
        username: info ? info.username : 'Unknown'
    }, ws);
}

/**
 * Broadcast message to all clients in a room
 */
function broadcastToRoom(presentationId, message, excludeWs = null) {
    const room = rooms.get(presentationId);
    
    if (!room) {
        return;
    }
    
    const messageStr = JSON.stringify(message);
    let broadcastCount = 0;
    
    room.forEach(client => {
        if (client !== excludeWs && client.readyState === WebSocket.OPEN) {
            client.send(messageStr);
            broadcastCount++;
        }
    });
    
    console.log(`[BROADCAST] Sent "${message.type}" to ${broadcastCount} client(s) in room ${presentationId}`);
}

/**
 * Send server statistics
 */
function sendStats(ws) {
    const uptime = Date.now() - startTime;
    const uptimeMinutes = Math.floor(uptime / 60000);
    
    const stats = {
        type: 'stats',
        totalConnections: totalConnections,
        activeConnections: wss.clients.size,
        totalMessages: totalMessages,
        activeRooms: rooms.size,
        uptime: uptime,
        uptimeFormatted: `${uptimeMinutes} minutes`,
        rooms: Array.from(rooms.entries()).map(([id, clients]) => ({
            presentationId: id,
            viewers: clients.size
        }))
    };
    
    ws.send(JSON.stringify(stats));
}

// Health check endpoint (for monitoring)
const http = require('http');
const healthServer = http.createServer((req, res) => {
    if (req.url === '/health') {
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({
            status: 'OK',
            service: 'WebSocket Server',
            port: PORT,
            connections: wss.clients.size,
            rooms: rooms.size,
            uptime: Date.now() - startTime
        }));
    } else {
        res.writeHead(404);
        res.end('Not Found');
    }
});

healthServer.listen(PORT + 1, () => {
    console.log(`Health check endpoint: http://localhost:${PORT + 1}/health`);
});

// Graceful shutdown
process.on('SIGTERM', () => {
    console.log('\n[SHUTDOWN] Received SIGTERM, closing server...');
    
    wss.clients.forEach(client => {
        client.send(JSON.stringify({
            type: 'server-shutdown',
            message: 'Server is shutting down'
        }));
        client.close();
    });
    
    wss.close(() => {
        console.log('[SHUTDOWN] Server closed');
        process.exit(0);
    });
});

// Handle errors
process.on('uncaughtException', (error) => {
    console.error('[FATAL] Uncaught exception:', error);
    process.exit(1);
});

process.on('unhandledRejection', (reason, promise) => {
    console.error('[ERROR] Unhandled rejection:', reason);
});
