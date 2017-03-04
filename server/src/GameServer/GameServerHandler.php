<?php
/**
 * Created by PhpStorm.
 * User: cmanalan
 * Date: 3/3/2017
 * Time: 11:43 PM
 */

namespace GameServer;


interface GameServerHandler {
  public function handle($msgDetails);
}