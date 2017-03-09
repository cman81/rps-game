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
}