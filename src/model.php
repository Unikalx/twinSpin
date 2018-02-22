<?php
require_once "logger.php";
require_once "sql.php";
require_once "moneyManager.php";

class Model
{
    private $weightsPrice;
    private $weights;
    private $linkedPercent;
    private $weightsPercent;
    private $bigWinPercent;
    private $megaWinPercent;
    private $superWinPercent;
    private $bigWin;
    private $megaWin;
//    private $superWin;
//    private $finishWin;

    public $log;
    public $sql;
    public $moneyManager;
    public $linkedRand;

    function __construct()
    {
        $this->weightsPrice = [
            'SYM1' => 0, //wild
            'SYM3' => [5 => 1000, 4 => 250, 3 => 50],//diamond
            'SYM4' => [5 => 500, 4 => 150, 3 => 30],//7
            'SYM5' => [5 => 400, 4 => 100, 3 => 15],//bar
            'SYM6' => [5 => 250, 4 => 75, 3 => 10],//bel
            'SYM7' => [5 => 250, 4 => 75, 3 => 10],//cherry
            'SYM8' => [5 => 40, 4 => 15, 3 => 4],//a
            'SYM9' => [5 => 40, 4 => 15, 3 => 4],//k
            'SYM10' => [5 => 40, 4 => 15, 3 => 4],//q
            'SYM11' => [5 => 25, 4 => 10, 3 => 3],//j
            'SYM12' => [5 => 25, 4 => 10, 3 => 3],//10
            'SYM13' => [5 => 25, 4 => 10, 3 => 3],//9
        ];
        $this->weights = ['SYM1', 'SYM3', 'SYM4', 'SYM5', 'SYM6', 'SYM7', 'SYM8', 'SYM9', 'SYM10', 'SYM11', 'SYM12', 'SYM13'];
//        $this->linkedPercent = [90, 6, 3, 1];
        $this->linkedPercent = [70, 15, 10, 5];
        $this->weightsPercent = [1, 3, 5, 5, 7, 7, 14, 14, 14, 16, 16, 16];
        $this->bigWinPercent = 5;
        $this->megaWinPercent = 3;
        $this->superWinPercent = 1;

        $this->bigWin = 2;
        $this->megaWin = 4;
//        $this->superWin = 8;
//        $this->finishWin = 10;

        $this->log = new Logger;
        $this->sql = new Sql;
        $this->moneyManager = new MoneyManager;
    }

    public function winPercent()
    {
        $rand = rand(1, 100);
        return ($this->bigWinPercent > $rand) ? true : false;
    }

    public function linkedReels()
    {
        $linked = $this->randNumberPercent($this->linkedPercent) + 1;
        $start = rand(0, (4 - $linked));
        $finish = $linked + $start;
        return $start . ',' . $finish;
    }

    public function renderWeights($linkedReels)
    {
        $linkedReels = explode(",", $linkedReels);
        $start = $linkedReels[0] + 1;
        $finish = $linkedReels[1] + 1;

        $weights = [];
        $i = 0;
        while ($i++ < 5) {
            $n = 0;
            $arrWeights = [];
            if (($start + 1) <= $i && $finish >= $i) {
                $arrWeights = $weights[$i - 2];
            } else {
                while ($n++ < 3) {
                    $startRand = ($i == 1) ? ($this->weightsPercent[0] + 1) : 0;
                    $num = $this->randNumberPercent($this->weightsPercent, $startRand);
                    $weight = $this->weights[$num];
                    array_push($arrWeights, $weight);
                }
            }
            array_push($weights, $arrWeights);
        }
        return $weights;
    }

    public function checkCountMoney($min, $max, $weights, $betDenomination, $betBetLevel, $betPrice, $bigWinResult, $num)
    {
        $renderCheck = $this->checkRenderCountMoney($weights[0], $weights);
        $total = 0;

        foreach ($renderCheck as $key => $item) {
            $weightPrice = $this->weightsPrice[$key][$item['longVining']];
            $weightPrice = $weightPrice * $betBetLevel * $item['multiplier'];
            $weightPriceCent = $this->moneyManager->convertBalance($weightPrice, $betDenomination)['cents'];
            $total += $weightPriceCent;
        }

        $winMoney = $total / 100;
        if ($bigWinResult == true && $min <= $winMoney && $winMoney <= $max) {
            return true;
        }
        if ($bigWinResult == false && $min <= $winMoney && $winMoney <= $max) {
            return true;
        }
        if ($bigWinResult == false && $num % 3 == 0 && $winMoney == 0) {
            return true;
        }
        if ($num <= -10000 && $betPrice <= $winMoney && $winMoney <= $betPrice * 3) {
            $this->log->e('ERROR FOR ');
            $this->log->e('$min ' . $min);
            $this->log->e('$max ' . $max);
            $testCheck = ($bigWinResult == true) ? 'true' : 'false';
            $this->log->e('$bigWinResult ' . $testCheck);
            return true;
        }
        return false;
    }

    public function renderWin($weights)
    {
        $first = $weights[0];
        $check = false;
        $renderWin = [];
        foreach ($first as $key => $item) {
            $pushFirstWeight = 0;
            if (array_key_exists($item, $renderWin)) {
                array_push($renderWin[$item]['winWeight'], '0,' . $key);
                continue;
            }
            $arrItem = ['winWeight' => [], 'longVining' => 1];
            for ($i = 0; $i < count($weights) - 1; $i++) {
                $checkLongVining = 0;
                if ($check == false && $i != 0) {
                    break;
                }
                $check = false;
                foreach ($weights[$i + 1] as $keyW => $weight) {
                    if ($item == $weight || $weight == 'SYM1') {
                        if ($checkLongVining == 0) {
                            $arrItem['longVining']++;
                            $checkLongVining++;
                        }
                        $check = true;
                        $winNumber = (1 + $i) . ',' . $keyW;
                        if ($pushFirstWeight == 0) {
                            array_push($arrItem['winWeight'], '0,' . $key);
                        }
                        array_push($arrItem['winWeight'], $winNumber);
                        $pushFirstWeight++;
                    }
                    if ($check == false && $keyW == 2 && $arrItem['winWeight'] == []) {
                        $arrItem = false;
                    }
                }
            }
            $renderWin[$item] = $arrItem;
        }
        return $renderWin;
    }

    public function positionsWin($renderWin)
    {
        $positionsWin = [];
        $i = 0;
        foreach ($renderWin as $key => $item) {
            if ($item['longVining'] < 3 || $key == false) {
                continue;
            }
            $arrWin['ws.i' . $i . '.sym'] = $key;
            $arrWin['ws.i' . $i . '.reelset'] = 'basic';
            $arrWin['ws.i' . $i . '.betline'] = 'null';
            foreach ($item['winWeight'] as $keyNum => $winNumber) {
                $arrWin['ws.i' . $i . '.pos.i' . ($keyNum) . ''] = $winNumber;
                $positionsWin = array_merge($positionsWin, $arrWin);
            }
            $i++;
        }
        return $positionsWin;
    }

    public function winCoins($renderWin, $denomination, $betBetLevel)
    {
        $wonCoins = [];
        $totalWinCoins = 0;
        $totalWinCents = 0;
        $i = 0;
        foreach ($renderWin as $key => $item) {
            if ($item['longVining'] < 3 || $key == false) {
                continue;
            }
            $weightPrice = $this->weightsPrice[$key][$item['longVining']];

            $col = 0;
            for ($w = 0; $w < 5; $w++) {
                $n = 0;
                foreach ($item['winWeight'] as $keyP => $position) {
                    $colPosition = explode(',', $position)[0];
                    if ($colPosition == $col) {
                        $n++;
                    }
                }
                $n = ($n == 0) ? 1 : $n;
                $weightPrice *= $n;
                $col++;
            }
            $weightPrice *= $betBetLevel;
            $totalWinCoins += $weightPrice;

            $weightPriceCent = $this->moneyManager->convertBalance($weightPrice, $denomination);

            $totalWinCents += $weightPriceCent['cents'];

            $arrCoins['ws.i' . $i . '.types.i0.wintype'] = 'coins';
            $arrCoins['ws.i' . $i . '.types.i0.coins'] = $weightPrice;
            $arrCoins['ws.i' . $i . '.types.i0.cents'] = $weightPriceCent['cents'];
            $i++;
            $wonCoins = array_merge($wonCoins, $arrCoins);
        }
        $wonCoins['totalwin.coins'] = $totalWinCoins;
        $wonCoins['totalwin.cent'] = $totalWinCents;
        return $wonCoins;
    }

    public function bigWinResult($diffBalanceWin, $betDenomination, $betBetLevel)
    {
        return ($this->bigWin($betDenomination, $betBetLevel) < $diffBalanceWin) ? $this->winPercent() : false;
    }

    public function bigWin($betDenomination, $betBetLevel)
    {
        return $this->bigWin * ($betDenomination * 2) * $betBetLevel;
    }

    public function megaWin($betDenomination, $betBetLevel)
    {
        return $this->megaWin * ($betDenomination * 2) * $betBetLevel;
    }

    public function priceWin($betPrice, $sessid, $balance, $bigWinResult, $betDenomination, $betBetLevel, $diffBalanceWin)
    {
        $transactions = $this->sql->getUserTransactionsSpin($sessid);
        $balance5game = 0;
        foreach ($transactions as $item) {
            $balance5game += $item['balance'];
        }
        $countGame = count($transactions);
        $balance5game = $balance5game / $countGame;
        $balanceDiffPercentage = (($balance5game - $balance) / $balance5game) * 100;

        $factor = ($balanceDiffPercentage > 15) ? 1.5 : 1;
        $priceWin['min'] = ($betPrice / 2) * $factor;
        $priceWin['max'] = ($betPrice * 2.5) * $factor;
        if ($bigWinResult == true) {
            $priceWin['min'] = $this->bigWin($betDenomination, $betBetLevel);
            $priceWin['max'] = (($this->megaWin($betDenomination, $betBetLevel) - $betPrice) > $diffBalanceWin) ? $diffBalanceWin : ($this->megaWin($betDenomination, $betBetLevel) - $betPrice);
        }
        return $priceWin;
    }

    private function randNumberPercent($arrPercent, $min = 0)
    {
        $newRand = rand($min, 100);
        $first = 0;
        $second = 0;
        foreach ($arrPercent as $key => $item) {
            $first += $item;
            if ($first >= $newRand && $newRand >= $second) {
                return $key;
                break;
            }
            $second += $item;
        }
        return false;
    }

    private function checkRenderCountMoney($first, $weights)
    {
        $check = false;
        $renderCheck = [];
        $coincidenceKey = array_count_values($first);

        foreach ($first as $key => $item) {
            if (array_key_exists($item, $renderCheck)) {
                continue;
            }
            $multiplierStart = ($coincidenceKey[$item] > 1) ? $coincidenceKey[$item] : 1;
            $arrItem = ['multiplier' => $multiplierStart, 'longVining' => 1];
            for ($i = 0; $i < count($weights) - 1; $i++) {
                $checkLongVining = 0;
                $scoreMultiplier = 0;
                if ($check == false && $i != 0) {
                    break;
                }
                $check = false;
                foreach ($weights[$i + 1] as $keyW => $weight) {
                    if ($item == $weight || $weight == 'SYM1') {
                        if ($checkLongVining == 0) {
                            $arrItem['longVining']++;
                            $checkLongVining++;
                        }
                        $scoreMultiplier++;
                        $check = true;
                    }
                    if ($check == false && $keyW == 2 && $arrItem['longVining'] == 1) {
                        $arrItem = false;
                    }
                }
                if ($arrItem != false) {
                    $arrItem['multiplier'] *= ($scoreMultiplier == 0) ? 1 : $scoreMultiplier;
                }
            }
            $renderCheck[$item] = $arrItem;
        }
        return $renderCheck;
    }
}