<?php
/**
 * Created by PhpStorm.
 * User: cmanalan
 * Date: 3/9/2017
 * Time: 1:07 AM
 */

namespace GameServer;


class RPSReferee extends GameServerHandler {
  public function handle($msg) {
    if ($msg->operation == "getGameState") {
      // fetch the game from the database and have the proper handler serve it to the clients.
      // @see http://stackoverflow.com/questions/16728265/how-do-i-connect-to-an-sqlite-database-with-php
      $dir = 'sqlite:C:\Apache24\htdocs\rps-game\server\db\game.db';
      $dbh  = new \PDO($dir) or die("cannot open the database");
      $query =  "SELECT game_state FROM games WHERE game_id='{$msg->gameId}'";

      // @see http://stackoverflow.com/questions/12170785/how-to-get-first-row-of-data-in-sqlite3-using-php-pdo
      if ($result = $dbh->query($query)) {
        $gameStateJSON = current($result->fetch(\PDO::FETCH_ASSOC));
        $this->sendGameState(\GuzzleHttp\json_decode($gameStateJSON), $msg->player);
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
  }

  public function sendGameState($gameState, $player) {
    $this->sender->send(json_encode(array(
      'from' => 'RPSReferee',
      'operation' => 'gameState',
      'content' => $gameState,
    )));
  }
}
