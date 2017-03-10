# rps-game

There is a server component and a client component to this.

## Setup
You will need composer to get some dependent packages. According to https://getcomposer.org/doc/01-basic-usage.md you head to the **/server** directory and run the following command:
```
php composer.phar install
```

## Chat (aka Hello World)
To run the server on your local, head to **/server/bin** directory and use the following command:
```
php game-server.php
```

To run the client, open a browser and load **/client/chat.html** - the quickest, dirtiest way is to open it via file:// instead of setting up Apache and serving the file

## RPS (aka Rock Paper Scissors)
This uses the same server as the Chat client.

To run the server on your local, head to **/server/bin** directory and use the following command:
```
php game-server.php
```

To run the client, open a browser and load **/client/rps.html** - however, you will not see much on the screen. That is because you need to pass a few parameters:
* gameId
* player

Try the following links:
* rps.html?gameId=fatal-bongo-showdown&player=mark
* rps.html?gameId=fatal-bongo-showdown&player=christian

As you may have guessed, you will either enter the 'fatal-bongo-showdown' game in progress as either 'mark' or 'christian'

# HAVE FUN!!!!!!!1
