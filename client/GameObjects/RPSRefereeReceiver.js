/**
 * Created by cmanalan on 3/4/2017.
 */

/**
 * In this game, the first player to win 4 rounds wins the game.
 */
var RPSRefereeReceiver = {

};

// in this game, mark has won round 1. christian has played scissors and is awaiting mark's selection
var sampleGameState = {
  "player1": "mark",
  "player2": "christian",
  "completedRounds": [
    {
      "p1": "rock",
      "p2": "scissors"
    }
  ],
  "currentRound": {
    "p1": "",
    "p2": "scissors"
  }
};
