/**
 * Created by cmanalan on 3/4/2017.
 */

var ChatBotReceiver;

(function ($) {
  $(document).ready(function() {
    ChatBotReceiver = {
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
