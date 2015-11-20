<?php
require 'vendor/autoload.php';

// initialize slim app
$app = new \Slim\Slim();

// define all routes
$app->get('/portal', function () {
    echo readfile('views/portal.html');
});

$app->get('/portal/students/grades', function () {
    echo "Hello, ";
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