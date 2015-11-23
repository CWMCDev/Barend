<?php
require 'Slim/Slim.php';
require 'classes/curl.php';
include('classes/ganon.php');


require 'modules/portal/portal_student.php';

function createResponse($data=array()) {
	if(isset($_GET['format']) && $_GET['format'] == 'xml') {
		$array = array('data'=>$data);
		$xml = new SimpleXMLElement('<response/>');
		array_walk_recursive($array, array ($xml, 'addChild'));
		print $xml->asXML();
	} else {
		print json_encode($data, JSON_PRETTY_PRINT);
	}
}

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
    
    createResponse($portal->getGrades(1));
});

$app->get('/portal/students/presention', function () {
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
    
    $portal->getPresention();
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
