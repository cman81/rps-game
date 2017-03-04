<?php
/**
 * GameRoom
 *
 * Here we keep track of who enters and leaves.
 *
 * Also, any actions performed (chatting, entering/leaving games, making moves)
 * are sent to their appropriate handlers for further processing.
 *
 * @see http://socketo.me/docs/hello-world#chatclass
 */

namespace GameServer;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class GameRoom implements MessageComponentInterface {
  protected $clients;

  public function __construct() {
    $this->clients = new \SplObjectStorage;
  }

  public function onOpen(ConnectionInterface $conn) {
    // Store the new connection to send messages to later
    $this->clients->attach($conn);

    echo "New connection! ({$conn->resourceId})\n";

    $conn->send(json_encode(array(
      'from' => 'GameRoom',
      'operation' => 'setTitle',
      'content' => $conn->resourceId,
    )));
  }

  public function onMessage(ConnectionInterface $from, $msg) {
    if ($message = $this->validateMessage($msg)) {
      $handler = new ChatBot($from, $this->clients);
//      $handler = new $message['handler']($from, $this->clients);
      $handler->handle($message->messageDetails);
    } else {
      // Send this back to the originator
      $from->send(json_encode(array(
        'status' => 'error',
        'message' => sprintf('Undeliverable message: "%s"', $msg)
      )));
    }

  }

  public function onClose(ConnectionInterface $conn) {
    // The connection is closed, remove it, as we can no longer send it messages
    $this->clients->detach($conn);

    echo "Connection {$conn->resourceId} has disconnected\n";
  }

  public function onError(ConnectionInterface $conn, \Exception $e) {
    echo "An error has occurred: {$e->getMessage()}\n";

    $conn->close();
  }

  /**
   * Check to see if a message has a valid handler, for example:
   * - a chat bot (private, public messages)
   * - the game hostess (starting new games, loading games in progress)
   * - a game's referee (making moves, updating the gamestate)
   *
   * @param $msg
   * @return boolean
   */
  public function validateMessage($msg) {
    $msg = \GuzzleHttp\json_decode($msg);
    if (in_array($msg->handler, array(
      'ChatBot',
      'GameHostess',
      'RPSReferee',
    ))) {
      return $msg;
    }

    return FALSE;
  }
}
