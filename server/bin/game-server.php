<?php

/**
 * Executable that instantiates the game server
 *
 * @see http://socketo.me/docs/hello-world#nextsteps
 */

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use GameServer\GameRoom;

require dirname(__DIR__) . '/vendor/autoload.php';

$server = IoServer::factory(
  new HttpServer(
    new WsServer(
      new GameRoom()
    )
  ),
  8181
);

echo "Starting...\n";
$server->run();