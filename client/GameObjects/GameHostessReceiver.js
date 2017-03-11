/**
 * Created by cmanalan on 3/4/2017.
 */


var GameHostessReceiver;

(function ($) {
  $(document).ready(function () {
    /**
     * In this game, the first player to win 4 rounds wins the game.
     */
    GameHostessReceiver = {
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
