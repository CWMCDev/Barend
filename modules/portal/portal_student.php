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
  return array(
    'presentie'=>self::parsePresention(curl::get('https://leerlingen.candea.nl/Portaal/Presentie/Presentie?wis_ajax&ajax_object=807', array(CURLOPT_COOKIE=>self::$cookiestr, CURLOPT_FOLLOWLOCATION=>1, CURLOPT_SSL_VERIFYPEER=>false, CURLOPT_TIMEOUT=>6)))
    );
}

}
?>
