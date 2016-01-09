<?php
    require 'databasecomm.php';

    $salt = "sVnf80XWaM87JU58hmshi0P43fW0ZPqvkNMcG8hb8GhEpa0IkcXQ5mjdblud6QAPw8dxIDlZzHE6zPbVqdwgnokIjYsxwHsSvpwN";

    function crypto_rand_secure($min, $max){
       $range = $max - $min;
    	if ($range < 1) return $min; // not so random...
    	$log = ceil(log($range, 2));
    	$bytes = (int) ($log / 8) + 1; // length in bytes
    	$bits = (int) $log + 1; // length in bits
    	$filter = (int) (1 << $bits) - 1; // set all lower bits to 1
    	do {
           $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
        	$rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
    }

    function generateToken($length){
    	$token = "";
    	$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    	$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    	$codeAlphabet.= "0123456789";
    	$max = strlen($codeAlphabet) - 1;
    	for ($i=0; $i < $length; $i++) {
           $token .= $codeAlphabet[crypto_rand_secure(0, $max)];
       }
       return $token;
    }

    function loginPortal($user='', $password='') {
        if (substr($user,0,2) !== 'cc'){
          $user = "cc" . $user;
          error_log($user . " , " . substr($user,0,2) );
        }

        $logindata = array(
         'wu_loginname' => urlencode($user),
         'wu_password' => urlencode($password),
         'Login' => urlencode('Inloggen'),
         );

        $curl = curl::post('https://leerlingen.candea.nl/Login?passAction=login&path=%2F', $logindata, array(CURLOPT_HEADER=>1, CURLOPT_FOLLOWLOCATION=>1, CURLOPT_SSL_VERIFYPEER=>false));
        if(strpos($curl, 'Inloggegevens onjuist') != 0 || strpos($curl, 'U heeft geen rechten') != 0){
            return false;
        }

        return true;
    }

    function checkAuth($username, $token){
        if(empty($token)){
            return ['error' => 'Empty Token'];
        }
        $password = getPassword($username, $token);

        if($password == false){
            echo "False Password";
            return ['error' => 'Invalid Token'];
        }

        if(loginPortal($username, $password)) {
            return true;
        }else{
            return ['error' => 'Invalid Password'];
        }
    }

    function registerUser($username, $password){
        global $salt;
        $hash = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($salt), $password, MCRYPT_MODE_CBC, md5(md5($salt))));
        insertUser($username, $hash);
    }

    function getPassword($username, $token){
        if(getTokenValid($username, $token)){
            global $salt;
            $hash = getHash($username);
            $password = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($salt), base64_decode($hash), MCRYPT_MODE_CBC, md5(md5($salt))), "\0");
            return $password;
        } else {
            return false;
        }
    }

    function updatePassword($username, $password){
        global $salt;
        $hash = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($salt), $password, MCRYPT_MODE_CBC, md5(md5($salt))));
        updateHash($username, $hash);
    }

    function requestToken($username, $password, $description = null){
        if(loginPortal($username, $password)) {
            if(!getUser($username)){
                registerUser($username, $password);
            }

            $token = generateToken(256);

            if(insertToken($username, $token, $description)) {
                return $token;
            } else {
                return false;
            }
        } else {
            // Incorrect login
            return false;
        }
    }
?>