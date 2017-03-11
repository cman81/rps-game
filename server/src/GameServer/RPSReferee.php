<?php
/**
 * Created by PhpStorm.
 * User: cmanalan
 * Date: 3/9/2017
 * Time: 1:07 AM
 */

namespace GameServer;
use Nubs\RandomNameGenerator;

class RPSReferee extends GameServerHandler {

  public function handle($msg) {
    if (!$this->sender->name) {
      $this->sender->send(json_encode(array(
        'from' => 'RPSReferee',
        'operation' => 'say',
        'content' => array(
          'sender' => "RPSReferee",
          'mode' => 'private',
          'message' => "You need to login first.",
        ),
      )));

      return;
    }

    if ($msg->operation == "newGame") {
      $generator = RandomNameGenerator\All::create();
      $this->sender->gameId = str_replace(' ', '-', strtolower($generator->getName()));
      $this->gameState = (object) array(
        "isGameOver" => FALSE,
        "player1" => array(
          "name" => $this->sender->name,
          "score" => 0
        ),
        "player2" => array(
          "name" => FALSE,
          "score" => 0
        ),
        "completedRounds" => array(),
        "currentRound" => array(
          "p1" => "deciding...",
          "p2" => "deciding...",
        ),
      );
      // update database
      $this->updateGameState(TRUE);
      $this->sender->send(json_encode(array(
        'from' => 'RPSReferee',
        'operation' => 'say',
        'content' => array(
          'sender' => "RPSReferee",
          'mode' => 'private',
          'message' => "You a new game called '{$this->sender->gameId}'",
        ),
      )));
    }

    if ($msg->operation == "continueGame") {
      $this->sender->gameId = $msg->gameId;
      if (!$this->getGameState()) {
        return;
      }
      $this->sendGameState();
    }

    if (!$this->getGameState()) {
      return;
    }


    if ($msg->operation == "makeMove") {
      // validate if move is allowed
      if (
        $this->gameState->isGameOver
        || in_array($this->getCurrentMove($this->sender->name), array('rock', 'paper', 'scissors'))
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

      if (
        $this->gameState->player1->name == $this->sender->name
        || $this->gameState->player2->name == $this->sender->name
      ) {
        $this->sender->send(json_encode(array(
          'from' => 'RPSReferee',
          'operation' => 'say',
          'content' => array(
            'sender' => "RPSReferee",
            'mode' => 'private',
            'message' => "You chose {$msg->move}.",
          ),
        )));

        if ($this->gameState->player1->name == $this->sender->name) {
          $this->gameState->currentRound->p1 = $msg->move;
        } else {
          $this->gameState->currentRound->p2 = $msg->move;
        }
      }

      // resolve combat?
      if ($this->gameState->currentRound->p1 == $this->gameState->currentRound->p2) {
        $this->inGameMessage("RPSReferee", "{$this->gameState->currentRound->p1} vs {$this->gameState->currentRound->p1}. Tie game!");
      }
      if (
        ($this->gameState->currentRound->p1 == 'rock' && $this->gameState->currentRound->p2 == 'scissors')
        || ($this->gameState->currentRound->p1 == 'scissors' && $this->gameState->currentRound->p2 == 'paper')
        || ($this->gameState->currentRound->p1 == 'paper' && $this->gameState->currentRound->p2 == 'rock')
      ) {
        // player 1 wins
        $this->inGameMessage("RPSReferee", "{$this->gameState->currentRound->p1} beats {$this->gameState->currentRound->p2}. {$this->gameState->player1->name} wins!");
        $this->gameState->player1->score++;
      }
      if (
        ($this->gameState->currentRound->p2 == 'rock' && $this->gameState->currentRound->p1 == 'scissors')
        || ($this->gameState->currentRound->p2 == 'scissors' && $this->gameState->currentRound->p1 == 'paper')
        || ($this->gameState->currentRound->p2 == 'paper' && $this->gameState->currentRound->p1 == 'rock')
      ) {
        // player 2 wins
        $this->inGameMessage("RPSReferee", "{$this->gameState->currentRound->p2} beats {$this->gameState->currentRound->p1}. {$this->gameState->player2->name} wins!");
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

        // is it game over?
        if (
          $this->gameState->player1->score == 4
          || $this->gameState->player2->score == 4
        ) {
          if ($this->gameState->player1->score == 4) {
            $this->inGameMessage("RPSReferee", "{$this->gameState->player1->name} wins the match! gg");
          } else {
            $this->inGameMessage("RPSReferee", "{$this->gameState->player2->name} wins the match! gg");
          }

          $this->gameState->isGameOver = TRUE;
        }
      }

      // update database
      $this->updateGameState();

      // send updated game state
      $this->sendGameState();
    }

    if ($msg->operation == "joinGame") {
      if ($this->gameState->player1->name == $this->sender->name) {
        $this->sender->send(json_encode(array(
          'from' => 'RPSReferee',
          'operation' => 'say',
          'content' => array(
            'sender' => "RPSReferee",
            'mode' => 'private',
            'message' => "Sorry, but you can't play yourself!",
          ),
        )));

        return;
      }

      if ($this->gameState->player2->name !== FALSE) {
        $this->sender->send(json_encode(array(
          'from' => 'RPSReferee',
          'operation' => 'say',
          'content' => array(
            'sender' => "RPSReferee",
            'mode' => 'private',
            'message' => "Sorry, but there are no empty seats at the table.",
          ),
        )));

        return;
      }

      $this->gameState->player2->name = $this->sender->name;
      $this->inGameMessage('RPSReferee', "{$this->sender->name} has joined the game!");
      $this->updateGameState();
      $this->sendGameState();
    }

  }

  public function getGameState() {
    if (!$this->queryGameState()) {
      $this->sender->send(json_encode(array(
        'from' => 'RPSReferee',
        'operation' => 'say',
        'content' => array(
          'sender' => "RPSReferee",
          'mode' => 'private',
          'message' => "Sorry, but I couldn't find your game.",
        ),
      )));

      return FALSE;
    }

    return TRUE;
  }

  public function sendGameState() {
    foreach ($this->clients as $client) {
      // only update clients who are in the game
      if ($client->gameId == $this->sender->gameId) {
        // hide opponent's selection
        $playerGameState = unserialize(serialize($this->gameState)); // dirty stdClass clone - @see http://stackoverflow.com/questions/15945837/trying-to-clone-a-stdclass
        if ($this->gameState->player1->name == $client->name) {
          // this is player 1, so hide player 2's selection
          if ($this->gameState->currentRound->p2 != "deciding...") {
            $playerGameState->currentRound->p2 = "finished and waiting";
          }
        }
        if ($this->gameState->player2->name == $client->name) {
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
