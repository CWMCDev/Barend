<?php
  public static $cookiestr = '';
	public static $url_students = 'https://leerlingen.candea.nl';
	public static $url_parents = 'https://ouders.candea.nl';
	public static $url_teachers = 'https://personeel.candea.nl';

  public static function login($url='', $user='', $password='') {
		$logindata = array(
			'wu_loginname' => urlencode($user),
			'wu_password' => urlencode($password),
			'Login' => urlencode('Inloggen'),
		);
  
    $url=$url_students;

		$curl = curl::post($url.'/Login?passAction=login&path=%2F', $logindata, array(CURLOPT_HEADER=>1, CURLOPT_FOLLOWLOCATION=>1, CURLOPT_SSL_VERIFYPEER=>false));
		if(strpos($curl, 'Inloggegevens onjuist') != 0) return false;
		preg_match('/^Set-Cookie:\s*([^;]*)/mi', $curl, $cookies);
		parse_str($cookies[1], $cookies);

		$cookiestr = '';
		foreach($cookies as $key=>$val) $cookiestr .= "$key=$val; ";
		self::$cookiestr = $cookiestr;

    echo SUCCES!;
      
		return true;
  }
?>