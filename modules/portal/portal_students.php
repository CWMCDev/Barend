<?php
class Portal {
 
  public static $cookiestr = '';
 
  public static function login($user='', $password='') {
		$logindata = array(
			'wu_loginname' => urlencode($user),
			'wu_password' => urlencode($password),
			'Login' => urlencode('Inloggen'),
		);

		$curl = curl::post('https://leerlingen.candea.nl/Login?passAction=login&path=%2F', $logindata, array(CURLOPT_HEADER=>1, CURLOPT_FOLLOWLOCATION=>1, CURLOPT_SSL_VERIFYPEER=>false));
		if(strpos($curl, 'Inloggegevens onjuist') != 0) return false;
		preg_match('/^Set-Cookie:\s*([^;]*)/mi', $curl, $cookies);
		parse_str($cookies[1], $cookies);

		$cookiestr = '';
		foreach($cookies as $key=>$val) $cookiestr .= "$key=$val; ";
		self::$cookiestr = $cookiestr;

    echo 'GG';
    
		return true;
	}
  
  
}
?>