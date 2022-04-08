<?php

namespace Moody\Controller;

use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    public int $playersNumber = 2;
    public int $dicesNumber = 2;
    protected $game;

    /**
     * Construct object and verify that the object has the expected
     * properties. Use only first argument.
     */
    protected function setUp()
    {
        $this->game = new Game($this->playersNumber, $this->dicesNumber);
    }

    protected function tearDown()
    {
        $this->game = null;
    }

    public function testGameClassInit()
    {
        $this->assertInstanceOf("\Moody\Controller\Game", $this->game);
    }

    public function testdiceBeforeThowingReturnString()
    {
        $this->game->arrPlayers();
        $res = $this->game->diceBeforeThowing();

        $this->assertIsString($res);
    }

    public function testanotherThrow()
    {
        $this->game->arrPlayers();
        $res = $this->game->diceBeforeThowing();
        $this->game->anotherThrow();
        $res1 = $this->game->diceBeforeThowing();

        $this->assertNotEquals($res, $res1);
    }

    public function testdicesInHand()
    {
        $this->game->arrPlayers();
        $res = $this->game->dicesInHand();

        $this->assertIsString($res);
    }

    public function testplayer1()
    {
        $this->game->arrPlayers();
        $playersHandsSum = $this->game->dicesInHand();
        $player1 = $this->game->player1();

        $res = explode(', ', $playersHandsSum);

        if ($res[0] > $res[1]) {
            $this->assertEquals(1, $player1);
        } else if ($res[1] > $res[0]) {
            $this->assertEquals(2, $player1);
        } else if ($res[0] === $res[1]) {
            $this->assertEquals('Roll again', $player1);
        }
    }

    public function testPlayButtonVisibilityIsVisible()
    {
        $this->game->arrPlayers();
        $this->game->dicesInHand();
        $this->game->playerRoundSum(1);
        $this->game->res1(1);

        $res = $this->game->playButtonVisibility();
        $this->assertEquals('visible', $res);
    }

    public function testNoWinner()
    {
        $this->game->arrPlayers();
        $this->game->dicesInHand();
        $this->game->playerRoundSum(1);
        $this->game->res1(1);

        $noWinner = $this->game->winner(1);

        $this->assertEquals('No winner yet!', $noWinner);
    }

    public function testnextPlayer()
    {
        $res = $this->game->nextPlayer(2);

        $this->assertEquals(1, $res);

        $res = $this->game->nextPlayer(1);

        $this->assertEquals(2, $res);

        $playerOutOfBoundary = 3;
        $res = $this->game->nextPlayer($playerOutOfBoundary);

        $this->assertFalse($res);
    }


    public function testPlayerHand()
    {
        $process = $this->game->arrPlayers();
        $res = $this->game->playerHand(1);

        $this->assertIsString($res);

        $toString = implode(', ', $process[0]);

        $this->assertEquals($toString, $res);
    }

    public function teststartGame()
    {
        $this->game->arrPlayers();
        $this->game->dicesInHand();
        $player1 = $this->game->player1();
        $res = $this->game->returnstartGame();

        if ($player1 == 'Roll again') {
            $this->assertIsInt($res);
        } else {
            $this->assertGreaterThan(0, $res);
        }
    }
}
