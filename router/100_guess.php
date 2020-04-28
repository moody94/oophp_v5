<?php
/**
 * Create routes using $app programming style.
 */
//var_dump(array_keys(get_defined_vars()));



/**
 *init  the game.
 */
$app->router->get("guess/init", function () use ($app) {
    // init the session fot the gamestart.


    $guessing = new Moody\Guess\Guess();
    $_SESSION['number'] = $guessing->number();
    $_SESSION['tries'] = $guessing->tries();
    return $app->response->redirect("guess/play");
});



/**
 * play the game. show game status
 */
$app->router->get("guess/play", function () use ($app) {
    $title = "Play the game";

    $tries = $_SESSION['tries'] ?? null;
    $res = $_SESSION['res'] ?? null;
    $_SESSION['res'] = null;
    $guess = $_SESSION['guess'] ?? null;
    $_SESSION['guess'] = null;


    $data = [
        "guess" => $guess ?? null,
        "tries" => $tries,
        "number" => $number ?? null,
        "res" => $res,
        "doGuess" => $doGuess ?? null,
        "doCheat" => $doCheat ?? null
    ];
    $app->page->add("guess/play", $data);
    $app->page->add("guess/debug");


    return $app->page->render([
        "title" => $title,
    ]);
});




/**
 * play the game. make a guess
 */
$app->router->post("guess/play", function () use ($app) {


    $guess = $_POST['guess']?? null;

    $doGuess = $_POST['doGuess']?? null;


    $number = $_SESSION['number']?? null;
    $tries = $_SESSION['tries']?? null ;



    if ($doGuess) {
        try {
            $guessing = new Moody\Guess\Guess($number, $tries);
            $res = $guessing->makeGuess($guess);
            $_SESSION["tries"] = $guessing->tries();
            $_SESSION["res"] = $res;
            $_SESSION["guess"] = $guess;
        } catch (GuessException $e) {
            echo '<h3 style="color: red;"> Message: </h3>' .$e->getMessage();
        }
    }


    return $app->response->redirect("guess/play");
});
