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
  
  public function parseGrades($page){
    $classes = array();
		$page = str_get_html($page);
		$i = 1;
		foreach($page->find('ul>li') as $f) {
			if($f->class != 'result__header_left') $classes[$i] = array('line'=>$i, 'title'=>$f->find('span', 0)->title, 'text'=>$f->plaintext, 'grades'=>array());
			$i++;
		}
		$gi = 0;
		$table = $page->find('.result__table', 0);
		foreach($table->find('tr') as $g) {
			foreach($g->find('.cijfer') as $s) {
				$info = str_get_html(html_entity_decode($s->find('a', 0)->rel));
				$tds = array();
				foreach($info->find('tr') as $tr) {
					$tds[str_replace(' ', '_', strtolower($tr->find ('td', 0)->plaintext))] = str_replace(self::$replaces, '', $tr->find ('td', 2)->plaintext);
				}
				$classes[$gi]['grades'][] = $tds;
			}
			$gi++;
		}
		$return = array();
		foreach($classes as $c)
			$return[] = $c;
		return $return;
  }
  
  public static function getGrades($periode) {
    
    return array(
			'periode'=>$periode,
			'classes'=>parseGrades(curl::get($url.'/Portaal/Cijferlijst/Examendossier/Cijferlijst?wis_ajax&ajax_object=727&periode727='.$periode, array(CURLOPT_COOKIE=>self::$cookiestr, CURLOPT_FOLLOWLOCATION=>1, CURLOPT_SSL_VERIFYPEER=>false, CURLOPT_TIMEOUT=>6)))
		);
  }
  
}
?>