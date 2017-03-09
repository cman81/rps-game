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

  /**
   * GameServerHandler constructor.
   * @param $sender
   * @param $clients
   */
  public function __construct($sender, $clients) {
    $this->sender = $sender;
    $this->clients = $clients;
  }

  public static function queryGameState($gameId) {
    // fetch the game from the database and have the proper handler serve it to the clients.
    // @see http://stackoverflow.com/questions/16728265/how-do-i-connect-to-an-sqlite-database-with-php
    $dir = 'sqlite:C:\Apache24\htdocs\rps-game\server\db\game.db';
    $dbh  = new \PDO($dir) or die("cannot open the database");
    $query =  "SELECT game_state FROM games WHERE game_id='{$gameId}'";

    // @see http://stackoverflow.com/questions/12170785/how-to-get-first-row-of-data-in-sqlite3-using-php-pdo
    return $dbh->query($query);
  }
}