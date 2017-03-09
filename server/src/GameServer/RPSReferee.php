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
    // no matter what we are doing, start by getting the game state
    $this->getGameState($msg->gameId);

    if ($msg->operation == "fetchGameState") {
      if ($this->gameState) {
        $this->sender->player = $msg->player;
        $this->sendGameState($msg->player);
      }
    }

    if ($msg->operation == "makeMove") {
      // validate if move is allowed

      // resolve combat?

      // update database

      // send updated game state
      $this->sendGameState($msg->player);
    }
  }

  public function getGameState($gameId) {
    if ($result = $this::queryGameState($gameId)) {
      $gameStateJSON = current($result->fetch(\PDO::FETCH_ASSOC));
      $this->gameState = \GuzzleHttp\json_decode($gameStateJSON);
      $this->sender->gameId = $gameId;
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
    foreach ($this->clients as $client) {
      // only update clients who are in the game
      if ($client->gameId == $this->sender->gameId) {
        // hide opponent's selection
        $playerGameState = unserialize(serialize($this->gameState)); // dirty stdClass clone - @see http://stackoverflow.com/questions/15945837/trying-to-clone-a-stdclass
        if ($this->gameState->player1->name == $client->player) {
          // this is player 1, so hide player 2's selection
          if ($this->gameState->currentRound->p2 != "deciding...") {
            $playerGameState->currentRound->p2 = "finished and waiting";
          }
        }
        if ($this->gameState->player2->name == $client->player) {
          // this is player 2, so hide player 1's selection
          if ($this->gameState->currentRound->p1 != "deciding...") {
            $playerGameState->currentRound->p1 = "finished and waiting";
          }
        }

        $client->send(json_encode(array(
          'from' => 'RPSReferee',
          'operation' => 'gameState',
          'content' => $playerGameState,
        )));
      }
    }
  }
}
