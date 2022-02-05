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
    $_SESSION["res"] = null;

    $guessing = new Moody\Guess\Guess();
    $_SESSION['number'] = $guessing->number();
    $_SESSION['tries'] = $guessing->tries();
    return $app->response->redirect("guess/game/play");
});



/**
* play the game. show game status
*/
$app->router->get("guess/game/play", function () use ($app) {
    $title = "Play the game";

    $tries = $_SESSION['tries'] ?? null;
    $res = $_SESSION['res'] ?? null;
    $guess = $_SESSION['guess'] ?? null;
    $number = $_SESSION['number'] ?? null;
    $_SESSION["res"] = null;



    $data = [
        "guess" => $guess ?? null,
        "tries" => $tries,
        "number" => $number ?? null,
        "res" => $res,
    ];
    $app->page->add("guess/game/play", $data);
    // $app->page->add("guess/debug");


    return $app->page->render([
        "title" => $title,
    ]);
});





/**
* play the game. make a guess
*/
$app->router->post("guess/game/play", function () use ($app) {


    $guess = $_POST["guess"] ?? null;
    $doGuess = $_POST["doGuess"] ?? null;
    $doInit = $_POST["doInit"] ?? null;
    $doCheat = $_POST["doCheat"] ?? null;
    $number = $_SESSION["number"] ?? null;
    $tries = $_SESSION["tries"] ?? null;
    $res = null;


    if ($doGuess) {
        $_SESSION["guess"] = $guess;
           return $app->response->redirect("guess/make-guess");
    } elseif ($doCheat) {
        $_SESSION["res"] = "Cheated number is: " . $number;
        return $app->response->redirect("guess/game/play");
    } else {
        return $app->response->redirect("guess/init");
    }
    // return $app->response->redirect("guess/play");
});





/**
* Make a guess (make-guess)
*/
$app->router->get("guess/make-guess", function () use ($app) {
    $number = $_SESSION["number"] ?? null;
    $tries = $_SESSION["tries"] ?? null;
    $guess = $_SESSION["guess"] ?? null;

    $guessing = new Moody\Guess\Guess($number, $tries);

    try {
        $res = $guessing->makeGuess($guess);
    } catch (Moody\Guess\GuessException $e) {
        $res = '<p style="color:red; font-weight: 900;">Warning: </p>' . $e->getMessage();
    } catch (TypeError $e) {
        $res = `The given number {$guess} is out of range.`;
    }


    $_SESSION["tries"] = $guessing->tries();
    $_SESSION["res"] = $res;

    if ($res == "CORRECT") {
        return $app->response->redirect("guess/game/win");
    } elseif ($_SESSION["tries"] < 1) {
        return $app->response->redirect("guess/game/fail");
    } else {
        return $app->response->redirect("guess/game/play");
    }
});



/**
 * Wining the game
 */
$app->router->get("guess/game/win", function () use ($app) {
    $title =" You won the game!";

    $data = [
        "number" => $_SESSION["number"] ?? null
    ];


    $app->page->add("guess/game/win", $data);

    return $app->page->render([
        "title" => $title,
    ]);
});


/**
 * In case of losing the game
 */
$app->router->get("guess/game/fail", function () use ($app) {
    $title =" You have lost the game!";

    $data = [
        "tries" => $_SESSION["tries"] ?? null,
        "number" => $_SESSION["number"] ?? null
    ];

    $app->page->add("guess/game/fail", $data);

    return $app->page->render([
        "title" => $title,
    ]);
});
