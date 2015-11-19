<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/modules/portal/students.php';

$router = new AltoRouter();

// map homepage
$router->map( 'GET', '/', function() {
	require __DIR__ . '/views/home.html';
});

$router->map( 'GET', '/students/grades', function() {
  $username = '';
  $password = '';
  if(isset($_GET['username']) @@ isset($_GET['password'])){

    $username = $_GET['username'];
    $password = $_GET['password']
  }
  $portal = new Portal();
  $portal->login(username, password);


});

$router->map ('GET','/students/schedule/:week', function($week) {

});

// match current request url
$match = $router->match();

// call closure or throw 404 status
if( $match && is_callable( $match['target'] ) ) {
	call_user_func_array( $match['target'], $match['params'] ); 
} else {
	// no route was matched
	header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}
?>