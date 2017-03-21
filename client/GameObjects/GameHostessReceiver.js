/**
 * Created by cmanalan on 3/4/2017.
 */


var GameHostessReceiver;

(function ($) {
  $(document).ready(function () {
    var $messages = $('.messages');
    
    /**
     * In this game, the first player to win 4 rounds wins the game.
     */
    GameHostessReceiver = {
      "say": function(msg) {
        if (msg.mode == 'public') {
          $messages.html($messages.html() + '<div>' + msg.sender + ' says: ' + msg.message + '</div>');
        } else {
          $messages.html($messages.html() + '<div>' + msg.sender + ' whispers: ' + msg.message + '</div>');
        }
      },
      "getGames": function(msg) {
        var gameIds = [];
        for (var key in msg.myGames) {
          gameIds.push(key);
        }
        if (gameIds.length > 0) {
          $messages.html($messages.html() + '<div>Your Games:<br />' + gameIds.join('<br />') + '</div>');  
        }
        

        gameIds = [];
        for (key in msg.openGames) {
          gameIds.push(key);
        }
        if (gameIds.length > 0) {
          $messages.html($messages.html() + '<div>Open Games:<br />' + gameIds.join('<br />') + '</div>');
        }
      }
    };
  });
})(jQuery);
