<?php
require_once "logger.php";
require_once "moneyManager.php";
require_once "sql.php";
require_once "model.php";

class Actions1
{
    private $gamesoundurl = 'http://gaming-soft.info/netent/twinSpin/';
    private $bigWin;
    private $megaWin;
//    private $superWin;
//    private $finishWin;

    public $sql;
    public $log;
    public $model;
    public $moneyManager;

    function __construct()
    {
        $this->bigWin = 2;
        $this->megaWin = 4;
//        $this->superWin = 8;
//        $this->finishWin = 10;

        $this->log = new Logger;
        $this->model = new Model;
        $this->sql = new Sql;

        $this->moneyManager = new MoneyManager;
        return;
    }

    public function init($request)
    {
        $user = $this->sql->getUserData($request['sessid']);
        if ($user === FALSE) {
            $this->log->e('Your session is ended, open the game window again (actions.php/init)');
            die();
        }
        $convertedBalance = $this->moneyManager->convertBalance($user['balance']);

        $saveTransaction = [
            'uid' => $user['uid'],
            'sessid' => $request['sessid'],
            'action' => $request['action'],
            'bet' => 0,
            'betlevel' => 0,
            'playerBalanceCents' => 0,
            'playerBalanceCoins' => 0,
            'denomination' => 0,
            'preCombination' => '',
            'totalWinCents' => 0,
            'totalWinCoins' => 0,
            'balance' => $user['balance'],
            'calculBigWin' => $user['balance']
        ];

        if ($this->sql->saveTransaction($saveTransaction) === FALSE) {
            $this->log->e($request['action'] . ' transaction was not saved');
            die();
        }

        $initResponse = [
            "credit" => $convertedBalance['cash'],
            "gamesoundurl" => $this->gamesoundurl,
            "staticsharedurl" => "http://gaming-soft.info/netent/twinSpin/game/current",

            "rs.i0.r.i0.strip" => "SYM10,SYM10,SYM10,SYM7,SYM9,SYM9,SYM5,SYM8,SYM6,SYM11,SYM6,SYM11,SYM8,SYM8,SYM8,SYM11,SYM11,SYM11,SYM7,SYM7,SYM5,SYM8,SYM12,SYM12,SYM12,SYM3,SYM11,SYM4,SYM11,SYM12,SYM9,SYM12,SYM9,SYM8,SYM9,SYM6,SYM12,SYM12,SYM12,SYM7,SYM10,SYM13,SYM13,SYM13,SYM9,SYM12,SYM12,SYM6,SYM11,SYM9,SYM9,SYM9,SYM7,SYM7,SYM7,SYM11,SYM12,SYM7,SYM6,SYM7",
            "rs.i0.r.i1.strip" => "SYM7,SYM10,SYM5,SYM9,SYM5,SYM10,SYM5,SYM10,SYM8,SYM9,SYM10,SYM6,SYM6,SYM6,SYM11,SYM11,SYM11,SYM7,SYM12,SYM13,SYM3,SYM11,SYM11,SYM11,SYM4,SYM5,SYM6,SYM12,SYM12,SYM12,SYM10,SYM5,SYM10,SYM5,SYM11,SYM13,SYM13,SYM13,SYM11,SYM5,SYM9,SYM9,SYM9,SYM11,SYM1,SYM8,SYM5,SYM9,SYM8,SYM8,SYM8,SYM6,SYM11,SYM11,SYM11,SYM4,SYM13,SYM5,SYM5,SYM12,SYM12,SYM12,SYM10,SYM8,SYM8,SYM6,SYM11,SYM11,SYM11,SYM13,SYM13,SYM13,SYM11,SYM5,SYM9,SYM9,SYM9,SYM12,SYM12,SYM5,SYM9",
            "rs.i0.r.i2.strip" => "SYM5,SYM10,SYM10,SYM10,SYM4,SYM9,SYM9,SYM9,SYM5,SYM5,SYM5,SYM8,SYM13,SYM6,SYM10,SYM13,SYM13,SYM13,SYM7,SYM13,SYM13,SYM13,SYM11,SYM3,SYM13,SYM4,SYM9,SYM4,SYM9,SYM11,SYM11,SYM11,SYM12,SYM12,SYM10,SYM10,SYM5,SYM13,SYM8,SYM8,SYM8,SYM11,SYM13,SYM8,SYM6,SYM8,SYM3,SYM3,SYM13,SYM11,SYM11,SYM11,SYM6,SYM4,SYM5,SYM11,SYM13,SYM11,SYM12,SYM4,SYM9,SYM4,SYM9,SYM11,SYM11,SYM11,SYM6,SYM12,SYM12,SYM12,SYM13,SYM13,SYM13,SYM5,SYM13,SYM13,SYM8,SYM13,SYM13,SYM6,SYM5,SYM11,SYM10,SYM11,SYM6,SYM13,SYM11,SYM13,SYM1,SYM9,SYM10,SYM6,SYM12",
            "rs.i0.r.i3.strip" => "SYM12,SYM11,SYM4,SYM4,SYM4,SYM9,SYM3,SYM3,SYM3,SYM13,SYM13,SYM13,SYM6,SYM8,SYM8,SYM8,SYM7,SYM13,SYM11,SYM11,SYM11,SYM13,SYM12,SYM12,SYM12,SYM11,SYM13,SYM4,SYM13,SYM5,SYM10,SYM11,SYM10,SYM11,SYM13,SYM13,SYM13,SYM4,SYM8,SYM8,SYM8,SYM9,SYM9,SYM9,SYM13,SYM5,SYM10,SYM10,SYM10,SYM4,SYM4,SYM4,SYM9,SYM3,SYM3,SYM3,SYM11,SYM11,SYM11,SYM6,SYM8,SYM8,SYM8,SYM7,SYM13,SYM11,SYM11,SYM11,SYM13,SYM12,SYM12,SYM12,SYM11,SYM13,SYM4,SYM13,SYM10,SYM5,SYM10,SYM13,SYM13,SYM13,SYM4,SYM8,SYM8,SYM8,SYM9,SYM9,SYM9,SYM13,SYM1,SYM12,SYM11",
            "rs.i0.r.i4.strip" => "SYM7,SYM10,SYM10,SYM10,SYM9,SYM6,SYM9,SYM13,SYM13,SYM13,SYM11,SYM5,SYM6,SYM9,SYM7,SYM11,SYM8,SYM10,SYM11,SYM6,SYM11,SYM6,SYM11,SYM7,SYM8,SYM8,SYM8,SYM11,SYM11,SYM11,SYM7,SYM9,SYM6,SYM9,SYM12,SYM12,SYM12,SYM3,SYM11,SYM11,SYM11,SYM4,SYM12,SYM12,SYM12,SYM5,SYM10,SYM5,SYM11,SYM9,SYM10,SYM10,SYM10,SYM13,SYM13,SYM13,SYM8,SYM6,SYM8,SYM7,SYM12,SYM6,SYM7,SYM8,SYM9,SYM10,SYM5,SYM11,SYM12,SYM9,SYM9,SYM9,SYM7,SYM12,SYM7,SYM12",
            "rs.i0.r.i5.strip" => "SYM7,SYM10,SYM4,SYM9,SYM6,SYM10,SYM11,SYM6,SYM7,SYM10,SYM5,SYM9,SYM6,SYM11,SYM8,SYM12,SYM7,SYM10,SYM6,SYM13,SYM9,SYM9,SYM9,SYM5,SYM10,SYM5,SYM8,SYM8,SYM8,SYM10,SYM11,SYM6,SYM11,SYM6,SYM11,SYM11,SYM11,SYM10,SYM10,SYM10,SYM7,SYM12,SYM7,SYM13,SYM13,SYM13,SYM3,SYM5,SYM11,SYM11,SYM11,SYM4,SYM4,SYM12,SYM12,SYM12,SYM3,SYM10,SYM6,SYM8,SYM6,SYM13,SYM13,SYM7,SYM9,SYM9,SYM9,SYM7,SYM10,SYM4,SYM10,SYM9,SYM6,SYM9,SYM9,SYM9,SYM5,SYM10,SYM5,SYM8,SYM8,SYM8,SYM6,SYM11,SYM6,SYM11,SYM11,SYM11,SYM10,SYM10,SYM10,SYM7,SYM7,SYM13,SYM13,SYM13,SYM11,SYM11,SYM11,SYM4,SYM8,SYM4,SYM12,SYM12,SYM12,SYM10,SYM6,SYM10,SYM8,SYM7,SYM6,SYM5,SYM13,SYM13,SYM8,SYM6,SYM7,SYM5,SYM11,SYM8,SYM4,SYM9,SYM9,SYM9,SYM13,SYM4,SYM11,SYM1",
            "rs.i0.r.i6.strip" => "SYM3,SYM3,SYM3,SYM10,SYM10,SYM10,SYM4,SYM4,SYM4,SYM9,SYM9,SYM9,SYM5,SYM5,SYM5,SYM8,SYM8,SYM8,SYM6,SYM13,SYM13,SYM13,SYM7,SYM13,SYM13,SYM13,SYM11,SYM11,SYM11,SYM12,SYM12,SYM12,SYM10,SYM10,SYM10,SYM5,SYM8,SYM8,SYM8,SYM6,SYM11,SYM7,SYM10,SYM13,SYM13,SYM13,SYM7,SYM10,SYM10,SYM10,SYM7,SYM10,SYM11,SYM4,SYM4,SYM4,SYM9,SYM9,SYM9,SYM5,SYM5,SYM5,SYM8,SYM8,SYM8,SYM6,SYM13,SYM13,SYM13,SYM7,SYM6,SYM13,SYM11,SYM11,SYM11,SYM12,SYM12,SYM12,SYM10,SYM10,SYM10,SYM5,SYM6,SYM11,SYM8,SYM8,SYM8,SYM6,SYM6,SYM7,SYM8,SYM1,SYM12,SYM11,SYM12",
            "rs.i0.r.i7.strip" => "SYM13,SYM3,SYM10,SYM10,SYM10,SYM13,SYM4,SYM9,SYM10,SYM4,SYM9,SYM9,SYM9,SYM13,SYM11,SYM5,SYM11,SYM6,SYM11,SYM6,SYM13,SYM7,SYM13,SYM8,SYM8,SYM8,SYM11,SYM11,SYM11,SYM13,SYM7,SYM13,SYM5,SYM12,SYM12,SYM12,SYM6,SYM13,SYM3,SYM9,SYM13,SYM11,SYM11,SYM11,SYM4,SYM8,SYM10,SYM11,SYM10,SYM12,SYM12,SYM12,SYM5,SYM10,SYM11,SYM5,SYM10,SYM10,SYM10,SYM7,SYM8,SYM4,SYM10,SYM6,SYM12,SYM7,SYM13,SYM8,SYM10,SYM10,SYM7,SYM8,SYM12,SYM5,SYM11,SYM11,SYM8,SYM8,SYM4,SYM11,SYM9,SYM9,SYM12,SYM12,SYM8,SYM6,SYM9,SYM8,SYM6,SYM13,SYM9,SYM8,SYM6,SYM5,SYM10,SYM7,SYM13,SYM8,SYM12,SYM6,SYM13,SYM9,SYM7,SYM13,SYM13,SYM12,SYM12,SYM11,SYM11,SYM8,SYM12,SYM12,SYM11,SYM11,SYM7,SYM11,SYM11,SYM13,SYM13,SYM13,SYM10,SYM10,SYM6,SYM7,SYM13,SYM9,SYM9,SYM9,SYM7,SYM12,SYM7,SYM4,SYM13,SYM8,SYM13,SYM13,SYM13",
            "rs.i0.r.i8.strip" => "SYM3,SYM8,SYM9,SYM3,SYM10,SYM13,SYM13,SYM4,SYM9,SYM10,SYM4,SYM9,SYM9,SYM9,SYM5,SYM10,SYM12,SYM5,SYM8,SYM8,SYM8,SYM6,SYM11,SYM6,SYM11,SYM11,SYM11,SYM10,SYM10,SYM10,SYM13,SYM13,SYM7,SYM13,SYM12,SYM7,SYM13,SYM13,SYM13,SYM3,SYM11,SYM11,SYM11,SYM4,SYM13,SYM13,SYM5,SYM13,SYM12,SYM12,SYM12,SYM10,SYM10,SYM10,SYM8,SYM8,SYM8,SYM6,SYM7,SYM9,SYM9,SYM9,SYM3,SYM8,SYM9,SYM3,SYM10,SYM13,SYM13,SYM4,SYM9,SYM10,SYM4,SYM9,SYM9,SYM9,SYM5,SYM10,SYM12,SYM5,SYM8,SYM8,SYM8,SYM6,SYM11,SYM6,SYM11,SYM11,SYM11,SYM10,SYM10,SYM10,SYM13,SYM7,SYM13,SYM4,SYM13,SYM12,SYM13,SYM13,SYM13,SYM3,SYM11,SYM11,SYM11,SYM4,SYM13,SYM13,SYM5,SYM13,SYM12,SYM12,SYM12,SYM10,SYM10,SYM10,SYM8,SYM8,SYM8,SYM6,SYM7,SYM9,SYM9,SYM9,SYM1,SYM12",
            "rs.i0.r.i9.strip" => "SYM13,SYM13,SYM13,SYM8,SYM8,SYM8,SYM3,SYM8,SYM10,SYM10,SYM10,SYM4,SYM9,SYM9,SYM9,SYM4,SYM9,SYM9,SYM9,SYM13,SYM13,SYM6,SYM11,SYM6,SYM8,SYM8,SYM8,SYM11,SYM11,SYM11,SYM7,SYM6,SYM5,SYM7,SYM6,SYM5,SYM9,SYM8,SYM5,SYM4,SYM8,SYM11,SYM6,SYM8,SYM12,SYM12,SYM12,SYM3,SYM11,SYM4,SYM12,SYM12,SYM11,SYM11,SYM11,SYM12,SYM12,SYM12,SYM11,SYM11,SYM11,SYM5,SYM13,SYM11,SYM6,SYM10,SYM10,SYM10,SYM5,SYM12,SYM12,SYM12,SYM10,SYM10,SYM10,SYM8,SYM8,SYM8,SYM13,SYM9,SYM4,SYM5,SYM6,SYM13,SYM13,SYM13,SYM6,SYM9,SYM9,SYM9,SYM5,SYM6,SYM13,SYM7,SYM12,SYM7,SYM13,SYM13,SYM11,SYM10,SYM11,SYM5,SYM13,SYM12,SYM4,SYM8,SYM7,SYM8,SYM4,SYM13,SYM8,SYM12,SYM4,SYM12,SYM10,SYM7,SYM12,SYM4,SYM11,SYM6,SYM10,SYM9,SYM10,SYM4,SYM12,SYM11,SYM12,SYM11,SYM12,SYM3,SYM8,SYM8,SYM8,SYM10,SYM3,SYM10,SYM10,SYM10,SYM4,SYM5,SYM6,SYM9,SYM9,SYM9,SYM4,SYM9,SYM9,SYM9,SYM6,SYM11,SYM6,SYM8,SYM8,SYM8,SYM11,SYM11,SYM11,SYM7,SYM6,SYM5,SYM7,SYM6,SYM5,SYM9,SYM8,SYM13,SYM5,SYM4,SYM12,SYM12,SYM12,SYM3,SYM13,SYM13,SYM3,SYM12,SYM11,SYM12,SYM4,SYM8,SYM8,SYM4,SYM11,SYM11,SYM11,SYM12,SYM12,SYM12,SYM11,SYM11,SYM11,SYM5,SYM6,SYM7,SYM10,SYM10,SYM10,SYM5,SYM6,SYM7,SYM12,SYM12,SYM12,SYM10,SYM10,SYM10,SYM12,SYM8,SYM8,SYM8,SYM4,SYM5,SYM6,SYM13,SYM13,SYM13,SYM6,SYM12,SYM7,SYM11,SYM9,SYM9,SYM9,SYM7,SYM12,SYM7,SYM8,SYM9,SYM10,SYM11,SYM10,SYM11,SYM7,SYM4,SYM13,SYM13,SYM12,SYM12,SYM13,SYM13,SYM12,SYM12,SYM5,SYM11,SYM8,SYM8,SYM9,SYM9,SYM10,SYM6,SYM7,SYM5,SYM10,SYM4,SYM11,SYM6",

            "rs.i1.r.i0.strip" => "SYM10,SYM4,SYM10,SYM9,SYM5,SYM8,SYM6,SYM8,SYM11,SYM7,SYM11,SYM12,SYM3,SYM7,SYM12,SYM4,SYM12,SYM12,SYM12,SYM7,SYM5,SYM5,SYM10,SYM9,SYM9,SYM7,SYM7,SYM7,SYM13,SYM13,SYM7,SYM10,SYM10,SYM9,SYM7,SYM8,SYM6,SYM8,SYM11,SYM7,SYM11,SYM12,SYM7,SYM12,SYM7,SYM10,SYM12,SYM12,SYM12,SYM7,SYM10,SYM9,SYM7,SYM9,SYM7,SYM13,SYM13,SYM13",
            "rs.i1.r.i1.strip" => "SYM11,SYM3,SYM13,SYM4,SYM13,SYM9,SYM8,SYM6,SYM6,SYM6,SYM11,SYM11,SYM7,SYM7,SYM6,SYM11,SYM11,SYM12,SYM6,SYM11,SYM11,SYM11,SYM6,SYM12,SYM5,SYM12,SYM10,SYM11,SYM10,SYM6,SYM11,SYM9,SYM7,SYM9,SYM13,SYM13,SYM13,SYM7,SYM5,SYM11,SYM9,SYM13,SYM13,SYM9,SYM8,SYM8,SYM11,SYM6,SYM11,SYM12,SYM6,SYM11,SYM11,SYM6,SYM12,SYM12,SYM10,SYM11,SYM10,SYM6,SYM11,SYM9,SYM7,SYM9,SYM6,SYM11,SYM13,SYM13,SYM1,SYM11,SYM10",
            "rs.i1.r.i2.strip" => "SYM9,SYM4,SYM9,SYM5,SYM10,SYM5,SYM5,SYM5,SYM8,SYM6,SYM8,SYM11,SYM7,SYM6,SYM10,SYM3,SYM11,SYM5,SYM11,SYM12,SYM5,SYM12,SYM10,SYM10,SYM10,SYM8,SYM6,SYM5,SYM13,SYM13,SYM13,SYM5,SYM10,SYM9,SYM5,SYM9,SYM5,SYM5,SYM5,SYM8,SYM6,SYM8,SYM11,SYM12,SYM5,SYM12,SYM8,SYM6,SYM5,SYM10,SYM5,SYM10,SYM10,SYM10,SYM5,SYM8,SYM10,SYM6,SYM13,SYM13,SYM13,SYM11,SYM6,SYM1,SYM11,SYM8",
            "rs.i1.r.i3.strip" => "SYM10,SYM3,SYM10,SYM4,SYM4,SYM4,SYM12,SYM9,SYM3,SYM10,SYM8,SYM6,SYM8,SYM11,SYM5,SYM11,SYM12,SYM4,SYM12,SYM11,SYM7,SYM7,SYM3,SYM12,SYM13,SYM5,SYM13,SYM10,SYM8,SYM6,SYM8,SYM9,SYM9,SYM9,SYM13,SYM13,SYM13,SYM8,SYM13,SYM8,SYM10,SYM3,SYM10,SYM4,SYM4,SYM9,SYM3,SYM3,SYM8,SYM13,SYM8,SYM11,SYM12,SYM4,SYM12,SYM11,SYM12,SYM13,SYM5,SYM13,SYM10,SYM6,SYM8,SYM11,SYM3,SYM13,SYM6,SYM12,SYM8,SYM6,SYM8,SYM9,SYM9,SYM9,SYM13,SYM13,SYM13,SYM1,SYM12",
            "rs.i1.r.i4.strip" => "SYM3,SYM3,SYM3,SYM10,SYM7,SYM12,SYM9,SYM4,SYM9,SYM8,SYM6,SYM8,SYM12,SYM13,SYM7,SYM13,SYM7,SYM12,SYM6,SYM8,SYM5,SYM9,SYM7,SYM11,SYM3,SYM11,SYM13,SYM12,SYM4,SYM12,SYM10,SYM5,SYM10,SYM8,SYM8,SYM8,SYM6,SYM8,SYM13,SYM13,SYM13,SYM5,SYM9,SYM7,SYM10,SYM4,SYM4,SYM4,SYM12,SYM9,SYM4,SYM9,SYM8,SYM6,SYM8,SYM11,SYM3,SYM12,SYM13,SYM7,SYM13,SYM12,SYM11,SYM3,SYM11,SYM12,SYM4,SYM12,SYM10,SYM5,SYM10,SYM8,SYM8,SYM8,SYM6,SYM5,SYM9,SYM13,SYM13,SYM13,SYM1,SYM11,SYM6",

            "rs.i0.r.i0.syms" => "SYM10,SYM10,SYM10",
            "rs.i0.r.i1.syms" => "SYM7,SYM10,SYM5",
            "rs.i0.r.i2.syms" => "SYM5,SYM10,SYM10",
            "rs.i0.r.i3.syms" => "SYM12,SYM11,SYM4",
            "rs.i0.r.i4.syms" => "SYM7,SYM10,SYM10",
            "rs.i0.r.i5.syms" => "SYM7,SYM10,SYM4",
            "rs.i0.r.i6.syms" => "SYM3,SYM3,SYM3",
            "rs.i0.r.i7.syms" => "SYM13,SYM3,SYM10",
            "rs.i0.r.i8.syms" => "SYM3,SYM8,SYM9",
            "rs.i0.r.i9.syms" => "SYM13,SYM13,SYM13",

            "rs.i1.r.i0.syms" => "SYM10,SYM4,SYM10",
            "rs.i1.r.i1.syms" => "SYM11,SYM3,SYM13",
            "rs.i1.r.i2.syms" => "SYM9,SYM4,SYM9",
            "rs.i1.r.i3.syms" => "SYM10,SYM3,SYM10",
            "rs.i1.r.i4.syms" => "SYM3,SYM3,SYM3",

            "rs.i0.r.i0.pos" => "0",
            "rs.i0.r.i1.pos" => "0",
            "rs.i0.r.i2.pos" => "0",
            "rs.i0.r.i3.pos" => "0",
            "rs.i0.r.i4.pos" => "0",
            "rs.i0.r.i5.pos" => "0",
            "rs.i0.r.i6.pos" => "0",
            "rs.i0.r.i7.pos" => "0",
            "rs.i0.r.i8.pos" => "0",
            "rs.i0.r.i9.pos" => "0",

            "rs.i1.r.i0.pos" => "0",
            "rs.i1.r.i1.pos" => "0",
            "rs.i1.r.i2.pos" => "0",
            "rs.i1.r.i3.pos" => "0",
            "rs.i1.r.i4.pos" => "0",

            "rs.i0.r.i0.hold" => 'false',
            "rs.i0.r.i1.hold" => 'false',
            "rs.i0.r.i2.hold" => 'false',
            "rs.i0.r.i3.hold" => 'false',
            "rs.i0.r.i4.hold" => 'false',
            "rs.i0.r.i5.hold" => 'false',
            "rs.i0.r.i6.hold" => 'false',
            "rs.i0.r.i7.hold" => 'false',
            "rs.i0.r.i8.hold" => 'false',
            "rs.i0.r.i9.hold" => 'false',

            "rs.i1.r.i0.hold" => 'false',
            "rs.i1.r.i1.hold" => 'false',
            "rs.i1.r.i2.hold" => 'false',
            "rs.i1.r.i3.hold" => 'false',
            "rs.i1.r.i4.hold" => 'false',

            "rs.i0.r.i0.id" => "twinreel-0-1",
            "rs.i0.r.i1.id" => "twinreel-1-2",
            "rs.i0.r.i2.id" => "twinreel-2-3",
            "rs.i0.r.i3.id" => "twinreel-3-4",
            "rs.i0.r.i4.id" => "twinreel-0-1-2",
            "rs.i0.r.i5.id" => "twinreel-1-2-3",
            "rs.i0.r.i6.id" => "twinreel-2-3-4",
            "rs.i0.r.i7.id" => "twinreel-0-1-2-3",
            "rs.i0.r.i8.id" => "twinreel-1-2-3-4",
            "rs.i0.r.i9.id" => "twinreel-0-1-2-3-4",

            "rs.i1.r.i0.id" => "basic-0",
            "rs.i1.r.i1.id" => "basic-1",
            "rs.i1.r.i2.id" => "basic-2",
            "rs.i1.r.i3.id" => "basic-3",
            "rs.i1.r.i4.id" => "basic-4",

            "rs.i0.id" => "twinreels",
            "bl.i0.line" => "0/1/2,0/1/2,0/1/2,0/1/2,0/1/2",
            "bl.i0.coins" => "25",
            "bl.i0.id" => "0",
            "bl.i0.reelset" => "ALL",
            "rs.i1.id" => "basic",
            "bl.standard" => "0",
            "autoplay" => "10,25,50,75,100,250,500,750,1000",
            "denomination.all" => "1,2,5,10,20,50",
            "betlevel.all" => "1,2,3,4,5,6,7,8,9,10",
            "gameEventSetters.enabled" => 'false',
            "gamestate.current" => "basic",
            "confirmBetMessageEnabled" => 'false',
            "game.win.cents" => "0",
            "betlevel.standard" => "1",
            "clientaction" => "init",
            "playercurrencyiso" => "EUR",
            "historybutton" => 'false',
            "nextaction" => "spin",
            "game.win.coins" => "0",
            "totalwin.cents" => "0",
            "multiplier" => "1",
            "gameover" => 'true',
            "wavecount" => "1",
            "restore" => 'false',
            "game.win.amount" => "null",
            "isJackpotWin" => 'false',
            "jackpotcurrencyiso" => "EUR",
            "jackpotcurrency" => "&#x20AC;",
            "playercurrency" => "&#x20AC;",
            "denomination.standard" => "5",
            "g4mode" => 'false',
            "autoplayLossLimitEnabled" => 'false',
            "nearwinallowed" => 'true',
            "iframeEnabled" => 'true',
            "playforfun" => 'true',
            "totalwin.coins" => "0",
        ];
        return print(urldecode(http_build_query($initResponse)));
    }

    public function payTable($request)
    {
        $user = $this->sql->getUserData($request['sessid']);

        if ($user === FALSE) {
            $this->log->e('Your session is ended, open the game window again (actions php/paytable)');
            die();
        }
        $convertedBalance = $this->moneyManager->convertBalance($user['balance']);

        $payTableResponse = [
            'credit' => $convertedBalance['cash'],
            'gamesoundurl' => $this->gamesoundurl,
            'pt.i0.id' => 'basic',
            'pt.i0.comp.i0.n' => '3',
            'pt.i0.comp.i1.n' => '4',
            'pt.i0.comp.i2.n' => '5',
            'pt.i0.comp.i3.n' => '3',
            'pt.i0.comp.i4.n' => '4',
            'pt.i0.comp.i5.n' => '5',
            'pt.i0.comp.i6.n' => '3',
            'pt.i0.comp.i7.n' => '4',
            'pt.i0.comp.i8.n' => '5',
            'pt.i0.comp.i9.n' => '3',
            'pt.i0.comp.i10.n' => '4',
            'pt.i0.comp.i11.n' => '5',
            'pt.i0.comp.i12.n' => '3',
            'pt.i0.comp.i13.n' => '4',
            'pt.i0.comp.i14.n' => '5',
            'pt.i0.comp.i15.n' => '3',
            'pt.i0.comp.i16.n' => '4',
            'pt.i0.comp.i17.n' => '5',
            'pt.i0.comp.i18.n' => '3',
            'pt.i0.comp.i19.n' => '4',
            'pt.i0.comp.i20.n' => '5',
            'pt.i0.comp.i21.n' => '3',
            'pt.i0.comp.i22.n' => '4',
            'pt.i0.comp.i23.n' => '5',
            'pt.i0.comp.i24.n' => '3',
            'pt.i0.comp.i25.n' => '4',
            'pt.i0.comp.i26.n' => '5',
            'pt.i0.comp.i27.n' => '3',
            'pt.i0.comp.i28.n' => '4',
            'pt.i0.comp.i29.n' => '5',
            'pt.i0.comp.i30.n' => '3',
            'pt.i0.comp.i31.n' => '4',
            'pt.i0.comp.i32.n' => '5',

            'pt.i0.comp.i0.multi' => '50',
            'pt.i0.comp.i1.multi' => '250',
            'pt.i0.comp.i2.multi' => '1000',
            'pt.i0.comp.i3.multi' => '30',
            'pt.i0.comp.i4.multi' => '150',
            'pt.i0.comp.i5.multi' => '500',
            'pt.i0.comp.i6.multi' => '15',
            'pt.i0.comp.i7.multi' => '100',
            'pt.i0.comp.i8.multi' => '400',
            'pt.i0.comp.i9.multi' => '10',
            'pt.i0.comp.i10.multi' => '75',
            'pt.i0.comp.i11.multi' => '250',
            'pt.i0.comp.i12.multi' => '10',
            'pt.i0.comp.i13.multi' => '75',
            'pt.i0.comp.i14.multi' => '250',
            'pt.i0.comp.i15.multi' => '4',
            'pt.i0.comp.i16.multi' => '15',
            'pt.i0.comp.i17.multi' => '40',
            'pt.i0.comp.i18.multi' => '4',
            'pt.i0.comp.i19.multi' => '15',
            'pt.i0.comp.i20.multi' => '40',
            'pt.i0.comp.i21.multi' => '4',
            'pt.i0.comp.i22.multi' => '15',
            'pt.i0.comp.i23.multi' => '40',
            'pt.i0.comp.i24.multi' => '3',
            'pt.i0.comp.i25.multi' => '10',
            'pt.i0.comp.i26.multi' => '25',
            'pt.i0.comp.i27.multi' => '3',
            'pt.i0.comp.i28.multi' => '10',
            'pt.i0.comp.i29.multi' => '25',
            'pt.i0.comp.i30.multi' => '3',
            'pt.i0.comp.i31.multi' => '10',
            'pt.i0.comp.i32.multi' => '25',

            'pt.i0.comp.i0.symbol' => 'SYM3',
            'pt.i0.comp.i1.symbol' => 'SYM3',
            'pt.i0.comp.i2.symbol' => 'SYM3',
            'pt.i0.comp.i3.symbol' => 'SYM4',
            'pt.i0.comp.i4.symbol' => 'SYM4',
            'pt.i0.comp.i5.symbol' => 'SYM4',
            'pt.i0.comp.i6.symbol' => 'SYM5',
            'pt.i0.comp.i7.symbol' => 'SYM5',
            'pt.i0.comp.i8.symbol' => 'SYM5',
            'pt.i0.comp.i9.symbol' => 'SYM6',
            'pt.i0.comp.i10.symbol' => 'SYM6',
            'pt.i0.comp.i11.symbol' => 'SYM6',
            'pt.i0.comp.i12.symbol' => 'SYM7',
            'pt.i0.comp.i13.symbol' => 'SYM7',
            'pt.i0.comp.i14.symbol' => 'SYM7',
            'pt.i0.comp.i15.symbol' => 'SYM8',
            'pt.i0.comp.i16.symbol' => 'SYM8',
            'pt.i0.comp.i17.symbol' => 'SYM8',
            'pt.i0.comp.i18.symbol' => 'SYM9',
            'pt.i0.comp.i19.symbol' => 'SYM9',
            'pt.i0.comp.i20.symbol' => 'SYM9',
            'pt.i0.comp.i21.symbol' => 'SYM10',
            'pt.i0.comp.i22.symbol' => 'SYM10',
            'pt.i0.comp.i23.symbol' => 'SYM10',
            'pt.i0.comp.i24.symbol' => 'SYM11',
            'pt.i0.comp.i25.symbol' => 'SYM11',
            'pt.i0.comp.i26.symbol' => 'SYM11',
            'pt.i0.comp.i27.symbol' => 'SYM12',
            'pt.i0.comp.i28.symbol' => 'SYM12',
            'pt.i0.comp.i29.symbol' => 'SYM12',
            'pt.i0.comp.i30.symbol' => 'SYM13',
            'pt.i0.comp.i31.symbol' => 'SYM13',
            'pt.i0.comp.i32.symbol' => 'SYM13',

            'pt.i0.comp.i0.type' => 'betline',
            'pt.i0.comp.i1.type' => 'betline',
            'pt.i0.comp.i2.type' => 'betline',
            'pt.i0.comp.i3.type' => 'betline',
            'pt.i0.comp.i4.type' => 'betline',
            'pt.i0.comp.i5.type' => 'betline',
            'pt.i0.comp.i6.type' => 'betline',
            'pt.i0.comp.i7.type' => 'betline',
            'pt.i0.comp.i8.type' => 'betline',
            'pt.i0.comp.i9.type' => 'betline',
            'pt.i0.comp.i10.type' => 'betline',
            'pt.i0.comp.i11.type' => 'betline',
            'pt.i0.comp.i12.type' => 'betline',
            'pt.i0.comp.i13.type' => 'betline',
            'pt.i0.comp.i14.type' => 'betline',
            'pt.i0.comp.i15.type' => 'betline',
            'pt.i0.comp.i16.type' => 'betline',
            'pt.i0.comp.i17.type' => 'betline',
            'pt.i0.comp.i18.type' => 'betline',
            'pt.i0.comp.i19.type' => 'betline',
            'pt.i0.comp.i20.type' => 'betline',
            'pt.i0.comp.i21.type' => 'betline',
            'pt.i0.comp.i22.type' => 'betline',
            'pt.i0.comp.i23.type' => 'betline',
            'pt.i0.comp.i24.type' => 'betline',
            'pt.i0.comp.i25.type' => 'betline',
            'pt.i0.comp.i26.type' => 'betline',
            'pt.i0.comp.i27.type' => 'betline',
            'pt.i0.comp.i28.type' => 'betline',
            'pt.i0.comp.i29.type' => 'betline',
            'pt.i0.comp.i30.type' => 'betline',
            'pt.i0.comp.i31.type' => 'betline',
            'pt.i0.comp.i32.type' => 'betline',

            'pt.i0.comp.i0.freespins' => '0',
            'pt.i0.comp.i1.freespins' => '0',
            'pt.i0.comp.i2.freespins' => '0',
            'pt.i0.comp.i3.freespins' => '0',
            'pt.i0.comp.i4.freespins' => '0',
            'pt.i0.comp.i5.freespins' => '0',
            'pt.i0.comp.i6.freespins' => '0',
            'pt.i0.comp.i7.freespins' => '0',
            'pt.i0.comp.i8.freespins' => '0',
            'pt.i0.comp.i9.freespins' => '0',
            'pt.i0.comp.i10.freespins' => '0',
            'pt.i0.comp.i11.freespins' => '0',
            'pt.i0.comp.i12.freespins' => '0',
            'pt.i0.comp.i13.freespins' => '0',
            'pt.i0.comp.i14.freespins' => '0',
            'pt.i0.comp.i15.freespins' => '0',
            'pt.i0.comp.i16.freespins' => '0',
            'pt.i0.comp.i17.freespins' => '0',
            'pt.i0.comp.i18.freespins' => '0',
            'pt.i0.comp.i19.freespins' => '0',
            'pt.i0.comp.i20.freespins' => '0',
            'pt.i0.comp.i21.freespins' => '0',
            'pt.i0.comp.i22.freespins' => '0',
            'pt.i0.comp.i23.freespins' => '0',
            'pt.i0.comp.i24.freespins' => '0',
            'pt.i0.comp.i25.freespins' => '0',
            'pt.i0.comp.i26.freespins' => '0',
            'pt.i0.comp.i27.freespins' => '0',
            'pt.i0.comp.i28.freespins' => '0',
            'pt.i0.comp.i29.freespins' => '0',
            'pt.i0.comp.i30.freespins' => '0',
            'pt.i0.comp.i31.freespins' => '0',
            'pt.i0.comp.i32.freespins' => '0',

            'bl.i0.id' => '0',
            'bl.i0.coins' => '25',
            'bl.i0.line' => '0/1/2,0/1/2,0/1/2,0/1/2,0/1/2',
            'bl.i0.reelset' => 'ALL',

            'g4mode' => 'false',
            'isJackpotWin' => 'false',
            'clientaction' => 'paytable',
            'playercurrencyiso' => 'EUR',
            'jackpotcurrencyiso' => 'EUR',
            'jackpotcurrency' => '€',
            'playercurrency' => '€',
            'historybutton' => 'false',
            'playforfun' => 'true',
        ];

        return print(urldecode(http_build_query($payTableResponse)));
    }

    public function spin($request)
    {
        $sessid = $request['sessid'];
        $betDenomination = $request['bet_denomination'];
        $betBetLevel = $request['bet_betlevel'];

        $user = $this->sql->getUserData($sessid);
        if (!$betDenomination || !$betBetLevel || $user === FALSE) {
            $this->log->e($request['action'] . ' bet_denomination or bet_betlevel or user is false');
            die();
        }
        $betPrice = $this->moneyManager->betLevels($betBetLevel, $betDenomination);

        $transactionsInit = $this->sql->getUserTransactionsInit($sessid)['calculBigWin'];
        $diffBalanceWin = $transactionsInit - $user['balance'];

        /*  $bigWin = $this->bigWin * ($betDenomination * 2) * $betBetLevel;
          $megaWin = $this->megaWin * ($betDenomination * 2) * $betBetLevel;

          $bigWinResult = ($bigWin < $diffBalanceWin) ? $this->model->winPercent() : false;*/
        $bigWinResult = $this->model->bigWinResult($diffBalanceWin, $betDenomination, $betBetLevel);

        /*$transactions = $this->sql->getUserTransactionsSpin($sessid);
        $balance5game = 0;
        foreach ($transactions as $item) {
            $balance5game += $item['balance'];
        }
        $countGame = count($transactions);
        $balance5game = $balance5game / $countGame;
        $balanceDiffPercentage = (($balance5game - $user['balance']) / $balance5game) * 100;

        $factor = ($balanceDiffPercentage > 15) ? 1.5 : 1;*/
//        $minWin = ($countGame == 5) ? ($betPrice / 2) * $factor : ($betPrice * 1.5) * $factor;
//        $maxWin = ($countGame == 5) ? ($betPrice * 2.5) * $factor : ($betPrice * 6) * $factor;
        $minWin = ($betPrice / 2) * $factor;
        $maxWin = ($betPrice * 2.5) * $factor;

        if ($bigWinResult == true) {
            $minWin = $bigWin;
            $maxWin = (($megaWin - $betPrice) > $diffBalanceWin) ? $diffBalanceWin : ($megaWin - $betPrice);
        }
        $minWin = $this->model->minWin($betPrice);
        $maxWin = $this->model->maxWin($betPrice);
        $linkedReels = '';
        $weights = '';
//        $bigWinResult = true;
//        $minWin = 2;
//        $maxWin = 1;
        for ($i = -1; 1 > $i; $i--) {
            $linkedReels = $this->model->linkedReels();
            $weights = $this->model->renderWeights($linkedReels);
            $checkCountMoney = $this->model->checkCountMoney($minWin, $maxWin, $weights, $betDenomination, $betBetLevel, $betPrice, $bigWinResult, $i);
            if ($checkCountMoney == true) {
                break;
            }
        }
        $renderWin = $this->model->renderWin($weights);
        $positionsWin = $this->model->positionsWin($renderWin);
        $winCoins = $this->model->winCoins($renderWin, $betDenomination, $betBetLevel);
        $winMoney = ($winCoins['totalwin.cent'] / 100);
        $balanceUser = $user['balance'] + $winMoney - $betPrice;


        $credit = $this->moneyManager->convertBalance($balanceUser);
        if ($bigWinResult == true) {
            $this->sql->updateUserTransactionsInit($sessid, ($transactionsInit - $winMoney));
        }
        $this->sql->userSave($user, $balanceUser);

        $spinArray = [
            'credit' => $credit['cash'],
            'clientaction' => $request['action'],
            'gamesoundurl' => $this->gamesoundurl,
            'linkedreels' => $linkedReels,
            'rs.i0.r.i0.syms' => implode(',', $weights[0]),
            'rs.i0.r.i1.syms' => implode(',', $weights[1]),
            'rs.i0.r.i2.syms' => implode(',', $weights[2]),
            'rs.i0.r.i3.syms' => implode(',', $weights[3]),
            'rs.i0.r.i4.syms' => implode(',', $weights[4]),

            'rs.i0.id' => 'basic',
            'rs.i0.r.i0.id' => 'basic-0',
            'rs.i0.r.i1.id' => 'basic-1',
            'rs.i0.r.i2.id' => 'twinreel-2-3',
            'rs.i0.r.i3.id' => 'twinreel-2-3',
            'rs.i0.r.i4.id' => 'basic-4',

//            'rs.i0.r.i0.pos' => '44',
//            'rs.i0.r.i1.pos' => '11',
//            'rs.i0.r.i2.pos' => '54',
//            'rs.i0.r.i3.pos' => '54',
//            'rs.i0.r.i4.pos' => '38',

            'rs.i0.r.i0.hold' => 'false',
            'rs.i0.r.i1.hold' => 'false',
            'rs.i0.r.i2.hold' => 'false',
            'rs.i0.r.i3.hold' => 'false',
            'rs.i0.r.i4.hold' => 'false',

            'historybutton' => 'false',
            'multiplier' => '1',
            'nextaction' => 'spin',
            'isJackpotWin' => 'false',
            'jackpotcurrencyiso' => 'EUR',
            'jackpotcurrency' => '€',
            'playforfun' => 'true',
            'playercurrency' => '€',
            'playercurrencyiso' => 'EUR',
            'g4mode' => 'false',
            'gamestate.history' => 'basic',
            'gamestate.current' => 'basic',
            'gameover' => 'true',
            'gamestate.stack' => 'basic',
            'wavecount' => '1',

        ];

        $response = array_merge($positionsWin, $spinArray, $winCoins);

        $saveTransaction = [
            'uid' => $user['uid'],
            'sessid' => $sessid,
            'action' => $request['action'],
            'bet' => $betPrice,
            'betlevel' => $betBetLevel,
            'playerBalanceCents' => ($balanceUser * 100),
            'playerBalanceCoins' => $this->moneyManager->convertBalance($balanceUser, $betDenomination)['coins'],
            'denomination' => $betDenomination,
            'preCombination' => json_encode($weights),
            'totalWinCents' => $winCoins['totalwin.cent'],
            'totalWinCoins' => $winCoins['totalwin.coins'],
            'balance' => $balanceUser,
            'calculBigWin' => 0
        ];

        if ($this->sql->saveTransaction($saveTransaction) === FALSE) {
            $this->log->e($request['action'] . ' transaction was not saved');
            die();
        }
        return print(urldecode(http_build_query($response)));
    }

    public function reloadBalance($request)
    {
        $user = $this->sql->getUserData($request['sessid']);

        if ($user === FALSE) {
            $this->log->e($request['action'] . ' user is false');
            die();
        }
        $balance = $this->moneyManager->convertBalance($user['balance']);

        $response = [
            'playercurrencyiso' => 'EUR',
            'clientaction' => $request['action'],
            'playercurrency' => '€',
            'credit' => $balance
        ];
        return print(urldecode(http_build_query($response)));
    }
    /*public function checkCountMoney($request)
    {
        $user = $this->sql->getUserData($request['sessid']);

        if ($user === FALSE) {
            $this->log->e($request['action'] . ' user is false');
            die();
        }
        $balance = $this->moneyManager->convertBalance($user['balance']);

        $response = [
            'playercurrencyiso' => 'EUR',
            'clientaction' => $request['action'],
            'playercurrency' => '€',
            'credit' => $balance
        ];
        return print(urldecode(http_build_query($response)));
    }*/
}