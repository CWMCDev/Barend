<?php
require 'vendor/autoload.php';
require 'modules/portal/portal_students.php';

// initialize slim app
$app = new \Slim\Slim();

// define all routes
$app->get('/portal', function () {
    echo file_get_contents('views/portal.html');
});

$app->get('/portal/students/grades', function () {
    $pass = '';
    $user = '';
    
    if (isset($_GET['username']) && isset($_GET['password'])) {
      $pass = $_GET['password'];
      $user = $_GET['username'];
    }
    
    if($user == '' || $pass == '') {
      echo '401, Please set username and password first';
    }
    
    $portal = new portal_student();
    $portal->login($user, $pass);
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