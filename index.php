<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'Slim/Slim.php';
require 'classes/curl.php';
require 'classes/ganon.php';
require 'modules/portal/portal_student.php';
require 'modules/mail/mail.php';
require 'modules/zportal/zportal_main.php';
require 'modules/utilities/parser.php';
require 'modules/utilities/integrate.php';
require 'modules/utilities/auth.php';

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

$app->get('/auth/register/:user/:pass', function ($user, $pass) use($app) {
    if (substr($user,0,2) !== 'cc'){
      $user = "cc" . $user;
      error_log($user . " , " . substr($user,0,2) );
    }

    if(empty($user) || empty($pass)) {
     $app->halt(401, json_encode(['error' => 'Please set username and password first']));
    }

    $token = requestToken($user, $pass, "Debug");

    if($token != false){
      $response = array();
      $response["token"] = $token;
      createResponse($response);
    } else {
      $app->halt(401, json_encode(['error' => 'Invalid username or password']));
    }
});

$app->get('/portal/students/profile/:user/:token', function ($user, $token) use($app) {
    $authStatus = checkAuth($user, $token);
    if($authStatus === true){
      $password = getPassword($user, $token);
      $portal = new Portal();
      if($portal->login($user, $password)){
        createResponse($portal->getProfile());
      }
    }else{
      $app->halt(401, json_encode($authStatus));
    }
});
$app->get('/portal/students/grades/:period/:user/:token', function ($period, $user, $token) use($app) {
    $authStatus = checkAuth($user, $token);
    if($authStatus === true){
      $password = getPassword($user, $token);
      $portal = new Portal();
      if($portal->login($user, $password)){
        createResponse($portal->getGrades($period));
      }
    }else{
      $app->halt(401, json_encode($authStatus));
    }
});
$app->get('/portal/students/classlist/:user/:token', function ($user, $token) use($app) {
  $authStatus = checkAuth($user, $token);
  if($authStatus === true){
    $password = getPassword($user, $token);
    $portal = new Portal();
    if($portal->login($user, $password)){
      createResponse($portal->getClassList());
    }
  }else{
    $app->halt(401, json_encode($authStatus));
  }
});
$app->get('/portal/students/presention/:user/:token', function ($user, $token) use($app) {
  $authStatus = checkAuth($user, $token);
  if($authStatus === true){
    $password = getPassword($user, $token);
    $portal = new Portal();
    if($portal->login($user, $password)){
      createResponse($portal->getPresention());
    }
  }else{
    $app->halt(401, json_encode($authStatus));
  }
});

$app->get('/mail/:user/:token', function ($user, $token) use($app) {
  $authStatus = checkAuth($user, $token);
  if($authStatus === true){
    $password = getPassword($user, $token);
    $portal = new Mail();
    createResponse($mail->getMail($user, $password));
  }else{
    $app->halt(401, json_encode($authStatus));
  }
});

// Zportal
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
$app->get('/zportal/schedule/:type/:id/:week/:token', function($type, $id, $week, $token) use($app) {
	if($week == 0) {
		$week = date('W');
	}
	$zportal = new Zportal();
	$zportal->setToken($token);
	$schedule = $zportal->getSchedule($week, $type, $id);
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
    
    createResponse($scheduleData);
});
$app->get('/zportal/schedule/:week/:token/:user/:userToken', function($week, $token, $user, $userToken) use($app) {
  
  
	if($week == 0) {
		$week = date('W');
	}
	$zportal = new Zportal();
	$zportal->setToken($token);
	$schedule = $zportal->getSchedule($week, 'student', 'me');
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
    
    $authStatus = checkAuth($user, $userToken);
    if($authStatus === true){
      $password = getPassword($user, $userToken);
      $integrater = new integrate();
    
      $portal = new Portal();
      if($portal->login($user, $pass)) {
        createResponse($integrater::addPresention($scheduleData, $portal->getPresention(), $week));
      } else {
        $app->halt(401, json_encode(['error' => 'Wrong Password or Username!']));
      }
    }else{
      $app->halt(401, json_encode($authStatus));
    }
    
});

$app->get('/test', function() use($app) {
	$app->halt(403, json_encode(['error' => "This endpoint is just for debugging"]));
});

$app->run();
?>
