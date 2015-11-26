<?php
require 'Slim/Slim.php';
require 'classes/curl.php';
include('classes/ganon.php');

require 'modules/portal/portal_student.php';
require 'modules/zportal/zportal_main.php';

function prettyPrint( $json )
{
    $result = '';
    $level = 0;
    $in_quotes = false;
    $in_escape = false;
    $ends_line_level = NULL;
    $json_length = strlen( $json );

    for( $i = 0; $i < $json_length; $i++ ) {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        if( $ends_line_level !== NULL ) {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
        if ( $in_escape ) {
            $in_escape = false;
        } else if( $char === '"' ) {
            $in_quotes = !$in_quotes;
        } else if( ! $in_quotes ) {
            switch( $char ) {
                case '}': case ']':
                    $level--;
                    $ends_line_level = NULL;
                    $new_line_level = $level;
                    break;

                case '{': case '[':
                    $level++;
                case ',':
                    $ends_line_level = $level;
                    break;

                case ':':
                    $post = " ";
                    break;

                case " ": case "\t": case "\n": case "\r":
                    $char = "";
                    $ends_line_level = $new_line_level;
                    $new_line_level = NULL;
                    break;
            }
        } else if ( $char === '\\' ) {
            $in_escape = true;
        }
        if( $new_line_level !== NULL ) {
            $result .= "\n".str_repeat( "\t", $new_line_level );
        }
        $result .= $char.$post;
    }

    return $result;
}

function createResponse($data=array()) {
	if(isset($_GET['format']) && $_GET['format'] == 'xml') {
		$array = array('data'=>$data);
		$xml = new SimpleXMLElement('<response/>');
		array_walk_recursive($array, array ($xml, 'addChild'));
		print $xml->asXML();
	} else {
		print prettyPrint(json_encode($data));
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
    if($portal->login($user, $pass)) {
    	createResponse($portal->getGrades(1));
    } else {
    	$app->halt(401, 'Wrong Password or Username!');
    }
});

$app->get('/portal/students/presention/:user/:pass', function ($user, $pass) {
   $app = \Slim\Slim::getInstance();
    
    if (isset($_GET['username']) && isset($_GET['password'])) {
      $pass = $_GET['password'];
      $user = $_GET['username'];
    }
    
    if($user == '' || $pass == '') {
     $app->halt(401, 'Please set username and password first');
    }
    
    $portal = new Portal();
    if($portal->login($user, $pass)) {
    	createResponse($portal->getPresention());
    } else {
    	$app->halt(401, 'Wrong Password or Username!');
    }
});

$app->get('/zportal/settoken/:key', function($key) use($app) {
	$zportal = new Zportal();
	$zportal->setAppKey($key);
	if($zportal->getToken()) {
		setcookie('ztoken', $zportal->token, time()+31536000, "/");
		createResponse([
			'token' => $zportal->token
		]);
	} else {
		$app->halt(403, json_encode(['error'=>'Deze code is niet correct']));
	}
});

$app->get('/zportal/schedule/:week', function($week) use($app) {
	$token = '';
	if(isset($_GET['token'])) $token = $_GET['token'];
	elseif(isset($_COOKIE['ztoken'])) $token = $_COOKIE['ztoken'];

	if($token == '') {
		$app->halt(401, 'Please set the token first');
	}

	if($week == 0) 
		$week = date('W');
	$zportal = new Zportal();
	$zportal->setToken($token);
	$schedule = $zportal->getSchedule($week);
	if($schedule->response->status != 200) {
		if($schedule->response->status == 401)
			$app->halt(401, 'The token is incorrect');

		$app->halt(500, $schedule->response->message);
	}
	createResponse($schedule->response->data);
});

$app->run();
?>
