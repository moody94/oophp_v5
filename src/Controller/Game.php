<?php

namespace Moody\Controller;

class Game
{

    private $players = [];
    private $playersValues = [];
    private $dicesInHand = [];
    private $sum1 = [];
    private $total = [];
    private $startGame = 0;


    public function __construct(int $playersNumber, int $dicesAmount)
    {
        for ($i = 0; $i < $playersNumber; $i++) {
            array_push($this->players, new DiceHand($dicesAmount));
        }
    }

    public function arrPlayers()
    {
        $playersNumber = count($this->players);
        for ($i = 0; $i < $playersNumber; $i++) {
            $diceHand = $this->players[$i];
            $diceHand->setValues();

            $dices = sizeof($diceHand->getValues());

            $playerArray = [];
            $playerArray[$i] = [];
            for ($j = 0; $j < $dices; $j++) {
                array_push($playerArray[$i], $diceHand->getValues()[$j]);
            }
            array_push($this->playersValues, $playerArray[$i]);
        }
        return $this->playersValues;
    }

    public function anotherThrow()
    {
        $playersNumber = count($this->players);
        $this->playersValues = [];
        $this->dicesInHand = [];

        for ($i = 0; $i < $playersNumber; $i++) {
            $this->players[$i]->changeValuesArray();
            $this->players[$i]->rollHand();
            $this->players[$i]->resetHandScore();

            $diceHand = $this->players[$i];
            $diceHand->setValues();

            $dices = sizeof($diceHand->getValues());

            $playerArray = [];
            $playerArray[$i] = [];
            for ($j = 0; $j < $dices; $j++) {
                array_push($playerArray[$i], $diceHand->getValues()[$j]);
            }
            array_push($this->playersValues, $playerArray[$i]);
        }
        return $this->playersValues;
    }

    public function diceBeforeThowing()
    {
        $count = sizeof($this->playersValues);
        $values = '';
        for ($i = 0; $i < $count; $i++) {
            $values .= "Player's " . ($i + 1) . ' hand dices: ' . implode(', ', $this->playersValues[$i]) . '/  ';
        }
        return $values;
    }

    public function dicesInHand()
    {
        $playersNumber = count($this->players);
        $this->dicesInHand = [];
        for ($i = 0; $i < $playersNumber; $i++) {
            array_push($this->dicesInHand, $this->players[$i]->sum());
        }
        $dicesInHand = implode(', ', $this->dicesInHand);
        return $dicesInHand;
    }

    public function player1()
    {
        $max = max($this->dicesInHand);
        $itemsInPlayerSum = count($this->dicesInHand);

        $rep = 0;
        for ($i = 0; $i < $itemsInPlayerSum; $i++) {
            if ($max) {
                if ($max == $this->dicesInHand[$i]) {
                    $rep++;
                }
            }
        }
        $startGame = array_search($max, $this->dicesInHand) + 1;
        if ($rep > 1) {
            return 'Roll again';
        }
        $this->startGame = $startGame;
        return $this->startGame;
    }

    public function hand1(int $player)
    {
        if (in_array(1, $this->playersValues[$player - 1])) {
            return true;
        };
        return false;
    }

    public function playerHand(int $player)
    {
        $playerHandValuesArr = $this->playersValues[$player - 1];
        $playerHandValues = implode(', ', $playerHandValuesArr);
        return $playerHandValues;
    }

    public function nextPlayer(int $player)
    {
        $playersAmount = count($this->players);

        if ($player <= $playersAmount && $player > 0) {
            if ($player === $playersAmount) {
                $this->startGame = 1;
                return $this->startGame;
            } else {
                return $this->startGame = $player + 1;
            }
        } else {
            return false;
        }
    }

    public function returnstartGame()
    {
        return $this->startGame;
    }

    public function playerRoundSum(int $player)
    {
        if (array_key_exists($player - 1, $this->sum1)) {
            $roundSum = $this->sum1[$player - 1];
        } else {
            $roundSum = 0;
        }

        if ($this->hand1($player) === true) {
            $this->sum1[$player - 1] = 0;
            $this->dicesInHand[$player - 1] = 0;
            $this->nextPlayer($player);
            return $this->sum1[$player - 1];
        } else if ($this->hand1($player) === false) {
            $roundSum += $this->dicesInHand[$player - 1];
            $this->sum1[$player - 1] = $roundSum;
            if ($this->sum1) {
                if ($this->sum1[$player - 1] < 100) {
                    return $this->sum1[$player - 1];
                }
                return 'bigger than 100';
            }
        }
    }

    public function res1(int $player)
    {
        if (array_key_exists($player - 1, $this->total)) {
            $this->total[$player - 1] += $this->sum1[$player - 1];
        } else {
            $this->total[$player - 1] = $this->sum1[$player - 1];
        }
        return $this->nextPlayer($player);
    }

    public function total()
    {
        $keys = array_keys($this->total);
        $arrayLength = count($keys);

        $res = '';
        for ($i = 0; $i < $arrayLength; $i++) {
            $res .= 'Player ' .  ($keys[$i] + 1) . "'s score is: " . $this->total[$keys[$i]]
                . ' <br>';
        }
        return $res;
    }

    public function winner(int $player)
    {
        $totalCount = count($this->total);
        if ($totalCount > 0) {
            if (array_key_exists($player - 1, $this->total)) {
                if ($this->total[$player - 1] < 100) {
                    return 'No winner yet!';
                }
                return 'Player ' . $player . ' wins! :)';
            }
        }
        return 'No winner yet!';
    }

    public function saveButtonVisibility(string $case, int $player)
    {
        if ($case == 'save') {
            if (array_key_exists($player - 1, $this->sum1)) {
                $this->sum1[$player - 1] = 0;
            }

            if ($this->hand1($player) === true) {
                return 'none';
            }
            return 'none';
        } else if ($case == 'visible') {
            if ($this->hand1($player) === true) {
                return 'none';
            }
            return 'visible';
        }
    }

    public function playButtonVisibility()
    {
        if (max($this->total) >= 100) {
            return 'none';
        }
        return 'visible';
    }
}
