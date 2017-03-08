<?php
/**
 * Created by PhpStorm.
 * User: cmanalan
 * Date: 3/4/2017
 * Time: 2:14 AM
 */

namespace GameServer;


class GameHostess implements GameServerHandler {
  public function handle($msg) {
    if ($msg->operation == "quickStartGame") {
      // try to find a game with an empty seat and load it. otherwise, start a new game.
    }

    if ($msg->operation == "loadGame") {
      // fetch the game from the database and have the proper handler serve it to the clients.
    }
  }
}