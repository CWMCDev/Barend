<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'Slim/Slim.php';
require 'classes/curl.php';
require 'classes/ganon.php';
require 'modules/portal/portal_student.php';
require 'modules/zportal/zportal_main.php';
require 'modules/utilities/parseTime.php';

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

$app->get('/portal/students/grades/:user/:pass', function ($user, $pass) use($app) {
    if (isset($_GET['username']) && isset($_GET['password'])) {
      $pass = $_GET['password'];
      $user = $_GET['username'];
    }
    
    if(empty($user) || empty($pass)) {
     $app->halt(401, json_encode(['error' => 'Please set username and password first']));
    }
    
    $portal = new Portal();
    if($portal->login($user, $pass)) {
    	createResponse($portal->getGrades(1));
    } else {
    	$app->halt(401, json_encode(['error' => 'Wrong Password or Username!']));
    }
});
$app->get('/portal/students/presention/:user/:pass', function ($user, $pass) use($app) {
    if (isset($_GET['username']) && isset($_GET['password'])) {
      $pass = $_GET['password'];
      $user = $_GET['username'];
    }
    
    if(empty($user) || empty($pass)) {
     $app->halt(401, json_encode(['error' => 'Please set username and password first']));
    }
    
    $portal = new Portal();
    if($portal->login($user, $pass)) {
    	createResponse($portal->getPresention());
    } else {
    	$app->halt(401, json_encode(['error' => 'Wrong Password or Username!']));
    }
});
$app->get('/zportal/settoken/:key', function($key) use($app) {
	$zportal = new Zportal();
	$zportal->setAppKey($key);
	if($zportal->getToken()) {
		createResponse([
			'token' => $zportal->token
		]);
	} else {
		$app->halt(403, json_encode(['error'=>'The used code is invalid']));
	}
});
$app->get('/zportal/schedule/:week/:token', function($week, $token) use($app) {
	if($week == 0) {
		$week = date('W');
	}
	$zportal = new Zportal();
	$zportal->setToken($token);
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
    
    $schedule = array();
    $timeParser = new parseTime();
    foreach($scheduleData as $lesson) {
      $day = $timeParser::getTime($lesson->start);
    
      $lesson.push({
        key:   "day",
        value: $day
      });
      
      $schedule[] = $lesson;
    }
    createResponse($schedule);
});


$app->get('/test', function() use($app) {
	$app->halt(403, json_encode(['error' => "This endpoint is just for debugging"]));
});

$app->run();
?>
