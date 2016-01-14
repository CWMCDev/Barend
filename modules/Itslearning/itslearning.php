<?php
class Itslearning {
  public static function login($username, $password) {
  $url = "https://candea.itslearning.com";
  $ckfile = tempnam("/tmp", "CURLCOOKIE");
  $useragent = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.2 (KHTML, like Gecko) Chrome/5.0.342.3 Safari/533.2';

    $f = fopen('log.txt', 'w'); // file to write request header for debug purpose

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
  
    curl_close($ch);
  
    preg_match('~<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*?)" />~', $pre_login, $viewstate);
    preg_match('~<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="(.*?)" />~', $pre_login, $eventValidation);

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
    
    return $ch;
    /*
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
    */
  }
  
  public static getSubjects($user, $password) {
    $ch = self::login($user, $password);
    
    curl_setopt($ch, CURLOPT_URL, $url.'/Course/AllCourses.aspx');
    $subjectCurl = curl_exec($ch);
    
    $html = str_get_dom($subjectCurl);
    $subjects = $html('div.itsl-formbox div div div table tr');
    
    $return = array(subjects=>array());
    foreach($subjects as $subject) {
      if ($subject->class == "ct126_0"){}
      else {
        foreach($subject('td a') as $a) {
          if ($a->class == 'ccl-iconlink') {
            echo '<br>';
            $id = str_replace("/main.aspx?CourseID=","",$a->href);
            echo $a->getPlainText().'   :   '.$id;
          }
        }
      }
    }
  }
}
?>
