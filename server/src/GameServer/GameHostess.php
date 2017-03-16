<?php
/**
 * Created by PhpStorm.
 * User: cmanalan
 * Date: 3/4/2017
 * Time: 2:14 AM
 */

namespace GameServer;


class GameHostess extends GameServerHandler {
  public function handle($msg) {
    if ($msg->operation == "quickStartGame") {
      // try to find a game with an empty seat and load it. otherwise, start a new game.
    }

    if ($msg->operation == "login") {
      $dir = 'sqlite:C:\Apache24\htdocs\rps-game\server\db\game.db';
      $dbh = new \PDO($dir) or die("cannot open the database");
      $query = $dbh->prepare('SELECT username FROM users WHERE username = ? AND password = ?');

      $query->execute(array(
        $msg->username,
        $msg->password,
      ));
      $rows = count($query->fetchAll());
      if ($rows == 1) {
        $this->sender->name = $msg->username;
        $this->sender->send(json_encode(array(
          'from' => 'GameHostess',
          'operation' => 'say',
          'content' => array(
            'sender' => "GameHostess",
            'mode' => 'private',
            'message' => "Welcome! You are logged in as '{$msg->username}'",
          ),
        )));

        $this->sender->send(json_encode(array(
          'from' => 'GameHostess',
          'operation' => 'getGames',
          'content' => array(
            'myGames' => $this->getMyGames(),
            'openGames' => $this->getOpenGames(),
          ),
        )));
      } else {
        $this->sender->send(json_encode(array(
          'from' => 'GameHostess',
          'operation' => 'say',
          'content' => array(
            'sender' => "GameHostess",
            'mode' => 'private',
            'message' => "Login failed.",
          ),
        )));
      }
    }
  }

  public function getMyGames() {
    $dir = 'sqlite:C:\Apache24\htdocs\rps-game\server\db\game.db';
    $dbh = new \PDO($dir) or die("cannot open the database");
    $query = $dbh->prepare('SELECT * FROM games');

    $query->execute(array());
    /**
     * "To return an associative array grouped by the values of a specified
     * column, bitwise-OR PDO::FETCH_COLUMN with PDO::FETCH_GROUP"
     *
     * @see http://php.net/manual/en/pdostatement.fetchall.php
     */
    $results = $query->fetchAll();
    $games = array();
    foreach ($results as $value) {
      $gameObj = \GuzzleHttp\json_decode($value['game_state']);
      if (!$gameObj->isGameOver) {
        if (
          $gameObj->player1->name == $this->sender->name
          || $gameObj->player2->name == $this->sender->name
        ) {
          $games[$value['game_id']] = $gameObj;
          continue;
        }
      }
    }
    return $games;
  }

  public function getOpenGames() {
    $dir = 'sqlite:C:\Apache24\htdocs\rps-game\server\db\game.db';
    $dbh = new \PDO($dir) or die("cannot open the database");
    $query = $dbh->prepare('SELECT * FROM games');

    $query->execute(array());
    $results = $query->fetchAll();
    $games = array();
    foreach ($results as $value) {
      $gameObj = \GuzzleHttp\json_decode($value['game_state']);
      if (!$gameObj->isGameOver) {
        if (
          $gameObj->player1->name == FALSE
          || $gameObj->player2->name == FALSE
        ) {
          $games[$value['game_id']] = $gameObj;
          continue;
        }
      }
    }
    return $games;
  }
}