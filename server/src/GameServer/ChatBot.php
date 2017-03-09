<?php
/**
 * Created by PhpStorm.
 * User: cmanalan
 * Date: 3/3/2017
 * Time: 11:42 PM
 */

namespace GameServer;


class ChatBot extends GameServerHandler {

  public function handle($msgDetails) {
    $is_sent = FALSE;

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
        $is_sent = TRUE;
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
        $is_sent = TRUE;
        break;
      }
    }

    // handle unknown recipient
    if (!$is_sent) {
      $this->sender->send(json_encode(array(
        'from' => 'ChatBot',
        'operation' => 'say',
        'content' => array(
          'sender' => 'ChatBot',
          'mode' => 'private',
          'message' => 'Your message was not sent because there is no user ' . $msgDetails->to,
        ),
      )));
    }
  }
}