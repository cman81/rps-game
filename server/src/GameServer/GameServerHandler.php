<?php
/**
 * Created by PhpStorm.
 * User: cmanalan
 * Date: 3/3/2017
 * Time: 11:43 PM
 */

namespace GameServer;


abstract class GameServerHandler {
  protected $sender;
  protected $clients;
  public $gameId;
  public $gameState;


  /**
   * GameServerHandler constructor.
   * @param $sender
   * @param $clients
   */
  public function __construct($sender, $clients) {
    $this->sender = $sender;
    $this->clients = $clients;
  }

  public function queryGameState() {
    // fetch the game from the database and have the proper handler serve it to the clients.
    // @see http://stackoverflow.com/questions/16728265/how-do-i-connect-to-an-sqlite-database-with-php
    $dir = 'sqlite:C:\Apache24\htdocs\rps-game\server\db\game.db';
    $dbh  = new \PDO($dir) or die("cannot open the database");
    $query =  "SELECT game_state FROM games WHERE game_id='{$this->sender->gameId}'";

    // @see http://stackoverflow.com/questions/12170785/how-to-get-first-row-of-data-in-sqlite3-using-php-pdo
    $result = $dbh->query($query);
    $gameStateJSON = $result->fetch(\PDO::FETCH_ASSOC);
    if (!$gameStateJSON) {
      return FALSE;
    }
    $this->gameState = \GuzzleHttp\json_decode(current($gameStateJSON));
    return TRUE;
  }

  public function updateGameState($is_new = FALSE) {
    // fetch the game from the database and have the proper handler serve it to the clients.
    // @see http://stackoverflow.com/questions/16728265/how-do-i-connect-to-an-sqlite-database-with-php
    $dir = 'sqlite:C:\Apache24\htdocs\rps-game\server\db\game.db';
    $dbh = new \PDO($dir) or die("cannot open the database");
    if ($is_new) {
      $query = $dbh->prepare('INSERT INTO games (game_state, game_id) VALUES (?, ?)');
    } else {
      $query = $dbh->prepare('UPDATE games SET game_state = ? WHERE game_id = ?');
    }

    // @see http://stackoverflow.com/questions/12170785/how-to-get-first-row-of-data-in-sqlite3-using-php-pdo
    $query->execute(array(
      json_encode($this->gameState),
      $this->sender->gameId,
    ));
  }

  public function inGameMessage($sender, $msg) {
    foreach ($this->clients as $client) {
      // only update clients who are in the game
      if ($client->gameId == $this->sender->gameId) {
        $client->send(json_encode(array(
          'from' => $sender,
          'operation' => 'say',
          'content' => array(
            'sender' => $sender,
            'mode' => 'public',
            'message' => $msg,
          ),
        )));
      }
    }
  }
}