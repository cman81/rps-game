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
      if (
        $this->gameState->isGameOver
        || in_array($this->getCurrentMove($msg->player), array('rock', 'paper', 'scissors'))
      ) {
        $this->sender->send(json_encode(array(
          'from' => 'RPSReferee',
          'operation' => 'say',
          'content' => array(
            'sender' => "RPSReferee",
            'mode' => 'private',
            'message' => "Sorry, but you are not allowed to make that move.",
          ),
        )));

        return;
      }
      if ($this->gameState->player1->name == $msg->player) {
        $this->gameState->currentRound->p1 = $msg->move;
      }
      if ($this->gameState->player2->name == $msg->player) {
        $this->gameState->currentRound->p2 = $msg->move;
      }

      // resolve combat?
      if (
        ($this->gameState->currentRound->p1 == 'rock' && $this->gameState->currentRound->p2 == 'scissors')
        || ($this->gameState->currentRound->p1 == 'scissors' && $this->gameState->currentRound->p2 == 'paper')
        || ($this->gameState->currentRound->p1 == 'paper' && $this->gameState->currentRound->p2 == 'rock')
      ) {
        // player 1 wins
        $this->gameState->player1->score++;
      }
      if (
        ($this->gameState->currentRound->p2 == 'rock' && $this->gameState->currentRound->p1 == 'scissors')
        || ($this->gameState->currentRound->p2 == 'scissors' && $this->gameState->currentRound->p1 == 'paper')
        || ($this->gameState->currentRound->p2 == 'paper' && $this->gameState->currentRound->p1 == 'rock')
      ) {
        // player 2 wins
        $this->gameState->player2->score++;

      }

      // log combat results
      if ($this->gameState->currentRound->p1 != "deciding..." && $this->gameState->currentRound->p2 != "deciding...") {
        $this->gameState->completedRounds[] = (object) array(
          'p1' => $this->gameState->currentRound->p1,
          'p2' => $this->gameState->currentRound->p2,
        );
        $this->gameState->currentRound->p1 = "deciding...";
        $this->gameState->currentRound->p2 = "deciding...";
        if (
          $this->gameState->player1->score == 4
          || $this->gameState->player2->score == 4
        ) {
          $this->gameState->isGameOver = TRUE;
        }
      }

      // update database
      $this->updateGameState($msg->gameId, $this->gameState);

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

  public function getCurrentMove($player) {
    if ($this->gameState->player1->name == $player) {
      return $this->gameState->currentRound->p1;
    }
    if ($this->gameState->player2->name == $player) {
      return $this->gameState->currentRound->p2;
    }
    return FALSE;

  }
}
