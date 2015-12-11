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

$app->('/portal/login/:user/:pass', function ($user, $pass) use($app) {
	if($portal->login($user, $pass)) {
    	echo "login succesfull";
    } else {
    	$app->halt(401, json_encode(['error' => 'Wrong Password or Username!']));
    }
});

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
$app->get('/portal/students/classlist/:user/:pass', function ($user, $pass) use($app) {
    $portal = new Portal();
    if($portal->login($user, $pass)) {
    	createResponse($portal->getClassList());
    } else {
    	$app->halt(401, json_encode(['error' => 'Wrong Password or Username!']));
    }
});
$app->get('/portal/students/presention/:user/:pass', function ($user, $pass) use($app) {
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
$app->get('/zportal/schedule/:week/:token/:user/:pass', function($week, $token, $user, $pass) use($app) {
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
    
    $integrater = new integrate();
    
    $portal = new Portal();
    if($portal->login($user, $pass)) {
      createResponse($integrater::addPresention($scheduleData, $portal->getPresention(), $week));
    } else {
    	$app->halt(401, json_encode(['error' => 'Wrong Password or Username!']));
    }
    //createResponse($scheduleData);
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
    
    if(isset($_COOKIE["portal"])) {
    	$portal = new Portal();
    	$portal::$cookiestr = $_COOKIE["portal"];
    	
    	$integrater = new integrate();
    	createResponse($integrater::addPresention($scheduleData, $portal->getPresention(), $week));
	} else {
	  	createResponse($scheduleData);
	}
});

$app->get('/test', function() use($app) {
	$app->halt(403, json_encode(['error' => "This endpoint is just for debugging"]));
});

$app->run();
?>
