<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require __DIR__ . '/vendor/autoload.php';

class Chat implements MessageComponentInterface {
    protected $clients;
    protected $users;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->users = [];
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        
        if ($data['type'] === 'register') {
            // Register user
            $this->users[$from->resourceId] = [
                'id' => $data['userId'],
                'name' => $data['username'],
                'conn' => $from
            ];
            
            // Notify all users
            $this->sendUserList();
        } elseif ($data['type'] === 'message') {
            // Handle new message
            $message = [
                'type' => 'message',
                'from' => $this->users[$from->resourceId]['id'],
                'fromName' => $this->users[$from->resourceId]['name'],
                'content' => $data['content'],
                'timestamp' => date('Y-m-d H:i:s'),
                'isPrivate' => $data['isPrivate'] ?? false,
                'to' => $data['to'] ?? null
            ];
            
            if ($message['isPrivate']) {
                // Send private message to specific user
                foreach ($this->users as $user) {
                    if ($user['id'] == $message['to']) {
                        $user['conn']->send(json_encode($message));
                    }
                }
                // Also send to sender (for their own UI)
                $from->send(json_encode($message));
            } else {
                // Broadcast public message
                foreach ($this->clients as $client) {
                    $client->send(json_encode($message));
                }
            }
            
            // Save to database (you would implement this)
            $this->saveMessageToDB($message);
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // Remove user from active list
        if (isset($this->users[$conn->resourceId])) {
            unset($this->users[$conn->resourceId]);
            $this->sendUserList();
        }
        
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error occurred: {$e->getMessage()}\n";
        $conn->close();
    }
    
    protected function sendUserList() {
        $usersList = array_map(function($user) {
            return ['id' => $user['id'], 'name' => $user['name']];
        }, $this->users);
        
        $message = [
            'type' => 'users',
            'users' => $usersList
        ];
        
        foreach ($this->clients as $client) {
            $client->send(json_encode($message));
        }
    }
    
    protected function saveMessageToDB($message) {
        // In a real app, you would save to database here
        // This is just a placeholder
        file_put_contents('chat_log.txt', print_r($message, true), FILE_APPEND);
    }
}

// Run the server
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    8080
);

echo "Chat server running on port 8080\n";
$server->run();