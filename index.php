<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'Slim/Slim.php';
require 'classes/curl.php';
require 'classes/ganon.php';
require 'modules/portal/portal_student.php';
require 'modules/zportal/zportal_main.php';
require 'modules/utilities/parser.php';
require 'modules/utilities/integrate.php';

function createResponse($data=array()) {
	if(isset($_GET['format']) && $_GET['format'] == 'xml') {
		$array = array('data'=>$data);
		$xml = new SimpleXMLElement('<response/>');
		array_walk_recursive($array, array ($xml, 'addChild'));
		echo $xml->asXML();
	} else {
		if(isset($_GET['callback'])) {
			echo $_GET['callback'].'(';
		}	
		echo json_encode($data, JSON_PRETTY_PRINT);
		if(isset($_GET['callback'])) {
			echo ')';
		}
	}
}
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

$app->get('/portal/students/login/:user/:pass', function ($user, $pass) use($app) {
    $portal = new Portal();
    if($portal->login($user, $pass)) {
    	echo "Login Succesfull";
    } else {
    	$app->halt(401, json_encode(['error' => 'Wrong Password or Username!']));
    }
});

$app->get('/portal/students/logout', function () use($app) {
     unset($_COOKIE['portal']);
     setcookie('portal', '', time() - 3600, '/');
     echo "Logout Succesfull";
});

$app->get('/portal/students/grades', function () use($app) {
    $portal = new Portal();
    
    if(!isset($_COOKIE["portal"])) {
      $app->halt(401, json_encode(['error' => 'Please login first']));
    } else {
      $portal->setCookiestr($_COOKIE["portal"]);
      createResponse($portal->getGrades(1));
    } 
});

$app->get('/portal/students/classlist', function () use($app) {
    $portal = new Portal();
    
    if(!isset($_COOKIE["portal"])) {
      $app->halt(401, json_encode(['error' => 'Please login first']));
    } else {
      $portal->setCookiestr($_COOKIE["portal"]);
      createResponse($portal->getClassList());
    } 
});
$app->get('/portal/students/presention', function () use($app) {
    $portal = new Portal();
    
    if(!isset($_COOKIE["portal"])) {
      $app->halt(401, json_encode(['error' => 'Please login first']));
    } else {
      $portal->setCookiestr($_COOKIE["portal"]);
      createResponse($portal->getPresention());
    } 
});

$app->get('/zportal/settoken/:key', function($key) use($app) {
	$zportal = new Zportal();
	$zportal->setAppKey($key);
	if($zportal->getToken()) {
		createResponse([
			'token' => $zportal->token
		]);
    $zportal->setCookie($zportal->token);
	} else {
		$app->halt(403, json_encode(['error'=>'The used code is invalid']));
	}
});

$app->get('/zportal/schedule/:week', function() use($app) {
	if($week == 0) {
		$week = date('W');
	}
  if(!isset($_COOKIE["zportal"])) {
    $app->halt(403, "Please set token first!");
  }
	$zportal = new Zportal();
  $zportal->setToken($_COOKIE["zportal"]);
	$schedule = $zportal->getSchedule($week);
	if($schedule->response->status != 200) {
		if($schedule->response->status == 401) {
			$app->halt(401, json_encode(['error' => 'The token is incorrect']));
		}
		$app->halt(500, json_encode(['error' => $schedule->response->message]));
	}
    $scheduleData = $schedule->response->data;
    
    function cmp($a, $b) {
    	return strcmp($a->start, $b->start);
    }
    usort($scheduleData, "cmp");
    
    if(!isset($_COOKIE["portal"])) {
      createResponse($scheduleData);
    } else {
      $portal = new Portal();
      $portal->setCookiestr($_COOKIE["portal"]);
      
      $integrater = new integrate();
      createResponse($integrater::addPresention($scheduleData, $portal->getPresention(), $week));
    } 
});

$app->get('/test', function() use($app) {
	$app->halt(403, json_encode(['error' => "This endpoint is just for debugging"]));
});

$app->run();
?>
