/**
 * Created by cmanalan on 3/4/2017.
 */


var RPSRefereeReceiver;
var gameState;

(function ($) {
  $(document).ready(function () {
    /**
     * In this game, the first player to win 4 rounds wins the game.
     */
    RPSRefereeReceiver = {
      "gameState": function(msg) {
        gameState = msg;
        console.log(msg);

        // show score
        var output = "Score (first to 4 wins):<br />"
          + gameState.player1.name + ": " + gameState.player1.score + "<br />"
          + gameState.player2.name + ": " + gameState.player2.score + "<br /><br />";
        // show the status of the current round
        output += "Current round:<br />"
          + gameState.player1.name + ": " + gameState.currentRound.p1 + "<br />"
          + gameState.player2.name + ": " + gameState.currentRound.p2 + "<br /><br />";
        $('.messages').html(output);
      },
      "say": function(msg) {
        var $messages = $('.messages');
        if (msg.mode == 'public') {
          $messages.html($messages.html() + msg.sender + ' says: ' + msg.message + '<br />');
        } else {
          $messages.html($messages.html() + msg.sender + ' whispers: ' + msg.message + '<br />');
        }
      }
    };
  });
})(jQuery);

// in this game, mark has won round 1. christian has played scissors and is awaiting mark's selection
var sampleGameState = {
  "player1": {
    "name": "mark",
    "score": 1
  },
  "player2": {
    "name": "christian",
    "score": 0
  },
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
