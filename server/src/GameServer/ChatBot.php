<?php
/**
 * Created by PhpStorm.
 * User: cmanalan
 * Date: 3/3/2017
 * Time: 11:42 PM
 */

namespace GameServer;


class ChatBot implements GameServerHandler {
  private $sender;
  private $clients;
  private $receiver;

  /**
   * ChatBot constructor.
   * @param $sender
   */
  public function __construct($sender, $clients) {
    $this->sender = $sender;
    $this->clients = $clients;
  }


  public function handle($msgDetails) {
    foreach ($this->clients as $client) {
      // handle public message
      if ($msgDetails->to == 'all' && $client !== $this->sender) {
        $client->send(json_encode(array(
          'from' => 'ChatBot',
          'operation' => 'say',
          'content' => array(
            'sender' => $this->sender->resourceId,
            'mode' => 'public',
            'message' => $msgDetails->message,
          ),
        )));
        continue;
      }

      // handle private message
      if ($msgDetails->to == $client->resourceId) {
        $client->send(json_encode(array(
          'from' => 'ChatBot',
          'operation' => 'say',
          'content' => array(
            'sender' => $this->sender->resourceId,
            'mode' => 'private',
            'message' => $msgDetails->message,
          ),
        )));
        break;
      }
    }
  }
}