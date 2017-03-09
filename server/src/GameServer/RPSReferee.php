<?php
/**
 * Created by PhpStorm.
 * User: cmanalan
 * Date: 3/9/2017
 * Time: 1:07 AM
 */

namespace GameServer;


class RPSReferee extends GameServerHandler {
  public $gameState;

  public function handle($msg) {
    $this->getGameState($msg->gameId);

    if ($msg->operation == "fetchGameState") {
      if ($this->gameState) {
        $this->sendGameState($msg->player);
      }
    }

    if ($msg->operation == "makeMove") {
      $this->sendGameState($msg->player);

      $this->sender->send(json_encode(array(
        'from' => 'RPSReferee',
        'operation' => 'say',
        'content' => array(
          'sender' => "RPSReferee",
          'mode' => 'private',
          'message' => "A move has been made.",
        ),
      )));
    }
  }

  public function getGameState($gameId) {
    if ($result = $this::queryGameState($gameId)) {
      $gameStateJSON = current($result->fetch(\PDO::FETCH_ASSOC));
      $this->gameState = \GuzzleHttp\json_decode($gameStateJSON);
    } else {
      $this->sender->send(json_encode(array(
        'from' => 'RPSReferee',
        'operation' => 'say',
        'content' => array(
          'sender' => "RPSReferee",
          'mode' => 'private',
          'message' => "Sorry, but I couldn't find your game.",
        ),
      )));
    }

  }

  public function sendGameState($player) {
    if ($this->gameState->player1->name == $player) {
      if (!empty($this->gameState->currentRound->p2)) {
        $this->gameState->currentRound->p2 = "finished and waiting";
      }
    }
    if ($this->gameState->player2->name == $player) {
      if (!empty($this->gameState->currentRound->p1)) {
        $this->gameState->currentRound->p1 = "finished and waiting";
      }
    }

    $this->sender->send(json_encode(array(
      'from' => 'RPSReferee',
      'operation' => 'gameState',
      'content' => $this->gameState,
    )));
  }
}
