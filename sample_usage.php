#!/usr/bin/php
<?php
include('Trello.php');

$diTrelloKeys = array(
  "key" => "0d9...f88",
  "token"  => "2b5...40e"
);

$trello = new Trello("config.ini",$diTrelloKeys);

/**
 * Show your config
 */
$trello->showConfig();

/**
 * Ok, let's say you want to get all the boards that you are connected to.
 * Call the 'getBoards()' method:
 */
$response = $trello->getBoards();
if($response) echo count($response) . " boards found\n";

/**
 * So, let's pretend you want to create a new card.
 * You need to know the list ID that you plan on putting the card.
 * You also need to have in mind at least the name, description of the card to
 * make it useful.
 *
 * To do this, pass an array of options to the 'createCard()' method.
 */
$response = $trello->createCard(array("idList" => "5b8...a27","name" => "This is another test!","desc" => "This ticket was created by the Trello Class"));

/**
 * All of the responses are converted to PHP objects so you can access them
 * normally.
 * This is the ID of the created card:
 */
$originalCardId = $response->id . "\n";

/**
 * If you pass any legal property the the Trello API recognizes to any public
 * method it will get passed to Trello.
 * Illegal properties don't get passed to Trello.
 * Options get automatically properly encoded. This is good for properties that
 * might have spaces or other non-legal characters on the Internet.
 *
 * Here, we create the card's vital data in a non-anonymous array then pass it
 * to the method.
 *
 * We also try to pass some bogus properties that are not accepted by Trello:
 */
$myNewCard = array(
  "idList"    => "5b8...a27",
  "name"      => "Here's another card",
  "desc"      => "This is an interesting article. Please read it and move it forward.",
  "due"       => "06/18/2020",
  "urlSource" => "https://medium.com/s/story/how-i-fully-quit-google-and-you-can-too-4c2f3f85793a",
  "clown"     => "Bozo"
);
$response = $trello->createCard($myNewCard);

/**
 * Now, let's modify that newly-created card. Let's move it to another list
 * supplied by giving the target list ID.
$response = $trello->updateCard(array("id" => $originalCardId,"idList" => "59f...326"));
echo $response->id;
 */

/**
 * We can search for Trello cards too. Just pass a string to the 'query' field.
 * You might also specify the specific field that you want to search in.
 *
 * Here, we look for the number '2540' and the card must not be archived (is:open).
 * By default the API checks open and archived cards:
 */
$response = $trello->search(array("query" => "2561 is:open"));
// $response = $trello->search(array("query" => "2561 is:archived"));

#var_dump($response);

/**
 * Look in the 'cards' property of the returned object to see if you found anything.
 */
if($response && property_exists($response,"cards")) {
  if(count($response->cards)) {
    echo "I found a card\n";
  } else {
    echo "No card found\n";
  }
} else {
  echo "No card found\n";
}

/**
 * Done!
 */
