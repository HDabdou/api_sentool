<?php 

ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL);


define('DIR',__DIR__);


require DIR . '/vendor/autoload.php';

session_start();

require DIR .'/config/default.php';

$app = new \Slim\App($config);

require DIR . '/app/dependencies.php';
require DIR . '/app/routes.php';


$app->run();


?>