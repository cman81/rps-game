/**
 * Created by cmanalan on 3/4/2017.
 */

var ChatBotReceiver;
(function ($) {
  $(document).ready(function() {
    var $messages = $('.messages');
    ChatBotReceiver = {
      "say": function(msg) {
        if (msg.mode == 'public') {
          $messages.html($messages.html() + msg.sender + ': ' + msg.message + '<br />');
        } else {
          $messages.html($messages.html() + msg.sender + ' (privately): ' + msg.message + '<br />');
        }
      }
    };
  });
})(jQuery);
