<?php
/**
 * Create routes for dice game using $app programming style.
 */

/**
 * Init the game redirect to play the game.
 */
$app->router->get("guess/dice/init", function () use ($app) {
    $app->session->set('game', null);
    $app->session->set("playerRoundSum", null);
    $app->session->set("total", null);
    $app->session->set("dicesInHand", null);
    $app->session->set("winner", null);

    return $app->response->redirect("guess/dice/start");
});


/**
 * Play the game. show game status
 */
$app->router->get("guess/dice/start", function () use ($app) {
    $app->page->add("guess/dice/start");
    // $app->page->add("guess/dice/debug");

    return $app->page->render([
        "title" => "Start",
    ]);
});


/**
 * Play the game. Make a guess (POST method)
 */
$app->router->post("guess/dice/start", function () use ($app) {
    $players = $app->request->getPost("playersAmount");
    $dices = $app->request->getPost("dicesAmount");

    $app->session->set("game", new Moody\Controller\Game($players, $dices));
    $game = $app->session->get("game");
    $game->processPlayersArrays();
    $app->session->set("playersHands", $game->getPlayersHands());
    $app->session->set("dicesInHand", $game->dicesInHand($reset = False));
    $app->session->set("firstPlayer", $game->firstPlayer());

    return $app->response->redirect("guess/dice/play");
});


/**
 * Play the game. show game status
 */
$app->router->get("guess/dice/play", function () use ($app) {
    $app->page->add("guess/dice/play");
    // $app->page->add("guess/dice/debug");

    return $app->page->render([
        "title" => "play",
    ]);
});


/**
 * Play the game. Make a guess (POST method)
 */
$app->router->post("guess/dice/play", function () use ($app) {
    $play = $app->request->getPost("play");
    $reset = $app->request->getPost("reset");

    $game = $app->session->get("game");

    if ($play) {
        if($game->firstPlayer() == 'Roll again') {
            $game->throwAgain();
            $app->session->set("playersHands", $game->getPlayersHands());
            $app->session->set("dicesInHand", $game->dicesInHand());
            $app->session->set("firstPlayer", $game->firstPlayer());
            return $app->response->redirect("guess/dice/play");
        } else {
            $whoWillPlay = $app->session->get('firstPlayer');
            $game->processPlayersArrays();
            $game->throwAgain();
            $app->session->set("playerHand", $game->playerHand($whoWillPlay));
            $app->session->set('saveButtonVisibility', 'visible');
            $app->session->set("dicesInHand", $game->dicesInHand());
            $app->session->set("playerRoundSum", $game->playerRoundSum($whoWillPlay));
            $app->session->set("winner", $game->winner($whoWillPlay));

            return $app->response->redirect("guess/dice/game");
        }
    } elseif ($reset) {
        return $app->response->redirect("guess/dice/init");
    }
});


/**
 * Play the game. show game status
 */
$app->router->get("guess/dice/game", function () use ($app) {
    $app->page->add("guess/dice/game");
    $game = $app->session->get("game");
    $app->session->set("firstPlayer1", $game->returnstartGame());

    return $app->page->render([
        "title" => "Game",
    ]);
});


/**
 * Play the game. show game status
 */
$app->router->post("guess/dice/game", function () use ($app) {
    $playGame = $app->request->getPost("playPlayer");
    $saveHand = $app->request->getPost("save");
    $reset = $app->request->getPost("reset");

    if ($playGame) {
        $game = $app->session->get("game");
        $app->session->set("firstPlayer", $game->returnstartGame());
        $whoWillPlay = $app->session->get('firstPlayer');
        $game->processPlayersArrays();
        $game->throwAgain();
        $app->session->set("playerHand",$game->playerHand($whoWillPlay));
        $game->dicesInHand();
        $app->session->set("playerRoundSum", $game->playerRoundSum($whoWillPlay));
        $app->session->set("winner", $game->winner($whoWillPlay));
        $app->session->set('saveButtonVisibility', $game->saveButtonVisibility('visible', $whoWillPlay));

        return $app->response->redirect("guess/dice/game");
    } elseif ($reset) {
        return $app->response->redirect("guess/dice/init");
    } elseif ($saveHand) {
        $game = $app->session->get("game");
        $whoWillPlay = $app->session->get('firstPlayer');
        $game->savePlayerResults($whoWillPlay);
        $app->session->set("total", $game->total() );
        $app->session->set("winner", $game->winner($whoWillPlay));
        $app->session->set("playerHand",$game->playerHand($whoWillPlay));
        $app->session->set('saveButtonVisibility', $game->saveButtonVisibility('save', $whoWillPlay));
        $app->session->set('playButtonVisibility', $game->playButtonVisibility());

        return $app->response->redirect("guess/dice/game");
    }

    return $app->page->render([
        "title" => "Game",
    ]);
});