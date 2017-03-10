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
    $query =  "SELECT game_state FROM games WHERE game_id='{$this->gameId}'";

    // @see http://stackoverflow.com/questions/12170785/how-to-get-first-row-of-data-in-sqlite3-using-php-pdo
    return $dbh->query($query);
  }

  public function updateGameState() {
    // fetch the game from the database and have the proper handler serve it to the clients.
    // @see http://stackoverflow.com/questions/16728265/how-do-i-connect-to-an-sqlite-database-with-php
    $dir = 'sqlite:C:\Apache24\htdocs\rps-game\server\db\game.db';
    $dbh = new \PDO($dir) or die("cannot open the database");
    $query = $dbh->prepare('UPDATE games SET game_state = ? WHERE game_id = ?');

    // @see http://stackoverflow.com/questions/12170785/how-to-get-first-row-of-data-in-sqlite3-using-php-pdo
    $query->execute(array(
      json_encode($this->gameState),
      $this->gameId,
    ));
  }

  public function inGameMessage($sender, $msg) {
    foreach ($this->clients as $client) {
      // only update clients who are in the game
      if ($client->gameId == $this->gameId) {
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