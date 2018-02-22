<?php
require_once 'actions.php';
require_once 'logger.php';

ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE);

class Server
{
    private $Actions;
    private $log;

    function __construct()
    {
        $this->Actions = new Actions;
        $this->log = new Logger;
    }

    public function actions()
    {
        if (!$_REQUEST['action'] || !$_REQUEST['sessid']) {
            $this->log->e('Not sessid or action (server.php)');
            die();
        }
        switch ($_REQUEST['action']) {
            case ('init'):
                $this->Actions->init($_REQUEST);
                break;
            case ('paytable'):
                $this->Actions->payTable($_REQUEST);
                break;
            case ('spin'):
                $this->Actions->spin($_REQUEST);
                break;
            case ('reloadbalance'):
                $this->Actions->reloadBalance($_REQUEST);
                break;
        }
    }
}

$server = new Server();
$server->actions();