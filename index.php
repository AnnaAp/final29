<?php
define('MAIN', true);
error_reporting(E_ALL);
ini_set('display_errors', 1);
$config = include 'config.php';
include 'core.php';
//var_dump($_SERVER);
require $_SERVER['DOCUMENT_ROOT'] . (isset($config['pathRoot']) ? $config['pathRoot'] : '') . '/vendor/autoload.php';
spl_autoload_register('libAutoload');

use DataBase\DataBase;
use Router\Router;
use Log\Log;
use \Exceptions\MyException;
use \Handler\Handler;

$handler = new Handler();
$handler->register();

/**
 * Подключение к базе данных
 */
try {
    $db = DataBase::connect(
        $config['mysql']['host'],
        $config['mysql']['dbname'],
        $config['mysql']['user'],
        $config['mysql']['pass']
    );
} catch (Exception $e) {
    error_log($e->__toString());
    Log::error("Подключение к БД не удалось:" . $e->__toString());
    die('Подключение к БД не удалось');
}

try {
    $router = new Router($db, $config['dirProject']);
    $router->run();
} catch (MyException $e) {
    error_log($e->__toString());
    Log::error("router:" . $e->__toString());
    die('router: ' . htmlentities($e->getMessage()) . "<br/>");
}