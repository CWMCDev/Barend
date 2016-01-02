<?php
class Portal {

  public static $cookiestr = '';
  
  public static $replaces = array(' (Oorspronkelijk)', "\r\n");

  public static function login($user='', $password='') {
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
    if(strpos($curl, 'Inloggegevens onjuist') != 0) return false;
    preg_match('/^Set-Cookie:\s*([^;]*)/mi', $curl, $cookies);
    parse_str($cookies[1], $cookies);

    $cookiestr = '';
    foreach($cookies as $key=>$val) $cookiestr .= "$key=$val; ";
    self::$cookiestr = $cookiestr;

    setcookie("portal", $cookiestr, time() + (86400 * 30), "/"); // 86400 = 1 day

    return true;
  }
  
  public static function parseGrades($page){
    $classes = array();
    
    $html = str_get_dom($page);
    $table = $html('table tbody tr');
    
    $i = 0;
    foreach($table as $subjectRow) {
      foreach($subjectRow('td.vak span') as $vak){
        $classes[$i] = array('line'=>$i, 'title'=>$vak->title, 'text'=>$vak->getPlainText(), 'grades'=>array());
        $i++;
      }
    }
    
    $gi = 0;
    foreach ($table as $tr) {
      $tds = array();
      $sp = 0;
      $parsedArray = $tr('td div div span a');

      foreach ($parsedArray as $decodedInformation) {
        $gradesInformation = array();

        $gradesDom = str_get_dom(htmlspecialchars_decode($decodedInformation->getAttribute('rel')));

        foreach ($gradesDom('div table tr') as $informationRow) {
          error_log($informationRow);
          $informationData = $informationRow('td');
          $gradesInformation[$informationData[0]->getPlainText()] = $informationData[2]->getPlainText();
        }

        $tds[$sp] = $gradesInformation;
        $sp++;
      }


      $classes[$gi]['grades'] = $tds;
      $gi++;
    }

    $return = array();
    foreach($classes as $c)
     $return[] = $c;
   return $return;
 }

 public static function getGrades($periode) {
  curl::get('https://leerlingen.candea.nl/Portaal/Cijferlijst/Examendossier/Cijferlijst?wis_ajax&ajax_object=727&periode727='.$periode, array(CURLOPT_COOKIE=>self::$cookiestr, CURLOPT_FOLLOWLOCATION=>1, CURLOPT_SSL_VERIFYPEER=>false, CURLOPT_TIMEOUT=>6));
  return array(
   'periode'=>$periode,
   'classes'=>self::parseGrades(curl::get('https://leerlingen.candea.nl/Portaal/Cijferlijst/Examendossier/Cijferlijst?wis_ajax&ajax_object=727&periode727='.$periode, array(CURLOPT_COOKIE=>self::$cookiestr, CURLOPT_FOLLOWLOCATION=>1, CURLOPT_SSL_VERIFYPEER=>false, CURLOPT_TIMEOUT=>6)))
   );
}

public static function parsePresention($page){
  $presentie = array();

  $html = str_get_dom($page);
  $table = $html('table.wp3-presentie-table tbody tr');

  $a = 1;
  foreach($table as $tr) {
    $week = $tr('th', 0)->getPlainText();
    $presentie[$a] = array('week'=>$week, 'dagen'=>array());

    
    $dagen = array('Maandag'=>array(), 'Dinsdag'=>array(), 'Woensdag'=>array(), 'Donderdag'=>array(), 'Vrijdag'=>array());
    $dagenNamen = array('Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag');
    
    $uren = array();

    $i = 0;
    $dag = 0;
    foreach($tr('td') as $hour) {

     $class = str_replace("last-of-week","",$hour->class);
     $class = str_replace(" ","",$class);

     if ($class == 'melding-only' || $class == 'geoorlafw') {
      $uren[$i] = array('hour'=>($i+1), 'status'=>$class, 'reason'=>$hour->getPlainText(), 'title'=>$hour->title);
    } else {
      $uren[$i] = array('hour'=>($i+1), 'status'=>$class);
    }


    if ($i == 9) {
     $dagen[$dagenNamen[$dag]] = $uren;
     $i = 0;
     $dag++;
     $uren = array();
   } else {
     $i++;	
   }

 }
 $presentie[$a]['dagen'] = $dagen;
 $a++;
}

$return = array();
foreach($presentie as $c)
  $return[] = $c;
return $return;
}

public static function getPresention() {
  curl::get('https://leerlingen.candea.nl/Portaal/Presentie/Presentie?wis_ajax&ajax_object=807', array(CURLOPT_COOKIE=>self::$cookiestr, CURLOPT_FOLLOWLOCATION=>1, CURLOPT_SSL_VERIFYPEER=>false, CURLOPT_TIMEOUT=>6));
  return array(
    'presentie'=>self::parsePresention(curl::get('https://leerlingen.candea.nl/Portaal/Presentie/Presentie?wis_ajax&ajax_object=807', array(CURLOPT_COOKIE=>self::$cookiestr, CURLOPT_FOLLOWLOCATION=>1, CURLOPT_SSL_VERIFYPEER=>false, CURLOPT_TIMEOUT=>6)))
    );
}

public static function parseClassList($page) {
  $html = str_get_dom($page);
  $names = $html('ul li div.wp3-profile-person div');
  $ids = $html('ul li div.wp3-profile-pic div');

  $return = array();
  for ($x = 0; $x < count($names); $x++) {
    $return[] = array("name"=>$names[$x]->getPlainText(), "id"=>str_replace("pasfoto_","",$ids[$x]->id));
  }
  
  return $return;
}

public static function getClassList() {
  // first page
  curl::get('https://leerlingen.candea.nl/Portaal/Presentie/Presentie?wis_ajax&ajax_object=724', array(CURLOPT_COOKIE=>self::$cookiestr, CURLOPT_FOLLOWLOCATION=>1, CURLOPT_SSL_VERIFYPEER=>false, CURLOPT_TIMEOUT=>6));
  $html = str_get_dom(curl::get('https://leerlingen.candea.nl/Portaal/Presentie/Presentie?wis_ajax&ajax_object=724', array(CURLOPT_COOKIE=>self::$cookiestr, CURLOPT_FOLLOWLOCATION=>1, CURLOPT_SSL_VERIFYPEER=>false, CURLOPT_TIMEOUT=>6)));
  $countString = $html('strong')[0]->getPlainText();
  $countArray = explode(" ", $countString);
  $maxStudentCount = floatval($countArray[4]);
  $studentPerPage = floatval($countArray[2]);
  $pageCount = ceil($maxStudentCount / $studentPerPage);
  
  $classList = array();
  for ($x = 0; $x < $pageCount; $x++) {
    $pageStart = $x*8 + 1;
    curl::get('https://leerlingen.candea.nl/Portaal/Presentie/Presentie?wis_ajax&ajax_object=724&start724='.$pageStart, array(CURLOPT_COOKIE=>self::$cookiestr, CURLOPT_FOLLOWLOCATION=>1, CURLOPT_SSL_VERIFYPEER=>false, CURLOPT_TIMEOUT=>6));
    $currentClassList = self::parseClassList(curl::get('https://leerlingen.candea.nl/Portaal/Presentie/Presentie?wis_ajax&ajax_object=724&start724='.$pageStart, array(CURLOPT_COOKIE=>self::$cookiestr, CURLOPT_FOLLOWLOCATION=>1, CURLOPT_SSL_VERIFYPEER=>false, CURLOPT_TIMEOUT=>6)));
    $classList = array_merge($classList, $currentClassList);
  } 
  
  return array ("classList"=>$classList);
}

public static function parseProfile($page) {
  $html = str_get_dom($page);
  $name = $html('table');

  $data = array('student'=>array('name'=>$name[0]('tr td')[0]->getPlainText(), 'studentnumber'=>$name[0]('tr td')[2]->getPlainText(), 'class'=>$name[0]('tr td')[4]->getPlainText(), 
               'birthdate'=>$name[1]('tr td')[1]->getPlainText(), 'phonenumbers'=>array('home'=>$name[1]('tr td')[3]->getPlainText(), 'mobile'=>$name[1]('tr td')[5]->getPlainText())), 
               'adress'=>array('street'=>$name[2]('tr td')[2]->getPlainText(), 'zipcode'=>$name[2]('tr td')[4]->getPlainText(), 'place'=>$name[2]('tr td')[6]->getPlainText()), 
               'mentor'=>array('name'=>$name[3]('tr td')[2]->getPlainText(), 'abbreviation'=>$name[3]('tr td')[4]->getPlainText(), 'email'=>$name[3]('tr td')[6]->getPlainText()), 
               'Profile'=>array('profile'=>$name[4]('tr td')[2]->getPlainText(), 'code'=>$name[4]('tr td')[4]->getPlainText(), 'abbreviation'=>$name[4]('tr td')[6]->getPlainText(), 'year'=>$name[4]('tr td')[8]->getPlainText()));
  
  return $data;
}

public static function getProfile() {
  curl::get('https://leerlingen.candea.nl/Portaal?wis_ajax&ajax_object=1292', array(CURLOPT_COOKIE=>self::$cookiestr, CURLOPT_FOLLOWLOCATION=>1, CURLOPT_SSL_VERIFYPEER=>false, CURLOPT_TIMEOUT=>6));
  return self::parseProfile(curl::get('https://leerlingen.candea.nl/Portaal?wis_ajax&ajax_object=1292', array(CURLOPT_COOKIE=>self::$cookiestr, CURLOPT_FOLLOWLOCATION=>1, CURLOPT_SSL_VERIFYPEER=>false, CURLOPT_TIMEOUT=>6)));
}

}
?>
