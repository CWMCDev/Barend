<?php
class Itslearning {
  public static $url = "https://candea.itslearning.com";
  public static $cookiestr = '';
  public static $useragent = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.2 (KHTML, like Gecko) Chrome/5.0.342.3 Safari/533.2';

  public static function login($username, $password) {

        $f = fopen('log.txt', 'w'); // file to write request header for debug purpose

<<<<<<< HEAD
        /**
            Get __VIEWSTATE & __EVENTVALIDATION
         */
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url.'/index.aspx');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
      
        $pre_login = curl_exec($ch);
=======
     /**
        Get __VIEWSTATE & __EVENTVALIDATION
     */
    $ch = curl_init(self::$url.'/index.aspx');
    curl_setopt($ch, CURLOPT_COOKIEJAR, self::$cookiestr);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, self::$useragent);
>>>>>>> bc2250a55457f8514dc2fc18780b31b976590345
  
        curl_close($ch);
  
        preg_match('~<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*?)" />~', $pre_login, $viewstate);
        preg_match('~<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="(.*?)" />~', $pre_login, $eventValidation);

<<<<<<< HEAD
        $viewstate = $viewstate[1];
        $eventValidation = $eventValidation[1];
  
        /**
        Start Login process
        */
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url.'/index.aspx');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $ckfile);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_STDERR, $f);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
      
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
        
        // Alle Vakken
        curl_setopt($ch, CURLOPT_URL, $url.'/Course/AllCourses.aspx');
        //td a.ccl-iconlink
        
        $test = curl_exec($ch); // Get result after login page.
        
        $html = str_get_dom($test);
        $table = $html('div.itsl-formbox div div div table tr');
        
        echo 'Vakken + Id:';
        foreach($table as $subject) {
        if ($subject->class == "ct126_0"){}
        else {
            foreach($subject('td a') as $a) {
                if ($a->class == 'ccl-iconlink') {
                    echo '<br>';
                    $id = str_replace("/main.aspx?CourseID=","",$a->href);
                    echo $a->getPlainText().'   :   '.$id;
                
                    curl_setopt($ch, CURLOPT_URL, $url.'/Course/course.aspx?CourseId='.$id);
        
                    $test1 = curl_exec($ch); // Get result after login page.
                    $test2 = str_get_dom($test1);
                    $DTDL = $test2('div.extension a');
                    echo '<br>digitaal materiaal:';
                    foreach($DTDL as $link) {
                        echo '<br>'.$link->href;
                    }
                
                    echo '<br>';
                
                    $imf = $test2('li.ilw-filesblock-li a');
                    echo '<br>Belangrijke bestanden:';
                    foreach($imf as $if) {
                        echo '<br>'.$if->getPlainText().': '.$url.$if->href;
                    }
                
                    echo '<br>';
                
                    $mdden = $test2('li.itsl-cb-news ul.itsl-widget-content-ul li');
                    if (count($mdden) != 0) {
                        echo '<br>Mededelingen:';
                        foreach($mdden as $mdd) {
                            echo '<br>'.$mdd->getPlainText().'<br>';
                        }
                    }
                    echo '<br>----------------------------------------*----------------------------------------';
                }

            }
        
        }
    }
    }
=======
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
  
    curl_setopt($ch, CURLOPT_URL, self::$url.'/AllCourses.aspx');
    
    $test = curl_exec($ch); // Get result after login page
    print $test;
    
    curl_close($ch);
    //main.aspx?TextURL=Course%2fAllCourses.aspx&Item=l-menu-course
  }
>>>>>>> bc2250a55457f8514dc2fc18780b31b976590345
}
?>
