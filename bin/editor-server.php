<?php
/**
 * Script to start the WebSocket editor server.
 * The default port is 8080 and listen to all interfaces.
 */
require __DIR__ . '/../vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;


$loop = React\EventLoop\Factory::create();

$port = isset($_SERVER['CHAT_SERVER_PORT']) ? $_SERVER['CHAT_SERVER_PORT'] : 8080;
$bindAddr = isset($_SERVER['CHAT_BIND_ADDR']) ? $_SERVER['CHAT_BIND_ADDR'] : '0.0.0.0';
$file = __DIR__ .'/sock.tmp';

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Text2a4Me\EditorServer($file)
        )
    ),
    $port, $bindAddr
);


printf("Websocket Editor server running on %s:%s.\n--\n", $bindAddr, $port);
$server->run();
