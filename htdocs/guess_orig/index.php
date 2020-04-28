<?php

/*
Guess my number, a POST version
*/
require __DIR__ . "/autoload.php";
require __DIR__ . "/config.php";
// require __DIR__ . "/src/Guess.php";


session_name("mySession");
session_start();



$guess = $_POST['guess']?? null;
$doInit = $_POST['doInit']?? null;
$doGuess = $_POST['doGuess']?? null;
$doCheat = $_POST['doCheat']?? null;
$number = $_SESSION['number']?? null;
$tries = $_SESSION['tries']?? null ;
$guessing = null;

if ($doInit || $number === null) {
    $guessing = new Guess();
    $_SESSION['number'] = $guessing->number();
    $_SESSION['tries'] = $guessing->tries();
} elseif ($doGuess) {
    try {
        $guessing = new Guess($number, $tries);
        $res = $guessing->makeGuess($guess);
        $_SESSION["tries"] = $guessing->tries();
    } catch (GuessException $e) {
        echo '<h3 style="color: red;"> Message: </h3>' .$e->getMessage();
    }
}

require __DIR__ . "/view/guess_my_number.php";
// require __DIR__ . "/view/debug_session_post_get_number.php";
