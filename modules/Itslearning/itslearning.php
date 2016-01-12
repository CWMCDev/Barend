<?php
class Itslearning {
  public static $url = "https://candea.itslearning.com";
  public static $cookiestr = '';
  public static $useragent = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.2 (KHTML, like Gecko) Chrome/5.0.342.3 Safari/533.2';

  public static function login($username, $password) {

    $f = fopen('log.txt', 'w'); // file to write request header for debug purpose

     /**
        Get __VIEWSTATE & __EVENTVALIDATION
     */
    $ch = curl_init(self::$url.'/index.aspx');
    curl_setopt($ch, CURLOPT_COOKIEJAR, self::$cookiestr);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, self::$useragent);
  
    $html = curl_exec($ch);
  
    curl_close($ch);
  
    preg_match('~<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*?)" />~', $html, $viewstate);
    preg_match('~<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="(.*?)" />~', $html, $eventValidation);

    $viewstate = $viewstate[1];
    $eventValidation = $eventValidation[1];
  
  
  
    /**
      Start Login process
    */
    $ch = curl_init();
  
    curl_setopt($ch, CURLOPT_URL, self::$url.'/index.aspx');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
    curl_setopt($ch, CURLOPT_COOKIEJAR, self::$cookiestr);
    curl_setopt($ch, CURLOPT_COOKIEFILE, self::$cookiestr);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_REFERER, self::$url);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_STDERR, $f);
    curl_setopt($ch, CURLOPT_USERAGENT, self::$useragent);
  
    // Collecting all POST fields
    $postfields = array();
    $postfields['__EVENTTARGET'] = "";
    $postfields['__EVENTARGUMENT'] = "";
    $postfields['__VIEWSTATE'] = $viewstate;
    $postfields['__EVENTVALIDATION'] = $eventValidation;
    $postfields['ctl00$ContentPlaceHolder1$Username$input'] = $username;
    $postfields['ctl00$ContentPlaceHolder1$Password$input'] = $password;
    $postfields['ctl00$ContentPlaceHolder1$nativeLoginButton'] = 'Inloggen voor gast accounts';
  
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    $ret = curl_exec($ch); // Get result after login page.
  
    print $ret;
  
    curl_setopt($ch, CURLOPT_URL, self::$url.'/main.aspx?TextURL=Course%2fAllCourses.aspx&Item=l-menu-course');
    
    $test = curl_exec($ch); // Get result after login page
    print $test;
    
    curl_close($ch);
    //main.aspx?TextURL=Course%2fAllCourses.aspx&Item=l-menu-course
  }
}
?>
