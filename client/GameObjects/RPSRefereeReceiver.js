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
          + gameState.player2.name + ": " + gameState.currentRound.p2;
        $('.messages').html($('.messages').html() + '<div>' + output + '</div>');
      },
      "say": function(msg) {
        var $messages = $('.messages');
        if (msg.mode == 'public') {
          $messages.html($messages.html() + '<div>' + msg.sender + ' says: ' + msg.message + '</div>');
        } else {
          $messages.html($messages.html() + '<div>' + msg.sender + ' whispers: ' + msg.message + '</div>');
        }
      }
    };
  });
})(jQuery);

// in this game, mark has won round 1. christian has played scissors and is awaiting mark's selection
var sampleGameState = {
  "isGameOver": true,
  "player1": {"name": "mark", "score": 4},
  "player2": {"name": "christian", "score": 2},
  "completedRounds": [{"p1": "rock", "p2": "scissors"}, {"p1": "rock", "p2": "rock"}, {
    "p1": "paper",
    "p2": "rock"
  }, {"p1": "paper", "p2": "scissors"}, {"p1": "paper", "p2": "scissors"}, {
    "p1": "paper",
    "p2": "rock"
  }, {"p1": "paper", "p2": "rock"}],
  "currentRound": {"p1": "deciding...", "p2": "deciding..."}
};
