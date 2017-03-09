<?php
/**
 * Created by PhpStorm.
 * User: cmanalan
 * Date: 3/9/2017
 * Time: 1:07 AM
 */

namespace GameServer;


class RPSReferee extends GameServerHandler {
  public function sendGameState($gameState, $player) {
    $this->sender->send(json_encode(array(
      'from' => 'RPSReferee',
      'operation' => 'gameState',
      'content' => array(
        'sender' => "RPSReferee",
        'mode' => 'private',
        'message' => $gameState,
      ),
    )));
  }
}
