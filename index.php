<?php
require 'Slim/Slim.php';
require 'classes/curl.php';

require 'modules/portal/portal_students.php';

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

$app->get('/portal/students/grades/:user/:pass', function ($user, $pass) {
    $app = \Slim\Slim::getInstance();
    
    if (isset($_GET['username']) && isset($_GET['password'])) {
      $pass = $_GET['password'];
      $user = $_GET['username'];
    }
    
    if($user == '' || $pass == '') {
     $app->halt(401, 'Please set username and password first');
    }
    
    $portal = new Portal();
    $portal->login($user, $pass);
    
    $portal->getGrades();
});

$app->get('/portal/students/presention', function () {
    echo "Hello, ";
});

$app->get('/portal/students/profile', function () {
    echo "Hello, ";
});

$app->get('/portal/students/class', function () {
    echo "";
});

$app->get('/portal/students', function () {
    echo "";
});

$app->run();
?>